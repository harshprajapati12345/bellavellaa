<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WithdrawalRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $professionals = DB::table('professionals')->pluck('id')->toArray();

        if (empty($professionals)) {
            $this->command->info('No professionals found. Run MasterSeeder first.');
            return;
        }

        $statuses = ['pending', 'approved', 'rejected', 'paid'];
        $banks = ['HDFC Bank', 'ICICI Bank', 'SBI', 'Axis Bank', 'Kotak Mahindra'];

        for ($i = 0; $i < min(10, count($professionals)); $i++) {
            $status = $statuses[array_rand($statuses)];
            $method = rand(0, 1) ? 'bank' : 'upi';

            DB::table('withdrawal_requests')->insert([
                'professional_id' => $professionals[$i],
                'amount' => rand(100000, 1000000), // 1000 to 10000 INR in paise
                'method' => $method,
                'status' => $status,
                'account_holder' => $method === 'bank' ? 'Professional ' . ($i + 1) : null,
                'account_number' => $method === 'bank' ? (string) rand(100000000000, 999999999999) : null,
                'ifsc_code' => $method === 'bank' ? 'HDFC000' . rand(1000, 9999) : null,
                'bank_name' => $method === 'bank' ? $banks[array_rand($banks)] : null,
                'upi_id' => $method === 'upi' ? 'professional' . ($i + 1) . '@upi' : null,
                'transaction_reference' => in_array($status, ['approved', 'paid']) ? 'TXN' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT) : null,
                'admin_note' => in_array($status, ['approved', 'rejected']) ? 'Request processed' : null,
                'rejection_reason' => $status === 'rejected' ? 'Insufficient balance' : null,
                'processed_at' => in_array($status, ['approved', 'rejected', 'paid']) ? now()->subDays(rand(1, 30)) : null,
                'request_id' => 'REQ' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Withdrawal Requests seeded');
    }
}