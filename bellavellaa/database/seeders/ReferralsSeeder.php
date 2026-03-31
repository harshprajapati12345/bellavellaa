<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReferralsSeeder extends Seeder
{
    public function run(): void
    {
        // Get some existing professionals and customers
        $professionals = DB::table('professionals')->pluck('id')->toArray();
        $customers = DB::table('customers')->pluck('id')->toArray();

        if (empty($professionals)) {
            $this->command->info('No professionals found. Run MasterSeeder first.');
            return;
        }

        // Get existing referred_ids to avoid duplicates
        $existingReferredIds = DB::table('referrals')->pluck('referred_id')->toArray();

        // Professional-to-professional referrals
        $availableProfessionals = array_diff($professionals, $existingReferredIds);
        $referrerProfessionals = array_diff($professionals, $availableProfessionals);

        $count = min(5, count($availableProfessionals), count($referrerProfessionals));
        for ($i = 0; $i < $count; $i++) {
            $referrerId = $referrerProfessionals[$i];
            $referredId = $availableProfessionals[$i];

            DB::table('referrals')->insert([
                'referrer_id' => $referrerId,
                'referred_id' => $referredId,
                'referred_type' => 'professional',
                'referred_phone' => '98' . rand(10000000, 99999999),
                'status' => ['pending', 'success', 'expired'][$i % 3],
                'reward_amount' => rand(500, 2000),
                'reward_type' => ['cash', 'coin'][rand(0, 1)],
                'referral_code' => strtoupper(Str::random(8)),
                'reward_referrer' => rand(200, 500),
                'reward_user' => rand(100, 300),
                'reward_given_at' => ($i % 3 === 1) ? now()->subDays(rand(1, 30)) : null,
                'reward_coins' => rand(100, 500),
                'trigger_type' => 'first_job',
                'is_rewarded' => (bool) rand(0, 1),
                'completed_at' => ($i % 3 === 1) ? now()->subDays(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now(),
            ]);
        }

        // Professional-to-customer referrals
        if (!empty($customers)) {
            $availableCustomers = array_diff($customers, $existingReferredIds);
            $count = min(5, count($availableCustomers), count($professionals));

            for ($i = 0; $i < $count; $i++) {
                DB::table('referrals')->insert([
                    'referrer_id' => $professionals[$i % count($professionals)],
                    'referred_id' => $availableCustomers[$i],
                    'referred_type' => 'client',
                    'referred_phone' => '98' . rand(10000000, 99999999),
                    'status' => ['pending', 'success', 'expired'][$i % 3],
                    'reward_amount' => rand(200, 1000),
                    'reward_type' => ['cash', 'coin'][rand(0, 1)],
                    'referral_code' => strtoupper(Str::random(8)),
                    'reward_referrer' => rand(100, 300),
                    'reward_user' => rand(50, 150),
                    'reward_given_at' => ($i % 3 === 1) ? now()->subDays(rand(1, 30)) : null,
                    'reward_coins' => rand(50, 200),
                    'trigger_type' => 'first_booking',
                    'is_rewarded' => (bool) rand(0, 1),
                    'completed_at' => ($i % 3 === 1) ? now()->subDays(rand(1, 30)) : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Referrals seeded');
    }
}