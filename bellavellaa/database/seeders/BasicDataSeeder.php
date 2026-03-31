<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BasicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            ['name' => 'Hair Care', 'slug' => 'hair-care', 'description' => 'Hair cuts, styling, and treatments.'],
            ['name' => 'Skin Care', 'slug' => 'skin-care', 'description' => 'Facials, cleanups, and skin treatments.'],
            ['name' => 'Nail Care', 'slug' => 'nail-care', 'description' => 'Manicures, pedicures, and nail art.'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['slug' => $cat['slug']], array_merge($cat, ['created_at' => now(), 'updated_at' => now()]));
        }

        // 2. Create Services
        $hairCatId = DB::table('categories')->where('slug', 'hair-care')->value('id');
        $skinCatId = DB::table('categories')->where('slug', 'skin-care')->value('id');

        $services = [
            ['name' => 'Hair Cut & Style', 'slug' => 'hair-cut-style', 'category_id' => $hairCatId, 'price' => 499, 'duration' => 45],
            ['name' => 'Hair Coloring', 'slug' => 'hair-coloring', 'category_id' => $hairCatId, 'price' => 1499, 'duration' => 120],
            ['name' => 'Fruit Facial', 'slug' => 'fruit-facial', 'category_id' => $skinCatId, 'price' => 799, 'duration' => 60],
            ['name' => 'Gold Facial', 'slug' => 'gold-facial', 'category_id' => $skinCatId, 'price' => 1299, 'duration' => 90],
        ];

        foreach ($services as $service) {
            DB::table('services')->updateOrInsert(['slug' => $service['slug']], array_merge($service, ['created_at' => now(), 'updated_at' => now()]));
        }

        // 3. Create a Test Professional
        $proPhone = '9876543210';
        $professional = DB::table('professionals')->where('phone', $proPhone)->first();
        if (!$professional) {
            $proId = DB::table('professionals')->insertGetId([
                'name' => 'John Doe',
                'phone' => $proPhone,
                'email' => 'john.doe@example.com',
                'city' => 'Mumbai',
                'status' => 'Active', // Corrected to Title Case
                'verification' => 'Verified',
                'joined' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $proId = $professional->id;
        }

        // 4. Create a Test Customer
        $custMobile = '9123456780';
        $customer = DB::table('customers')->where('mobile', $custMobile)->first();
        if (!$customer) {
            $custId = DB::table('customers')->insertGetId([
                'name' => 'Jane Smith',
                'mobile' => $custMobile,
                'email' => 'jane.smith@example.com',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $custId = $customer->id;
        }

        // 5. Create some Bookings
        $service1 = DB::table('services')->first();
        
        DB::table('bookings')->insert([
            'customer_id' => $custId,
            'customer_name' => 'Jane Smith',
            'customer_phone' => $custMobile,
            'service_id' => $service1->id,
            'service_name' => $service1->name,
            'professional_id' => $proId,
            'professional_name' => 'John Doe',
            'date' => now()->format('Y-m-d'),
            'slot' => '10:00 AM',
            'status' => 'pending', // Corrected to lowercase
            'price' => $service1->price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✅ BasicDataSeeder: Categories, Services, Professionals, Customers, and Bookings created successfully!\n";
    }
}
