<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate tables to allow re-seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('customers')->truncate();
        DB::table('admins')->truncate();
        DB::table('otps')->truncate();
        DB::table('addresses')->truncate();
        DB::table('categories')->truncate();
        DB::table('services')->truncate();
        DB::table('packages')->truncate();
        DB::table('professionals')->truncate();
        DB::table('bookings')->truncate();
        DB::table('reviews')->truncate();
        DB::table('media')->truncate();
        DB::table('offers')->truncate();
        DB::table('homepage_contents')->truncate();
        DB::table('kit_products')->truncate();
        DB::table('kit_orders')->truncate();
        DB::table('leave_requests')->truncate();
        DB::table('settings')->truncate();
        DB::table('orders')->truncate();
        DB::table('order_items')->truncate();
        DB::table('order_status_history')->truncate();
        DB::table('order_assignments')->truncate();
        DB::table('order_otps')->truncate();
        DB::table('professional_location_logs')->truncate();
        DB::table('payments')->truncate();
        DB::table('refunds')->truncate();
        DB::table('wallets')->truncate();
        DB::table('wallet_transactions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('role_permission')->truncate();
        DB::table('admin_profiles')->truncate();
        DB::table('customer_notifications')->truncate();
        DB::table('professional_notifications')->truncate();
        DB::table('admin_notifications')->truncate();
        DB::table('promotions')->truncate();
        DB::table('promotion_usages')->truncate();
        DB::table('service_options')->truncate();
        DB::table('service_variants')->truncate();
        DB::table('tags')->truncate();
        DB::table('service_tag')->truncate();
        DB::table('kit_categories')->truncate();
        DB::table('kit_types')->truncate();
        DB::table('kit_units')->truncate();
        DB::table('professional_kit_units')->truncate();
        DB::table('kit_shipments')->truncate();
        DB::table('order_kit_scans')->truncate();
        DB::table('professional_documents')->truncate();
        DB::table('professional_service_areas')->truncate();
        DB::table('professional_devices')->truncate();
        DB::table('professional_online_sessions')->truncate();
        DB::table('membership_plans')->truncate();
        DB::table('customer_memberships')->truncate();
        DB::table('performance_targets')->truncate();
        DB::table('professional_target_assignments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ══════════════════════════════════════════════════════════════
        // 1. CUSTOMERS (mobile + OTP auth)
        // ══════════════════════════════════════════════════════════════
        $customers = [];
        $custNames = ['Priya Sharma', 'Rahul Verma', 'Sneha Patel', 'Amit Kumar', 'Neha Gupta'];
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Pune', 'Hyderabad'];
        foreach ($custNames as $i => $name) {
            $customerId = DB::table('customers')->insertGetId([
                'name' => $name,
                'mobile' => '98' . rand(10000000, 99999999),
                'city' => $cities[$i],
                'zip' => (string) rand(100000, 999999),
                'address' => ($i + 1) . ', MG Road, ' . $cities[$i],
                'status' => 'Active',
                'bookings' => rand(0, 20),
                'joined' => now()->subDays(rand(30, 365)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $customers[] = $customerId;

            // Seed Addresses for each customer
            DB::table('addresses')->insert([
                [
                    'customer_id' => $customerId,
                    'label' => 'Home',
                    'address' => 'Plot no.' . (200 + $i) . ', Kavuri Hills, Madhapur',
                    'city' => 'Hyderabad',
                    'zip' => '500033',
                    'latitude' => 17.4399,
                    'longitude' => 78.3985,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'customer_id' => $customerId,
                    'label' => 'Work',
                    'address' => 'Cyber Towers, HITEC City',
                    'city' => 'Hyderabad',
                    'zip' => '500081',
                    'latitude' => 17.4504,
                    'longitude' => 78.3808,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
        $this->command->info('✅ Customers seeded');

        // ══════════════════════════════════════════════════════════════
        // 2. ADMINS (email + password)
        // ══════════════════════════════════════════════════════════════
        $admins = [];
        $adminData = [
            ['Harsh Admin', 'admin@bellavella.com', 'super_admin'],
            ['Ravi Manager', 'ravi@bellavella.com', 'admin'],
            ['Sita Support', 'sita@bellavella.com', 'manager'],
            ['Mohan Viewer', 'mohan@bellavella.com', 'support'],
            ['Geeta Analyst', 'geeta@bellavella.com', 'viewer'],
        ];
        foreach ($adminData as $i => $a) {
            $admins[] = DB::table('admins')->insertGetId([
                'name' => $a[0],
                'email' => $a[1],
                'password' => Hash::make('password'),
                'phone' => '99' . rand(10000000, 99999999),
                'role' => $a[2],
                'is_active' => true,
                'last_login_at' => now()->subHours(rand(1, 48)),
                'last_login_ip' => '192.168.1.' . rand(1, 254),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Admins seeded');

        // ══════════════════════════════════════════════════════════════
        // 3. OTPs (sample verification records)
        // ══════════════════════════════════════════════════════════════
        for ($i = 0; $i < 5; $i++) {
            DB::table('otps')->insert([
                'mobile' => '98' . rand(10000000, 99999999),
                'otp' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'purpose' => 'login',
                'verified' => $i < 3,
                'expires_at' => now()->addMinutes(5),
                'verified_at' => $i < 3 ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ OTPs seeded');

        // ══════════════════════════════════════════════════════════════
        // 4. CATEGORIES
        // ══════════════════════════════════════════════════════════════
        $catNames = ['Hair Care', 'Skin Care', 'Nail Art', 'Makeup', 'Spa & Wellness'];
        $catColors = ['#FF6B6B', '#4ECDC4', '#FF9FF3', '#F368E0', '#54A0FF'];
        $catIds = [];
        foreach ($catNames as $i => $cat) {
            $catIds[] = DB::table('categories')->insertGetId([
                'name' => $cat,
                'slug' => Str::slug($cat),
                'services_count' => rand(3, 15),
                'bookings_count' => rand(10, 200),
                'status' => 'Active',
                'featured' => $i < 3,
                'color' => $catColors[$i],
                'description' => "Professional {$cat} services.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Categories seeded');

        // ══════════════════════════════════════════════════════════════
        // 5. SERVICES
        // ══════════════════════════════════════════════════════════════
        $serviceData = [
            ['Hair Cut & Style', 0, 45, 499],
            ['Classic Facial', 1, 60, 899],
            ['Gel Nail Extension', 2, 90, 1499],
            ['Bridal Makeup', 3, 120, 4999],
            ['Full Body Massage', 4, 75, 1999],
        ];
        $serviceIds = [];
        foreach ($serviceData as $s) {
            $serviceIds[] = DB::table('services')->insertGetId([
                'name' => $s[0],
                'category' => $catNames[$s[1]],
                'category_id' => $catIds[$s[1]],
                'duration' => $s[2],
                'price' => $s[3],
                'status' => 'Active',
                'featured' => true,
                'bookings' => rand(5, 100),
                'description' => "Premium {$s[0]} service.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Services seeded');

        // ══════════════════════════════════════════════════════════════
        // 6. PACKAGES
        // ══════════════════════════════════════════════════════════════
        $pkgData = [
            ['Bridal Glow Package', 'Makeup', 2999, 20, 180],
            ['Complete Hair Makeover', 'Hair Care', 1499, 15, 90],
            ['Weekend Spa Retreat', 'Spa & Wellness', 3999, 25, 150],
            ['Party Ready Package', 'Makeup', 1999, 10, 120],
            ['Nail Art Combo', 'Nail Art', 999, 12, 60],
        ];
        $pkgIds = [];
        foreach ($pkgData as $p) {
            $pkgIds[] = DB::table('packages')->insertGetId([
                'name' => $p[0],
                'category' => $p[1],
                'services' => json_encode(array_slice($serviceIds, 0, rand(2, 4))),
                'price' => $p[2],
                'discount' => $p[3],
                'duration' => $p[4],
                'bookings' => rand(5, 50),
                'status' => 'Active',
                'featured' => true,
                'description' => "All-inclusive {$p[0]}.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Packages seeded');

        // ══════════════════════════════════════════════════════════════
        // 7. PROFESSIONALS
        // ══════════════════════════════════════════════════════════════
        $proNames = ['Anjali Mehta', 'Kavita Singh', 'Pooja Reddy', 'Ritu Jain', 'Deepika Nair'];
        $proIds = [];
        foreach ($proNames as $i => $name) {
            $proIds[] = DB::table('professionals')->insertGetId([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@bellavella.com',
                'phone' => '97' . rand(10000000, 99999999),
                'city' => $cities[$i],
                'category' => $catNames[$i],
                'bio' => "Expert in {$catNames[$i]}.",
                'status' => 'Active',
                'verification' => $i < 3 ? 'Verified' : 'Pending',
                'orders' => rand(10, 200),
                'earnings' => rand(10000, 150000),
                'commission' => rand(10, 25),
                'experience' => rand(1, 10) . ' years',
                'joined' => now()->subDays(rand(60, 500)),
                'services' => json_encode(array_slice($serviceIds, 0, rand(2, 5))),
                'docs' => $i < 3,
                'rating' => round(rand(35, 50) / 10, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Professionals seeded');

        // ══════════════════════════════════════════════════════════════
        // 8. BOOKINGS (customer_id instead of user_id)
        // ══════════════════════════════════════════════════════════════
        $statuses = ['Pending', 'Confirmed', 'Assigned', 'In Progress', 'Completed'];
        $bookingIds = [];
        for ($i = 0; $i < 5; $i++) {
            $bookingIds[] = DB::table('bookings')->insertGetId([
                'customer_id' => $customers[$i],
                'customer_name' => $custNames[$i],
                'customer_phone' => '98' . rand(10000000, 99999999),
                'city' => $cities[$i],
                'service_id' => $serviceIds[$i],
                'service_name' => $serviceData[$i][0],
                'professional_id' => $proIds[$i],
                'professional_name' => $proNames[$i],
                'date' => now()->addDays(rand(1, 30)),
                'slot' => ['10:00 AM', '12:00 PM', '2:00 PM', '4:00 PM', '6:00 PM'][$i],
                'status' => $statuses[$i],
                'price' => $serviceData[$i][3],
                'notes' => 'Sample booking #' . ($i + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Bookings seeded');

        // ══════════════════════════════════════════════════════════════
        // 9. REVIEWS (customer_id)
        // ══════════════════════════════════════════════════════════════
        for ($i = 0; $i < 5; $i++) {
            DB::table('reviews')->insert([
                'customer_id' => $customers[$i],
                'booking_id' => $bookingIds[$i],
                'rating' => rand(3, 5),
                'comment' => ['Amazing service!', 'Very professional', 'Good experience', 'Could be better', 'Excellent work'][$i],
                'status' => ['Approved', 'Approved', 'Pending', 'Approved', 'Rejected'][$i],
                'customer_name' => $custNames[$i],
                'review_type' => $i === 4 ? 'video' : 'text',
                'points_given' => rand(5, 20),
                'is_featured' => $i < 2,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Reviews seeded');

        // ══════════════════════════════════════════════════════════════
        // 10. MEDIA
        // ══════════════════════════════════════════════════════════════
        for ($i = 1; $i <= 5; $i++) {
            DB::table('media')->insert([
                'type' => $i <= 3 ? 'banner' : 'video',
                'title' => $i <= 3 ? "Summer Sale Banner {$i}" : "Tutorial Video {$i}",
                'url' => "https://placehold.co/1200x400?text=Banner+{$i}",
                'thumbnail' => "https://placehold.co/300x100?text=Thumb+{$i}",
                'linked_section' => ['services', 'packages', 'offers', 'services', 'packages'][$i - 1],
                'status' => 'Active',
                'order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Media seeded');

        // ══════════════════════════════════════════════════════════════
        // 11. OFFERS
        // ══════════════════════════════════════════════════════════════
        $offerNames = ['Summer Glow', 'First Timer', 'Weekend Deal', 'Festive Special', 'Loyalty Reward'];
        for ($i = 0; $i < 5; $i++) {
            DB::table('offers')->insert([
                'name' => $offerNames[$i],
                'code' => strtoupper(str_replace(' ', '', $offerNames[$i])) . rand(10, 99),
                'discount_type' => $i % 2 === 0 ? 'percentage' : 'fixed',
                'discount_value' => $i % 2 === 0 ? rand(10, 30) : rand(100, 500),
                'valid_from' => now()->subDays(5),
                'valid_until' => now()->addDays(30),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Offers seeded');

        // ══════════════════════════════════════════════════════════════
        // 12. HOMEPAGE CONTENTS
        // ══════════════════════════════════════════════════════════════
        $sections = ['hero_banner', 'featured_services', 'trending_packages', 'testimonials', 'download_app'];
        $sectionTitles = ['Hero Banner', 'Featured Services', 'Trending Packages', 'Testimonials', 'Download App'];
        for ($i = 0; $i < 5; $i++) {
            $content = ['heading' => $sectionTitles[$i], 'items' => []];

            if ($sections[$i] === 'hero_banner') {
                $content['items'] = [
                    [
                        'title' => 'Perfect Combo',
                        'subtitle' => 'Haircut & Makeup - ₹1500',
                        'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?q=80&w=800',
                    ],
                    [
                        'title' => 'New Season Sale',
                        'subtitle' => 'Flat 30% Off on Facials',
                        'image' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?q=80&w=800',
                    ],
                    [
                        'title' => 'Bridal Special',
                        'subtitle' => 'Book now for Exclusive Glow',
                        'image' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?q=80&w=800',
                    ],
                ];
            }

            DB::table('homepage_contents')->insert([
                'section' => $sections[$i],
                'content' => json_encode($content),
                'title' => $sectionTitles[$i],
                'status' => 'Active',
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Homepage Contents seeded');

        // ══════════════════════════════════════════════════════════════
        // 13. KIT PRODUCTS
        // ══════════════════════════════════════════════════════════════
        $kitNames = ['Wax Strips Pack', 'Hair Color Kit', 'Facial Kit Pro', 'Nail Polish Set', 'Massage Oil Pack'];
        $kitIds = [];
        foreach ($kitNames as $i => $kit) {
            $kitIds[] = DB::table('kit_products')->insertGetId([
                'sku' => 'KIT-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name' => $kit,
                'brand' => ['VLCC', 'L\'Oreal', 'Biotique', 'OPI', 'Forest Essentials'][$i],
                'category' => $catNames[$i],
                'unit' => ['pack', 'kit', 'set', 'set', 'bottle'][$i],
                'price' => rand(200, 1500),
                'total_stock' => rand(50, 200),
                'min_stock' => 10,
                'expiry_date' => now()->addMonths(rand(6, 18)),
                'status' => 'Active',
                'last_restocked' => now()->subDays(rand(1, 30)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Kit Products seeded');

        // ══════════════════════════════════════════════════════════════
        // 14. KIT ORDERS
        // ══════════════════════════════════════════════════════════════
        for ($i = 0; $i < 5; $i++) {
            DB::table('kit_orders')->insert([
                'professional_id' => $proIds[$i],
                'kit_product_id' => $kitIds[$i],
                'quantity' => rand(1, 5),
                'used_quantity' => rand(0, 3),
                'status' => ['Assigned', 'Assigned', 'Returned', 'Assigned', 'Lost'][$i],
                'assigned_at' => now()->subDays(rand(1, 15)),
                'notes' => 'Kit assignment #' . ($i + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Kit Orders seeded');

        // ══════════════════════════════════════════════════════════════
        // 15. LEAVE REQUESTS (approved_by → admin_id)
        // ══════════════════════════════════════════════════════════════
        $leaveTypes = ['Sick Leave', 'Personal', 'Festival', 'Emergency', 'Vacation'];
        for ($i = 0; $i < 5; $i++) {
            DB::table('leave_requests')->insert([
                'professional_id' => $proIds[$i],
                'leave_type' => $leaveTypes[$i],
                'start_date' => now()->addDays(rand(1, 10)),
                'end_date' => now()->addDays(rand(11, 15)),
                'reason' => "Need {$leaveTypes[$i]} for personal reasons",
                'status' => ['Pending', 'Approved', 'Pending', 'Rejected', 'Pending'][$i],
                'approved_by' => $i === 1 ? $admins[0] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Leave Requests seeded');

        // ══════════════════════════════════════════════════════════════
        // 16. SETTINGS
        // ══════════════════════════════════════════════════════════════
        $settings = [
            ['site_name', 'Bellavella', 'general'],
            ['commission_rate', '15', 'finance'],
            ['min_order_amount', '299', 'orders'],
            ['otp_expiry_minutes', '5', 'auth'],
            ['support_phone', '+91-9876543210', 'general'],
        ];
        foreach ($settings as $s) {
            DB::table('settings')->insert([
                'key' => $s[0],
                'value' => $s[1],
                'group' => $s[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Settings seeded');

        // ══════════════════════════════════════════════════════════════
        // 17. ORDERS (customer_id)
        // ══════════════════════════════════════════════════════════════
        $orderIds = [];
        $orderStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        for ($i = 0; $i < 5; $i++) {
            $subtotal = rand(49900, 499900);
            $discount = rand(0, (int) ($subtotal * 0.2));
            $tax = (int) (($subtotal - $discount) * 0.18);
            $total = $subtotal - $discount + $tax;
            $orderIds[] = DB::table('orders')->insertGetId([
                'order_number' => 'BV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'customer_id' => $customers[$i],
                'professional_id' => $proIds[$i],
                'address' => ($i + 1) . ', MG Road, ' . $cities[$i],
                'city' => $cities[$i],
                'zip' => (string) rand(100000, 999999),
                'latitude' => 19.0760 + ($i * 0.5),
                'longitude' => 72.8777 + ($i * 0.3),
                'scheduled_date' => now()->addDays(rand(1, 14)),
                'scheduled_slot' => ['10:00-11:00', '11:00-12:00', '14:00-15:00', '16:00-17:00', '18:00-19:00'][$i],
                'subtotal_paise' => $subtotal,
                'discount_paise' => $discount,
                'tax_paise' => $tax,
                'total_paise' => $total,
                'coins_used' => rand(0, 50),
                'status' => $orderStatuses[$i],
                'payment_status' => $i < 4 ? 'captured' : 'pending',
                'payment_method' => ['online', 'online', 'cod', 'online', 'cod'][$i],
                'customer_notes' => 'Please be on time',
                'completed_at' => $i === 3 ? now()->subHours(2) : null,
                'cancelled_at' => $i === 4 ? now()->subHours(1) : null,
                'cancel_reason' => $i === 4 ? 'Customer changed plans' : null,
                'created_at' => now()->subDays(rand(1, 7)),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Orders seeded');

        // ══════════════════════════════════════════════════════════════
        // 18. ORDER ITEMS
        // ══════════════════════════════════════════════════════════════
        foreach ($orderIds as $i => $oid) {
            $unitPrice = rand(29900, 149900);
            DB::table('order_items')->insert([
                'order_id' => $oid,
                'item_type' => $i % 2 === 0 ? 'service' : 'package',
                'item_id' => $i % 2 === 0 ? $serviceIds[$i] : $pkgIds[$i],
                'item_name' => $i % 2 === 0 ? $serviceData[$i][0] : $pkgData[$i][0],
                'quantity' => 1,
                'unit_price_paise' => $unitPrice,
                'total_price_paise' => $unitPrice,
                'duration_minutes' => rand(30, 120),
                'meta' => json_encode(['notes' => 'Standard']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Order Items seeded');

        // ══════════════════════════════════════════════════════════════
        // 19. ORDER STATUS HISTORY
        // ══════════════════════════════════════════════════════════════
        foreach ($orderIds as $i => $oid) {
            DB::table('order_status_history')->insert([
                'order_id' => $oid,
                'from_status' => null,
                'to_status' => 'pending',
                'changed_by_type' => 'system',
                'notes' => 'Order placed',
                'created_at' => now()->subDays(rand(1, 5)),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Order Status History seeded');

        // ══════════════════════════════════════════════════════════════
        // 20-21. ORDER ASSIGNMENTS + OTPS
        // ══════════════════════════════════════════════════════════════
        foreach ($orderIds as $i => $oid) {
            DB::table('order_assignments')->insert([
                'order_id' => $oid,
                'professional_id' => $proIds[$i],
                'status' => ['pending', 'accepted', 'accepted', 'accepted', 'rejected'][$i],
                'assigned_at' => now()->subDays(rand(1, 3)),
                'responded_at' => $i > 0 ? now()->subDays(rand(0, 1)) : null,
                'rejection_reason' => $i === 4 ? 'Not available' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('order_otps')->insert([
                'order_id' => $oid,
                'otp' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'type' => $i < 3 ? 'start' : 'complete',
                'verified' => $i === 3,
                'expires_at' => now()->addMinutes(10),
                'verified_at' => $i === 3 ? now()->subHours(2) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Order Assignments + OTPs seeded');

        // ══════════════════════════════════════════════════════════════
        // 22. LOCATION LOGS
        // ══════════════════════════════════════════════════════════════
        foreach ($proIds as $i => $pid) {
            DB::table('professional_location_logs')->insert([
                'professional_id' => $pid,
                'order_id' => $orderIds[$i],
                'latitude' => 19.0760 + (rand(-100, 100) / 1000),
                'longitude' => 72.8777 + (rand(-100, 100) / 1000),
                'logged_at' => now()->subMinutes(rand(5, 60)),
            ]);
        }
        $this->command->info('✅ Location Logs seeded');

        // ══════════════════════════════════════════════════════════════
        // 23. PAYMENTS (customer_id)
        // ══════════════════════════════════════════════════════════════
        $paymentIds = [];
        foreach ($orderIds as $i => $oid) {
            $paymentIds[] = DB::table('payments')->insertGetId([
                'order_id' => $oid,
                'customer_id' => $customers[$i],
                'gateway' => ['razorpay', 'razorpay', 'cod', 'razorpay', 'cod'][$i],
                'gateway_payment_id' => $i < 3 ? 'pay_' . Str::random(14) : null,
                'gateway_order_id' => $i < 3 ? 'order_' . Str::random(14) : null,
                'amount_paise' => rand(49900, 499900),
                'currency' => 'INR',
                'status' => ['captured', 'captured', 'captured', 'captured', 'pending'][$i],
                'paid_at' => $i < 4 ? now()->subDays(rand(1, 5)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Payments seeded');

        // ══════════════════════════════════════════════════════════════
        // 24. REFUNDS
        // ══════════════════════════════════════════════════════════════
        for ($i = 0; $i < 5; $i++) {
            DB::table('refunds')->insert([
                'payment_id' => $paymentIds[$i],
                'order_id' => $orderIds[$i],
                'gateway_refund_id' => 'rfnd_' . Str::random(14),
                'amount_paise' => rand(10000, 50000),
                'reason' => ['Service issue', 'Not satisfied', 'Duplicate', 'No-show', 'Wrong service'][$i],
                'status' => ['processed', 'processed', 'pending', 'processed', 'failed'][$i],
                'processed_at' => $i !== 2 ? now()->subDays(rand(1, 3)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Refunds seeded');

        // ══════════════════════════════════════════════════════════════
        // 25. WALLETS (holder_type: customer/professional)
        // ══════════════════════════════════════════════════════════════
        $walletIds = [];
        foreach ($customers as $i => $cid) {
            $walletIds[] = DB::table('wallets')->insertGetId([
                'holder_type' => 'customer',
                'holder_id' => $cid,
                'type' => 'coin',
                'balance' => rand(50, 500),
                'version' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($proIds as $pid) {
            $walletIds[] = DB::table('wallets')->insertGetId([
                'holder_type' => 'professional',
                'holder_id' => $pid,
                'type' => 'cash',
                'balance' => rand(100000, 1000000),
                'version' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Wallets seeded');

        // ══════════════════════════════════════════════════════════════
        // 26. WALLET TRANSACTIONS
        // ══════════════════════════════════════════════════════════════
        $sources = ['order_completion', 'refund', 'admin_adjustment', 'review_reward', 'promotion'];
        foreach (array_slice($walletIds, 0, 5) as $i => $wid) {
            // Fetch the wallet model instance
            $wallet = \App\Models\Wallet::find($wid);

            if ($wallet) {
                // Example 1: Credit transaction for referral bonus
                DB::table('wallet_transactions')->insert([
                    'wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => 500,
                    'balance_after' => $wallet->balance + 500, // Assuming balance update logic
                    'source' => 'referral_bonus',
                    'description' => 'Referral Bonus - Amit',
                    'created_at' => now()->subDays(rand(1, 10)),
                    'updated_at' => now(),
                ]);

                // Example 2: Debit transaction for booking payment
                DB::table('wallet_transactions')->insert([
                    'wallet_id' => $wallet->id,
                    'type' => 'debit',
                    'amount' => 200,
                    'balance_after' => $wallet->balance + 500 - 200, // Assuming balance update logic
                    'source' => 'booking_payment',
                    'description' => 'Facial Service Payment',
                    'created_at' => now()->subDays(rand(1, 10)),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->command->info('✅ Wallet Transactions seeded');

        // ══════════════════════════════════════════════════════════════
        // 27. ROLES + PERMISSIONS + PIVOT
        // ══════════════════════════════════════════════════════════════
        $roleData = [
            ['Super Admin', 'super-admin', true],
            ['Admin', 'admin', true],
            ['Manager', 'manager', false],
            ['Support', 'support', false],
            ['Viewer', 'viewer', false],
        ];
        $roleIds = [];
        foreach ($roleData as $r) {
            $roleIds[] = DB::table('roles')->insertGetId([
                'name' => $r[0],
                'slug' => $r[1],
                'description' => "{$r[0]} role",
                'is_system' => $r[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $permData = [
            ['Manage Orders', 'manage-orders', 'orders'],
            ['View Reports', 'view-reports', 'reports'],
            ['Manage Professionals', 'manage-professionals', 'professionals'],
            ['Manage Promotions', 'manage-promotions', 'promotions'],
            ['Manage Settings', 'manage-settings', 'settings'],
        ];
        $permIds = [];
        foreach ($permData as $p) {
            $permIds[] = DB::table('permissions')->insertGetId([
                'name' => $p[0],
                'slug' => $p[1],
                'group' => $p[2],
                'description' => "Permission to {$p[0]}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($permIds as $pid) {
            DB::table('role_permission')->insert(['role_id' => $roleIds[0], 'permission_id' => $pid]);
        }
        $this->command->info('✅ Roles + Permissions seeded');

        // ══════════════════════════════════════════════════════════════
        // 28. ADMIN PROFILES (admin_id)
        // ══════════════════════════════════════════════════════════════
        foreach ($admins as $i => $aid) {
            DB::table('admin_profiles')->insert([
                'admin_id' => $aid,
                'role_id' => $roleIds[$i],
                'is_super_admin' => $i === 0,
                'last_login_at' => now()->subHours(rand(1, 48)),
                'last_login_ip' => '192.168.1.' . rand(1, 254),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Admin Profiles seeded');

        // ══════════════════════════════════════════════════════════════
        // 29. NOTIFICATIONS (customer_id / admin_id)
        // ══════════════════════════════════════════════════════════════
        for ($i = 0; $i < 5; $i++) {
            DB::table('customer_notifications')->insert([
                'customer_id' => $customers[$i],
                'type' => ['order_update', 'promotion', 'review', 'wallet', 'system'][$i],
                'title' => "Notification for {$custNames[$i]}",
                'body' => 'Your update is here!',
                'data' => json_encode(['id' => $i + 1]),
                'read_at' => $i < 2 ? now() : null,
                'created_at' => now()->subHours(rand(1, 72)),
                'updated_at' => now(),
            ]);
            DB::table('professional_notifications')->insert([
                'professional_id' => $proIds[$i],
                'type' => ['order_assigned', 'kit_update', 'target', 'wallet', 'system'][$i],
                'title' => "Alert for {$proNames[$i]}",
                'body' => 'Check your dashboard',
                'data' => json_encode(['ref' => $i + 1]),
                'read_at' => $i < 3 ? now() : null,
                'created_at' => now()->subHours(rand(1, 48)),
                'updated_at' => now(),
            ]);
            DB::table('admin_notifications')->insert([
                'admin_id' => $admins[0],
                'type' => ['new_order', 'verification', 'leave', 'review', 'system'][$i],
                'title' => 'Admin Alert',
                'body' => 'Requires attention',
                'data' => json_encode(['action' => 'review']),
                'read_at' => $i < 2 ? now() : null,
                'created_at' => now()->subHours(rand(1, 24)),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ All Notifications seeded');

        // ══════════════════════════════════════════════════════════════
        // 30. PROMOTIONS + USAGES (customer_id)
        // ══════════════════════════════════════════════════════════════
        $promoCodes = ['WELCOME50', 'SUMMER20', 'BOGO2025', 'FLAT200', 'NEWUSER'];
        $promoIds = [];
        foreach ($promoCodes as $i => $code) {
            $promoIds[] = DB::table('promotions')->insertGetId([
                'name' => ucwords(strtolower($code)) . ' Offer',
                'code' => $code,
                'description' => "Special {$code} promotion",
                'type' => ['percentage', 'percentage', 'bogo', 'flat', 'percentage'][$i],
                'value' => $i === 3 ? 20000 : rand(10, 50),
                'max_discount_paise' => $i !== 3 ? rand(30000, 100000) : null,
                'min_order_paise' => rand(29900, 99900),
                'usage_limit' => rand(100, 1000),
                'per_user_limit' => rand(1, 3),
                'times_used' => rand(0, 50),
                'starts_at' => now()->subDays(10),
                'ends_at' => now()->addDays(30),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        for ($i = 0; $i < 5; $i++) {
            DB::table('promotion_usages')->insert([
                'promotion_id' => $promoIds[$i],
                'customer_id' => $customers[$i],
                'order_id' => $orderIds[$i],
                'discount_paise' => rand(5000, 50000),
                'created_at' => now()->subDays(rand(1, 7)),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Promotions seeded');

        // ══════════════════════════════════════════════════════════════
        // 31-33. SERVICE OPTIONS, VARIANTS, TAGS
        // ══════════════════════════════════════════════════════════════
        $optionNames = ['Keratin Treatment', 'Gold Facial Add-on', 'Chrome Finish', 'HD Foundation', 'Hot Stone'];
        $variantNames = ['Short Hair', 'Medium Hair', 'Long Hair', 'Extra Long', 'Shoulder Length'];
        $tagNames = ['trending', 'premium', 'budget-friendly', 'bestseller', 'new-arrival'];
        $tagIds = [];
        foreach ($serviceIds as $i => $sid) {
            DB::table('service_options')->insert([
                'service_id' => $sid,
                'name' => $optionNames[$i],
                'description' => "Premium {$optionNames[$i]} add-on",
                'price_paise' => rand(19900, 99900),
                'duration_minutes' => rand(15, 45),
                'is_required' => false,
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('service_variants')->insert([
                'service_id' => $sid,
                'name' => $variantNames[$i],
                'price_paise' => rand(29900, 149900),
                'duration_minutes' => rand(30, 90),
                'is_default' => $i === 0,
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($tagNames as $t) {
            $tagIds[] = DB::table('tags')->insertGetId([
                'name' => ucwords(str_replace('-', ' ', $t)),
                'slug' => $t,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($serviceIds as $i => $sid) {
            DB::table('service_tag')->insert(['service_id' => $sid, 'tag_id' => $tagIds[$i]]);
        }
        $this->command->info('✅ Service Extensions seeded');

        // ══════════════════════════════════════════════════════════════
        // 34-39. KIT EXTENDED TABLES
        // ══════════════════════════════════════════════════════════════
        $kitCatNames = ['Waxing Kits', 'Hair Kits', 'Facial Kits', 'Nail Kits', 'Spa Kits'];
        $kitCatIds = [];
        foreach ($kitCatNames as $kc) {
            $kitCatIds[] = DB::table('kit_categories')->insertGetId([
                'name' => $kc,
                'slug' => Str::slug($kc),
                'description' => "{$kc} for pros",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($kitCatIds as $i => $kcid) {
            DB::table('kit_types')->insert([
                'kit_category_id' => $kcid,
                'name' => str_replace(' Kits', ' Basic', $kitCatNames[$i]),
                'description' => 'Basic variant',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $kitUnitIds = [];
        foreach ($kitIds as $kpid) {
            $kitUnitIds[] = DB::table('kit_units')->insertGetId([
                'kit_product_id' => $kpid,
                'serial_number' => 'SN-' . strtoupper(Str::random(8)),
                'qr_code' => 'QR-' . strtoupper(Str::random(10)),
                'status' => 'available',
                'expiry_date' => now()->addMonths(rand(6, 18)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($proIds as $i => $pid) {
            DB::table('professional_kit_units')->insert([
                'professional_id' => $pid,
                'kit_unit_id' => $kitUnitIds[$i],
                'assigned_at' => now()->subDays(rand(1, 10)),
                'used_at' => $i < 2 ? now()->subDays(rand(0, 3)) : null,
                'order_id' => $i < 2 ? $orderIds[$i] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('kit_shipments')->insert([
                'professional_id' => $pid,
                'tracking_number' => 'SHIP' . rand(100000, 999999),
                'courier' => ['BlueDart', 'DTDC', 'Delhivery', 'FedEx', 'India Post'][$i],
                'status' => ['delivered', 'shipped', 'in_transit', 'preparing', 'delivered'][$i],
                'address' => $cities[$i],
                'items' => json_encode([['product' => $kitNames[$i], 'qty' => rand(1, 3)]]),
                'shipped_at' => $i < 3 ? now()->subDays(rand(3, 10)) : null,
                'delivered_at' => ($i === 0 || $i === 4) ? now()->subDays(rand(1, 3)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($orderIds as $i => $oid) {
            DB::table('order_kit_scans')->insert([
                'order_id' => $oid,
                'kit_unit_id' => $kitUnitIds[$i],
                'professional_id' => $proIds[$i],
                'scan_type' => $i % 2 === 0 ? 'qr' : 'manual',
                'is_valid' => $i !== 4,
                'rejection_reason' => $i === 4 ? 'Kit expired' : null,
                'scanned_at' => now()->subHours(rand(1, 48)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Kit Extended Tables seeded');

        // ══════════════════════════════════════════════════════════════
        // 40-43. PROFESSIONAL PROFILE (verified_by → admin_id)
        // ══════════════════════════════════════════════════════════════
        $docTypes = ['aadhaar', 'pan', 'certificate', 'license', 'photo'];
        $areas = ['Andheri West', 'Connaught Place', 'Koramangala', 'Kothrud', 'Banjara Hills'];
        $devices = ['Samsung Galaxy S23', 'iPhone 15', 'OnePlus 12', 'Pixel 8', 'Redmi Note 13'];
        foreach ($proIds as $i => $pid) {
            DB::table('professional_documents')->insert([
                'professional_id' => $pid,
                'type' => $docTypes[$i],
                'file_path' => "documents/professionals/{$pid}/{$docTypes[$i]}.pdf",
                'status' => $i < 3 ? 'approved' : 'pending',
                'verified_at' => $i < 3 ? now()->subDays(rand(10, 60)) : null,
                'verified_by' => $i < 3 ? $admins[0] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('professional_service_areas')->insert([
                'professional_id' => $pid,
                'city' => $cities[$i],
                'area' => $areas[$i],
                'pincode' => str_pad(rand(100000, 999999), 6, '0'),
                'latitude' => 19.0760 + ($i * 2),
                'longitude' => 72.8777 + ($i * 1.5),
                'radius_km' => rand(5, 20),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('professional_devices')->insert([
                'professional_id' => $pid,
                'device_type' => $i === 1 ? 'ios' : 'android',
                'device_model' => $devices[$i],
                'fcm_token' => 'fcm_' . Str::random(40),
                'app_version' => '2.' . rand(0, 5) . '.' . rand(0, 9),
                'last_active_at' => now()->subMinutes(rand(5, 360)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $onlineAt = now()->subHours(rand(2, 8));
            $offlineAt = $i < 3 ? (clone $onlineAt)->addMinutes(rand(60, 480)) : null;
            DB::table('professional_online_sessions')->insert([
                'professional_id' => $pid,
                'went_online_at' => $onlineAt,
                'went_offline_at' => $offlineAt,
                'duration_minutes' => $offlineAt ? $onlineAt->diffInMinutes($offlineAt) : 0,
                'latitude' => 19.0760 + ($i * 0.5),
                'longitude' => 72.8777 + ($i * 0.3),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Professional Profiles seeded');

        // ══════════════════════════════════════════════════════════════
        // 44-47. MEMBERSHIPS + TARGETS (customer_id)
        // ══════════════════════════════════════════════════════════════
        $planNames = ['Silver', 'Gold', 'Platinum', 'Diamond', 'VIP'];
        $planIds = [];
        foreach ($planNames as $i => $plan) {
            $planIds[] = DB::table('membership_plans')->insertGetId([
                'name' => "{$plan} Membership",
                'slug' => Str::slug($plan),
                'description' => "{$plan} membership",
                'price_paise' => ($i + 1) * 99900,
                'duration_days' => [30, 90, 180, 365, 365][$i],
                'discount_percentage' => ($i + 1) * 5,
                'coins_reward' => ($i + 1) * 100,
                'benefits' => json_encode(['priority_booking', 'free_delivery']),
                'is_active' => true,
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($customers as $i => $cid) {
            DB::table('customer_memberships')->insert([
                'customer_id' => $cid,
                'membership_plan_id' => $planIds[$i],
                'starts_at' => now()->subDays(rand(1, 30)),
                'expires_at' => now()->addDays(rand(30, 365)),
                'status' => $i < 4 ? 'active' : 'expired',
                'payment_id' => $paymentIds[$i],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $targetData = [
            ['Monthly Services Star', 'services_completed', 30, 'monthly', 100, 0],
            ['Online Champion', 'online_minutes', 6000, 'monthly', 50, 50000],
            ['Revenue King', 'revenue_paise', 5000000, 'monthly', 200, 100000],
            ['Weekly Sprint', 'services_completed', 10, 'weekly', 30, 0],
            ['Daily Warrior', 'work_minutes', 480, 'daily', 10, 0],
        ];
        $targetIds = [];
        foreach ($targetData as $t) {
            $targetIds[] = DB::table('performance_targets')->insertGetId([
                'name' => $t[0],
                'metric' => $t[1],
                'target_value' => $t[2],
                'period' => $t[3],
                'reward_coins' => $t[4],
                'reward_cash_paise' => $t[5],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($proIds as $i => $pid) {
            DB::table('professional_target_assignments')->insert([
                'professional_id' => $pid,
                'performance_target_id' => $targetIds[$i],
                'current_value' => rand(5, 25),
                'is_completed' => $i < 2,
                'completed_at' => $i < 2 ? now()->subDays(rand(1, 5)) : null,
                'reward_claimed' => $i === 0,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Memberships + Targets seeded');

        $this->command->info('');
        $this->command->info('🎉 ALL TABLES SEEDED — CUSTOMERS + ADMINS + OTP ARCHITECTURE!');
    }
}
