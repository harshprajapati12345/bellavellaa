<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RewardRulesSeeder extends Seeder
{
    public function run(): void
    {
        $rewardRules = [
            ['signup', 'Referral Signup Reward', 500, true, 1],
            ['referrer', 'Referral Bonus for Referrer', 200, true, 0],
            ['referred_user', 'Welcome Bonus for New User', 100, true, 1],
            ['review', 'Review Submission Reward', 50, true, 5],
            ['booking', 'First Booking Reward', 25, true, 1],
        ];

        foreach ($rewardRules as $rule) {
            DB::table('reward_rules')->insert([
                'type' => $rule[0],
                'title' => $rule[1],
                'coins' => $rule[2],
                'status' => $rule[3],
                'max_per_user' => $rule[4],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Reward Rules seeded');
    }
}