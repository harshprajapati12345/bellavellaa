<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerAppFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $customers = DB::table('customers')->pluck('id')->toArray();

        if (empty($customers)) {
            $this->command->info('No customers found. Run MasterSeeder first.');
            return;
        }

        $feedbacks = [
            ['Great app!', 'Easy to use', 'Good service', 'Could be better', 'Excellent', 'Fast booking', 'Professional service', 'Value for money'],
            ['Android', 'iOS'],
            ['Samsung S23', 'iPhone 15', 'OnePlus 12', 'Pixel 8', 'Redmi Note 13']
        ];

        for ($i = 0; $i < min(20, count($customers)); $i++) {
            DB::table('customer_app_feedback')->insert([
                'customer_id' => $customers[$i],
                'rating' => rand(3, 5),
                'feedback' => $feedbacks[0][array_rand($feedbacks[0])],
                'app_version' => '2.' . rand(0, 5) . '.' . rand(0, 9),
                'device_info' => json_encode([
                    'os' => $feedbacks[1][array_rand($feedbacks[1])],
                    'model' => $feedbacks[2][array_rand($feedbacks[2])]
                ]),
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Customer App Feedback seeded');
    }
}