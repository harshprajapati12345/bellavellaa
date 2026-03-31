<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Professional;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Mark a job as completed, calculate earnings, update wallets, and trigger referrals.
     *
     * @param Booking $booking
     * @return void
     */
    public static function completeJob(Booking $booking): void
    {
        // Prevent processing already completed jobs
        if ($booking->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'completed',
                'current_step' => 'completed',
            ]);

            // Step 8: Only distribute earnings if the order is SUCCESS
            $order = $booking->order;
            if ($order && $order->payment_status === 'SUCCESS') {
                if ($booking->professional) {
                    self::distributeEarnings($booking, $booking->professional);
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('BookingService::completeJob : Attempted to distribute earnings for unpaid order', [
                    'booking_id' => $booking->id,
                    'order_id' => $order?->id,
                    'payment_status' => $order?->payment_status
                ]);
            }

            if ($booking->customer) {
                ReferralService::processFirstBookingCompletion($booking->customer);
            }
        });
    }

    /**
     * Calculate and distribute earnings to the professional.
     *
     * @param Booking $booking
     * @param Professional $professional
     * @return void
     */
    private static function distributeEarnings(Booking $booking, Professional $professional): void
    {
        // Base commission is 15%
        $commissionRate = 0.15;
        $priceInPaise = (int)round($booking->price * 100);
        $commissionAmountPaise = (int)round($priceInPaise * $commissionRate);
        $netEarningsPaise = $priceInPaise - $commissionAmountPaise;

        // Credit professional's wallet (which stores balance in paise)
        $wallet = Wallet::firstOrCreate(
            [
                'holder_type' => 'professional',
                'holder_id'   => $professional->id,
                'type'        => 'cash',
            ],
            [
                'balance' => 0,
                'version' => 1,
            ]
        );

        $wallet->credit(
            $netEarningsPaise,
            'job_earning',
            "Earnings for booking #{$booking->id}",
            $booking->id,
            Booking::class
        );

        // Update total lifetime earnings and orders count for the professional
        // Assuming $professional->earnings is stored in standard currency (Rupees) 
        // as per DashboardController ("total_earnings" vs "todays_earnings" comparison)
        $netEarningsRupees = $netEarningsPaise / 100;
        $professional->increment('earnings', $netEarningsRupees);
        $professional->increment('orders'); // Legacy
        $professional->increment('total_completed_jobs'); // New system
    }
}
