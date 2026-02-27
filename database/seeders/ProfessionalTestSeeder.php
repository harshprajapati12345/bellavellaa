<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfessionalTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create/Find a Test Professional
        $phonePro = '9000000001';
        $professional = DB::table('professionals')->where('phone', $phonePro)->first();
        
        if (!$professional) {
            $proId = DB::table('professionals')->insertGetId([
                'name' => 'Test Professional',
                'phone' => $phonePro,
                'city' => 'Mumbai',
                'category' => 'Hair Care',
                'status' => 'Active',
                'verification' => 'Verified',
                'joined' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $proId = $professional->id;
        }

        // 2. Create/Find a Test Customer
        $phoneCust = '9000000002';
        $customer = DB::table('customers')->where('mobile', $phoneCust)->first();

        if (!$customer) {
            $custId = DB::table('customers')->insertGetId([
                'name' => 'Test Customer',
                'mobile' => $phoneCust,
                'city' => 'Mumbai',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $custId = $customer->id;
        }


        // 3. Create 3 Pending Bookings for this Professional
        for ($i = 1; $i <= 3; $i++) {
            DB::table('bookings')->insert([
                'customer_id' => $custId,
                'customer_name' => 'Test Customer',
                'customer_phone' => '9000000002',
                'city' => 'Mumbai',
                'service_id' => 1,
                'service_name' => 'Hair Cut & Style',
                'professional_id' => $proId,
                'professional_name' => 'Test Professional',
                'date' => now()->addDays($i),
                'slot' => '10:00 AM',
                'status' => 'Pending',
                'price' => 499,
                'notes' => "Test Booking Request #{$i}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Create a Test OTP Record for direct login testing
        DB::table('otps')->updateOrInsert(
            ['mobile' => '9000000001'],
            [
                'otp' => '123456',
                'verified' => false,
                'expires_at' => now()->addHours(24), // Long expiry for convenience
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        echo "✅ Test Professional (9000000001), 3 Bookings, and OTP (123456) created!\n";

    }
}
