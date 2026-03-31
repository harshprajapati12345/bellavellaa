<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $customers = DB::table('customers')->pluck('id')->toArray();
        $professionals = DB::table('professionals')->pluck('id')->toArray();
        $bookings = DB::table('bookings')->pluck('id')->toArray();

        if (empty($customers) || empty($professionals)) {
            $this->command->info('No customers or professionals found. Run MasterSeeder first.');
            return;
        }

        $reviews = [
            'Professional service', 'Very good experience', 'Satisfied with the work',
            'Average service', 'Outstanding quality', 'Could be better', 'Excellent work',
            'Timely and efficient', 'Highly recommended', 'Good value for money'
        ];

        for ($i = 0; $i < min(30, count($customers), count($professionals)); $i++) {
            DB::table('user_reviews')->insert([
                'booking_id' => !empty($bookings) ? $bookings[$i % count($bookings)] : null,
                'reviewer_id' => $customers[$i % count($customers)],
                'reviewed_id' => $professionals[$i % count($professionals)],
                'reviewer_role' => 'client',
                'reviewed_role' => 'professional',
                'rating' => rand(3, 5),
                'comment' => $reviews[array_rand($reviews)],
                'content_type' => rand(0, 4) === 0 ? 'video' : 'text', // 20% videos
                'video_path' => rand(0, 4) === 0 ? 'videos/review_' . ($i + 1) . '.mp4' : null,
                'status' => ['Pending', 'Approved', 'Rejected'][rand(0, 2)],
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ User Reviews seeded');
    }
}