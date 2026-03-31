<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Referral;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    /**
     * Complete a referral and grant the reward if applicable.
     */
    public static function processFirstBookingCompletion(Customer $customer): void
    {
        // 1. Does the customer have a pending referral record?
        $referral = Referral::where('referred_id', $customer->id)
            ->where('referred_type', 'client')
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return;
        }

        // 2. Is this their first completed booking?
        $completedBookingsCount = $customer->bookingsRel()
            ->where('status', 'Completed')
            ->count();

        if ($completedBookingsCount !== 1) {
            return;
        }

        // 3. Grant reward in a transaction
        DB::transaction(function () use ($referral, $customer) {
            $referrer = $referral->referrer;
            
            // Use firstOrCreate to prevent duplicate entry errors if wallet already exists
            $coinWallet = Wallet::firstOrCreate(
                [
                    'holder_type' => 'customer',
                    'holder_id'   => $referrer->id,
                    'type'        => 'coin',
                ],
                [
                    'balance' => 0,
                    'version' => 1,
                ]
            );

            // Grant bonus
            $rewardAmount = $referral->reward_amount ?: 100;

            $coinWallet->credit(
                $rewardAmount,
                'referral_bonus',
                "Referral bonus for inviting customer: " . ($customer->name ?: $customer->mobile),
                $customer->id,
                Customer::class
            );

            // Update referral status
            $referral->update([
                'status' => 'success',
                'reward_given_at' => now(),
            ]);
        });
    }
}
