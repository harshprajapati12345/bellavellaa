<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerificationRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $professionals = DB::table('professionals')->pluck('id')->toArray();

        if (empty($professionals)) {
            $this->command->info('No professionals found. Run MasterSeeder first.');
            return;
        }

        $types = ['bank', 'upi'];
        $statuses = ['pending', 'approved', 'rejected'];

        for ($i = 0; $i < min(15, count($professionals)); $i++) {
            $status = $statuses[array_rand($statuses)];

            DB::table('verification_requests')->insert([
                'professional_id' => $professionals[$i],
                'type' => $types[$i % count($types)],
                'status' => $status,
                'rejection_reason' => $status === 'rejected' ? 'Documents not clear enough' : null,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Verification Requests seeded');
    }
}