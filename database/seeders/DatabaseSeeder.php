<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterSeeder::class,
            ServiceHierarchySeeder::class,
            HairColourHierarchySeeder::class,
            CategoryBannerSeeder::class,
            HomepageContentSeeder::class,
            HierarchyBannerSeeder::class,
            // New seeders for updated schema
            RewardRulesSeeder::class,
            ReferralsSeeder::class,
            CustomerAppFeedbackSeeder::class,
            UserReviewsSeeder::class,
            VerificationRequestsSeeder::class,
            WithdrawalRequestsSeeder::class,
        ]);
    }
}
