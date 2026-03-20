<?php

namespace App\Services;

use App\Models\RewardRule;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Referral;
use Illuminate\Support\Facades\DB;

class RewardService
{
    /**
     * Award coins for a new signup.
     */
    public function awardSignupReward($user, $userType)
    {
        $rule = RewardRule::where('type', 'signup')->where('status', true)->first();
        if (!$rule || $rule->coins <= 0)
            return 0;

        return DB::transaction(function () use ($user, $userType, $rule) {
            // Prevent duplicate signup rewards
            $exists = WalletTransaction::whereHas('wallet', function ($q) use ($user, $userType) {
                    $q->where('holder_id', $user->id)->where('holder_type', $userType);
                }
                )->where('description', 'Signup Bonus')->exists();

                if ($exists)
                    return 0;

                $wallet = $this->getOrCreateCoinWallet($user, $userType);
                $wallet->credit($rule->coins, 'signup_reward', 'Signup Bonus');
                return $rule->coins;
            });
    }

    /**
     * Award coins for a successful referral (both referrer and referred user).
     */
    public function awardReferralRewards($new_user, $userType, $referrer, $referrerType, $referralCode)
    {
        $referrerRule = RewardRule::where('type', 'referrer')->where('status', true)->first();
        $referredUserRule = RewardRule::where('type', 'referred_user')->where('status', true)->first();

        return DB::transaction(function () use ($new_user, $userType, $referrer, $referrerType, $referralCode, $referrerRule, $referredUserRule) {
            $awarded = 0;

            // 1. Reward the Referrer
            if ($referrerRule && $referrerRule->coins > 0) {
                // Fraud check: Max referrals per user
                $referralCount = Referral::where('referrer_id', $referrer->id)
                    ->where('referrer_type', $referrerType)
                    ->where('status', 'success')
                    ->count();

                if ($referrerRule->max_per_user == 0 || $referralCount < $referrerRule->max_per_user) {
                    $referrerWallet = $this->getOrCreateCoinWallet($referrer, $referrerType);
                    $phone = $new_user->mobile ?? $new_user->phone;
                    $referrerWallet->credit($referrerRule->coins, 'referral_reward', "Referral Reward for inviting {$new_user->name} ($phone)");
                    $awarded += $referrerRule->coins;
                }
            }

            // 2. Reward the Referred User
            if ($referredUserRule && $referredUserRule->coins > 0) {
                $userWallet = $this->getOrCreateCoinWallet($new_user, $userType);
                $userWallet->credit($referredUserRule->coins, 'referral_signup', "Referral Bonus for using code {$referralCode}");
                $awarded += $referredUserRule->coins;
            }

            // 3. Complete the Referral Record (Update existing or create new)
            Referral::updateOrCreate(
            [
                'referred_id' => $new_user->id,
                'referred_type' => $userType,
            ],
            [
                'referrer_id' => $referrer->id,
                'referrer_type' => $referrerType,
                'referral_code' => $referralCode,
                'referred_phone' => $new_user->mobile ?? $new_user->phone,
                'status' => 'success',
                'reward_amount' => $referrerRule->coins ?? 0,
                'reward_referrer' => $referrerRule->coins ?? 0,
                'reward_user' => $referredUserRule->coins ?? 0,
            ]
            );

            return $awarded;
        });
    }

    /**
     * Award coins for a daily login.
     */
    public function awardLoginReward($user, $userType)
    {
        $rule = RewardRule::where('type', 'login')->where('status', true)->first();
        if (!$rule || $rule->coins <= 0)
            return 0;

        return DB::transaction(function () use ($user, $userType, $rule) {
            // Check if already awarded today
            $exists = WalletTransaction::whereHas('wallet', function ($q) use ($user, $userType) {
                    $q->where('holder_id', $user->id)->where('holder_type', $userType);
                }
                )->where('description', 'like', 'Daily Login Bonus%')
                    ->whereDate('created_at', now()->toDateString())
                    ->exists();

                if ($exists)
                    return 0;

                $wallet = $this->getOrCreateCoinWallet($user, $userType);
                $wallet->credit($rule->coins, 'login_reward', 'Daily Login Bonus');
                return $rule->coins;
            });
    }

    /**
     * Credit coins to a user's balance and log the transaction.
     */
    public function creditCoins($userId, $coins, $referenceId, $source = 'referral_bonus')
    {
        DB::table('professionals')
            ->where('id', $userId)
            ->lockForUpdate()
            ->increment('coins_balance', $coins);

        DB::table('coin_transactions')->insert([
            'user_id' => $userId,
            'coins' => $coins,
            'type' => 'credit',
            'source' => $source,
            'reference_id' => $referenceId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Award coins for completing 5 premium jobs (price > 1000) in a week.
     */
    public function rewardWeeklyPremiumJobs($professional)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $weekKey = $startOfWeek->format('Y-W');

        // Check if already rewarded this week
        $exists = DB::table('coin_transactions')
            ->where('user_id', $professional->id)
            ->where('source', 'weekly_premium')
            ->where('reference_id', $weekKey)
            ->exists();

        if ($exists)
            return;

        $count = DB::table('bookings')
            ->where('professional_id', $professional->id)
            ->where('status', 'completed')
            ->where('price', '>', 1000)
            ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
            ->count();

        if ($count >= 5) {
            $rule = RewardRule::where('type', 'weekly_premium')->where('status', true)->first();
            $coins = $rule ? $rule->coins : 200;
            $this->creditCoins($professional->id, $coins, $weekKey, 'weekly_premium');
        }
    }

    /**
     * Award coins for profile verification.
     */
    public function rewardProfileVerification($professional)
    {
        $exists = DB::table('coin_transactions')
            ->where('user_id', $professional->id)
            ->where('source', 'profile_verification')
            ->exists();

        if ($exists)
            return;

        $rule = RewardRule::where('type', 'verification')->where('status', true)->first();
        $coins = $rule ? $rule->coins : 100;
        $this->creditCoins($professional->id, $coins, 'verified', 'profile_verification');
    }

    /**
     * Award coins for on-time job completion.
     */
    public function rewardOnTimeCompletion($professional, $booking)
    {
        $rule = RewardRule::where('type', 'on_time')->where('status', true)->first();
        $coins = $rule ? $rule->coins : 50;
        $this->creditCoins($professional->id, $coins, $booking->id, 'on_time_completion');
    }

    /**
     * Record a pending referral for professional-to-professional referrals.
     */
    public function createPendingReferral($newUser, $referrer, $referralCode)
    {
        if (!$referrer || $newUser->id == $referrer->id)
            return; // ❌ block self referral

        $referrerRule = RewardRule::where('type', 'referrer')->where('status', true)->first();
        $coins = $referrerRule ? $referrerRule->coins : 500;

        DB::table('referrals')->insert([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'referral_code' => $referralCode,
            'status' => 'pending',
            'reward_coins' => $coins,
            'trigger_type' => 'first_job',
            'is_rewarded' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Atomic process to disburse referral reward on first job completion.
     */
    public function processFirstJobReferralReward($professional)
    {
        // 🛡 Double Safety: only reward on the very first job
        if ($professional->total_completed_jobs > 1) {
            return;
        }

        DB::beginTransaction();

        try {
            // 🔒 Lock referral row to prevent race conditions
            $referral = DB::table('referrals')
                ->where('referred_id', $professional->id)
                ->where('status', 'pending')
                ->where('is_rewarded', 0)
                ->lockForUpdate()
                ->first();

            if (!$referral || !is_object($referral)) {
                DB::commit();
                return;
            }

            $coins = $referral->reward_coins;

            // 🎯 Credit referrer
            $this->creditCoins($referral->referrer_id, $coins, $referral->id);

            // ✅ Mark as rewarded and completed
            DB::table('referrals')
                ->where('id', $referral->id)
                ->update([
                'status' => 'success', // Using 'success' for compatibility with existing system
                'is_rewarded' => 1,
                'completed_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Helper to get or create a coin wallet for any user/holder type (Legacy system compatibility).
     */
    private function getOrCreateCoinWallet($holder, $holderType)
    {
        return Wallet::firstOrCreate(
        [
            'holder_id' => $holder->id,
            'holder_type' => $holderType,
            'type' => 'coin',
        ],
        [
            'balance' => 0,
            'version' => 1,
        ]
        );
    }
}
