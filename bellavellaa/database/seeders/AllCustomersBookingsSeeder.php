<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllCustomersBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $customers = DB::table('customers')->get();
        $services = DB::table('services')->get();
        $professionals = DB::table('professionals')->get();

        if ($services->isEmpty() || $professionals->isEmpty()) {
            $this->command->error('No services or professionals found to create bookings.');
            return;
        }

        $count = 0;
        foreach ($customers as $customer) {
            // Force create one for everyone just to be 100% sure in this debug phase
            $hasBooking = DB::table('bookings')->where('customer_id', $customer->id)->exists();
            
            if (!$hasBooking || $customer->id == 6 || $customer->id == 5) {
                $service = $services->random();
                $professional = $professionals->random();
                $cName = $customer->name ?? 'Guest User';
                $cPhone = str_replace(' ', '', $customer->mobile);
                if (empty($cPhone)) $cPhone = '9800000000';
                
                DB::table('bookings')->insert([
                    'customer_id' => $customer->id,
                    'customer_name' => $cName,
                    'customer_phone' => $cPhone,
                    'city' => 'Mumbai', // Default city if not found
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'professional_id' => $professional->id,
                    'professional_name' => $professional->name,
                    'date' => Carbon::now()->addDays(rand(1, 30))->format('Y-m-d'),
                    'slot' => ['10:00 AM', '12:00 PM', '2:00 PM', '4:00 PM', '6:00 PM'][rand(0, 4)],
                    'status' => 'Pending',
                    'price' => $service->price,
                    'notes' => 'Forced Seeded booking for customer ' . $customer->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }
        
        $this->command->info("✅ Successfully added/forced bookings to {$count} customers.");
    }
}
