<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\BookingService;

class PaymentService
{
    /**
     * Mark a payment as successful with strict state guarding.
     * Prevents: FAILED -> SUCCESS, Double-Dipping (SUCCESS after Refund), and Race Conditions.
     */
    public static function processCapture(string $razorpayOrderId, string $razorpayPaymentId, array $gatewayData = []): bool
    {
        return DB::transaction(function () use ($razorpayOrderId, $razorpayPaymentId, $gatewayData) {
            $payment = Payment::where('gateway_order_id', $razorpayOrderId)->lockForUpdate()->first();

            if (!$payment) {
                Log::error('Payment record not found for capture', ['gateway_order_id' => $razorpayOrderId]);
                return false;
            }

            // ⛔ GUARD 1: Idempotency (Already SUCCESS)
            if ($payment->status === 'SUCCESS') {
                return true; 
            }

            // ⛔ GUARD 2: Prevent FAILED -> SUCCESS (Late Webhook)
            if ($payment->status === 'FAILED') {
                Log::warning('Blocked SUCCESS transition for an already FAILED payment', ['payment_id' => $payment->id]);
                return false;
            }

            // ⛔ GUARD 3: Prevent SUCCESS after REFUND (Crucial for Wallet integrity)
            if (data_get($payment->meta_json, 'refunded', false)) {
                Log::critical('SEC-RISK: Attempted SUCCESS on a REFUNDED payment!', ['payment_id' => $payment->id]);
                return false;
            }

            // ✅ ATOMIC UPDATE
            $payment->update([
                'status' => 'SUCCESS',
                'gateway_payment_id' => $razorpayPaymentId,
                'paid_at' => now(),
                'meta_json' => array_merge($payment->meta_json ?? [], ['gateway_capture_raw' => $gatewayData])
            ]);

            $order = $payment->order;
            if ($order) {
                $order->update([
                    'payment_status' => 'SUCCESS',
                    'status' => 'confirmed'
                ]);

                if ($order->customer) {
                    $order->customer->carts()->delete();
                }

                // Finalize any bookings associated
                foreach ($order->bookings as $booking) {
                    BookingService::completeJob($booking);
                }
            }

            return true;
        });
    }

    /**
     * Mark a payment as failed with atomic refund and idempotency.
     */
    public static function processFailure(string $razorpayOrderId, array $errorData = []): bool
    {
        return DB::transaction(function () use ($razorpayOrderId, $errorData) {
            $payment = Payment::where('gateway_order_id', $razorpayOrderId)->lockForUpdate()->first();

            if (!$payment || $payment->status !== 'PENDING') {
                return false; // Only PENDING payments can fail
            }

            // 💰 ATOMIC REFUND
            $coinsUsed = data_get($payment->meta_json, 'coins_used', 0);
            $alreadyRefunded = data_get($payment->meta_json, 'refunded', false);

            if ($coinsUsed > 0 && !$alreadyRefunded) {
                $order = $payment->order;
                if ($order && $order->customer) {
                    $order->customer->coinWallet?->credit($coinsUsed, 'refund', "Order #{$order->order_number} failure refund");
                    
                    $meta = $payment->meta_json ?? [];
                    $meta['refunded'] = true;
                    $payment->meta_json = $meta;
                    $payment->save();
                }
            }

            $payment->update([
                'status' => 'FAILED',
                'meta_json' => array_merge($payment->meta_json ?? [], ['error' => $errorData])
            ]);

            if ($payment->order) {
                $payment->order->update(['payment_status' => 'FAILED']);
            }

            return true;
        });
    }
}
