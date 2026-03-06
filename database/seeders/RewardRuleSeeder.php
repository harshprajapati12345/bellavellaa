<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RewardRule;

class RewardRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'type' => 'signup',
                'title' => 'Signup Reward',
                'coins' => 50,
                'status' => true,
                'max_per_user' => 1,
            ],
            [
                'type' => 'referrer',
                'title' => 'Referrer Reward',
                'coins' => 100,
                'status' => true,
                'max_per_user' => 0,
            ],
            [
                'type' => 'referred_user',
                'title' => 'Referred User Reward',
                'coins' => 50,
                'status' => true,
                'max_per_user' => 1,
            ],
            [
                'type' => 'login',
                'title' => 'Daily Login Reward',
                'coins' => 5,
                'status' => true,
                'max_per_user' => 0,
            ],
        ];

        foreach ($rules as $rule) {
            RewardRule::updateOrCreate(['type' => $rule['type']], $rule);
        }
    }
}
