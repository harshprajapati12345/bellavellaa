<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionalOnlineStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pros = [
            ['name' => 'Kavita Singh', 'is_online' => true, 'last_seen' => now()], // Online
            ['name' => 'Amit Sharma', 'is_online' => true, 'last_seen' => now()->subMinutes(1)], // Online
            ['name' => 'Priya Verma', 'is_online' => true, 'last_seen' => now()->subMinutes(5)], // Offline (5 mins ago)
            ['name' => 'Rahul Gupta', 'is_online' => false, 'last_seen' => now()->subHours(1)], // Offline
            ['name' => 'Sonia Das', 'is_online' => true, 'last_seen' => null], // Offline (Never seen)
        ];

        foreach ($pros as $pro) {
            \App\Models\Professional::updateOrCreate(
                ['phone' => '9' . rand(0,9) . rand(10000000, 99999999)],
                array_merge($pro, [
                    'email' => strtolower(str_replace(' ', '.', $pro['name'])) . '@example.com',
                    'status' => 'Active',
                    'verification' => 'Verified',
                    'category' => 'Prime',
                    'experience' => '5 years',
                ])
            );
        }
    }
}
