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
        DB::table('package_service')->truncate();
        DB::table('packages')->truncate();
        DB::table('service_types')->truncate();
        DB::table('services')->truncate();
        DB::table('service_groups')->truncate();
        DB::table('categories')->truncate();
        DB::table('professionals')->truncate();
        DB::table('bookings')->truncate();
        DB::table('reviews')->truncate();
        DB::table('media')->truncate();
        DB::table('category_banners')->truncate();
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
        DB::table('service_options')->truncate();
        DB::table('service_variants')->truncate();
        DB::table('hierarchy_banners')->truncate();
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
        foreach ($custNames as $i => $name) {
            $customerId = DB::table('customers')->insertGetId([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'mobile' => '98' . rand(10000000, 99999999),
                'referral_code' => strtoupper(Str::random(8)),
                'referred_by' => null,
                'avatar' => null,
                'date_of_birth' => now()->subYears(rand(18, 60))->subDays(rand(0, 365)),
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
        // 4. CATEGORIES  (matches Flutter bottom nav)
        // ══════════════════════════════════════════════════════════════
        // type 'services'  → tapping opens service-group picker then service list
        // type 'packages'  → tapping opens package listing directly
        $catData = [
            // [name, slug, type, sort, color, featured]
            ['Salon for Women',        'salon-for-women',        'services', 1, '#F9A8D4', true],
            ['Spa for Women',          'spa-for-women',          'services', 2, '#6EE7B7', true],
            ['Hair Studio for Women',  'hair-studio-for-women',  'services', 3, '#93C5FD', true],
            ['Bridal',                 'bridal',                 'packages', 4, '#FDE68A', true],
        ];
        $catIds = [];
        foreach ($catData as $c) {
            $catIds[$c[1]] = DB::table('categories')->insertGetId([
                'name'        => $c[0],
                'slug'        => $c[1],
                'type'        => $c[2],
                'sort_order'  => $c[3],
                'color'       => $c[4],
                'featured'    => $c[5],
                'status'      => 'Active',
                'description' => "{$c[0]} services.",
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        $this->command->info('✅ Categories seeded (Salon, Spa, Hair Studio, Bridal)');

        // ══════════════════════════════════════════════════════════════
        // 5. SERVICE GROUPS  (second-level picker in Flutter)
        // ══════════════════════════════════════════════════════════════
        // Salon → Luxe, Prime
        // Spa   → Ayurveda, Prime
        // Hair Studio & Bridal → no groups
        $groupData = [
            // [category_slug, name, slug, tag_label, sort]
            ['salon-for-women', 'Luxe',     'salon-luxe',      'Premium',    1],
            ['salon-for-women', 'Prime',    'salon-prime',     'Affordable', 2],
            ['spa-for-women',   'Ayurveda', 'spa-ayurveda',    'Holistic',   1],
            ['spa-for-women',   'Prime',    'spa-prime',       'Affordable', 2],
        ];
        $groupIds = [];
        foreach ($groupData as $g) {
            $groupIds[$g[2]] = DB::table('service_groups')->insertGetId([
                'category_id' => $catIds[$g[0]],
                'name'        => $g[1],
                'slug'        => $g[2],
                'tag_label'   => $g[3],
                'sort_order'  => $g[4],
                'status'      => 'Active',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        $this->command->info('✅ Service Groups seeded (Salon: Luxe/Prime, Spa: Ayurveda/Prime)');

        // ══════════════════════════════════════════════════════════════
        // 6. SERVICES  (bookable items under each group or category)
        // ══════════════════════════════════════════════════════════════
        // Threading has service_types (variants) — all others are simple.
        // Variants Data for different services
        $variantsMap = [
            'Waxing' => [
                ['name' => 'Full Leg (Aloe Wax)',      'price' => 499, 'duration' => 30],
                ['name' => 'Full Leg (Milk Roll-on)',  'price' => 699, 'duration' => 30],
                ['name' => 'Full Arm & Underarm',      'price' => 399, 'duration' => 25],
                ['name' => 'Back Waxing (Honey)',      'price' => 299, 'duration' => 20],
            ],
            'Threading' => [
                ['name' => 'Eyebrows',   'price' => 30,  'duration' => 5],
                ['name' => 'Forehead',   'price' => 30,  'duration' => 5],
                ['name' => 'Upper Lip',  'price' => 20,  'duration' => 5],
                ['name' => 'Chin',       'price' => 20,  'duration' => 5],
                ['name' => 'Full Face',  'price' => 120, 'duration' => 20],
            ],
            'Korean Facial' => [
                ['name' => 'Glass Skin Facial',   'price' => 1499, 'duration' => 60],
                ['name' => 'Age-Rewind Facial',   'price' => 1999, 'duration' => 75],
            ],
            'Cleanup' => [
                ['name' => 'Detox & Cleanup',     'price' => 599, 'duration' => 45],
                ['name' => 'Casmara Charcoal',    'price' => 899, 'duration' => 45],
            ],
            'Bleach, Detan & Massage' => [
                ['name' => 'Full Face Bleach',    'price' => 299, 'duration' => 20],
                ['name' => 'Full Back Detan',     'price' => 499, 'duration' => 30],
                ['name' => 'Head Massage (20m)',   'price' => 199, 'duration' => 20],
            ]
        ];

        // [name, category_slug, group_slug|null, duration, price, has_variants]
        $svcData = [
            ['Waxing',            'salon-for-women', 'salon-luxe',   45,  499, true],
            ['Korean Facial',      'salon-for-women', 'salon-luxe',   60,  999, true],
            ['Signature Facial',  'salon-for-women', 'salon-luxe',   60, 1299, false],
            ['Cleanup',           'salon-for-women', 'salon-luxe',   45,  599, true],
            ['Threading',         'salon-for-women', 'salon-luxe',   20,   30, true],
            ['Bleach, Detan & Massage', 'salon-for-women', 'salon-luxe', 30, 399, true],
            
            ['Basic Facial',      'salon-for-women', 'salon-prime',  45,  499, false],
            ['Hair Cut & Style',  'hair-studio-for-women', null,    45,  499, false],
            ['Keratin Treatment', 'hair-studio-for-women', null,   120, 2999, false],
            ['Swedish Massage',   'spa-for-women',   'spa-prime',    60,  999, false],
        ];

        $serviceIds = [];
        foreach ($svcData as $s) {
            $base = Str::slug($s[0]);
            $slug = $base; $n = 1;
            while (DB::table('services')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $n++;
            }
            $serviceId = DB::table('services')->insertGetId([
                'name'             => $s[0],
                'slug'             => $slug,
                'category_id'      => $catIds[$s[1]],
                'service_group_id' => $s[2] ? ($groupIds[$s[2]] ?? null) : null,
                'duration'         => $s[3],
                'price'            => $s[4],
                'has_variants'     => $s[5],
                'status'           => 'Active',
                'featured'         => true,
                'bookings'         => rand(5, 100),
                'description'      => "Premium {$s[0]} service.",
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $serviceIds[] = $serviceId;

            // If it has variants, seed them
            if ($s[5] && isset($variantsMap[$s[0]])) {
                foreach ($variantsMap[$s[0]] as $v) {
                    DB::table('service_variants')->insert([
                        'service_id'       => $serviceId,
                        'name'             => $v['name'],
                        'slug'             => Str::slug($v['name']),
                        'price'            => $v['price'],
                        'duration_minutes' => $v['duration'],
                        'status'           => 'Active',
                        'sort_order'       => 0,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }
        }
        $this->command->info('✅ Services seeded (17 services across Salon/Spa/Hair Studio with threading variants)');

        // ══════════════════════════════════════════════════════════════
        // 7. PACKAGES  (Bridal only — type=packages category)
        // ══════════════════════════════════════════════════════════════
        $pkgData = [
            ['Bridal Glow Package',       2999, 20, 180, 'Complete bridal skin prep package.'],
            ['Royal Bride Package',        5999, 15, 240, 'Luxury full-day bridal package.'],
            ['Budget Bride Package',       1999, 10, 120, 'Affordable bridal essentials.'],
            ['Pre-Bridal Ritual (3 Days)', 7999, 25, 300, 'Three-session pre-bridal ritual.'],
        ];
        $pkgIds = [];
        foreach ($pkgData as $p) {
            $base = Str::slug($p[0]);
            $slug = $base; $n = 1;
            while (DB::table('packages')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $n++;
            }
            $packageId = DB::table('packages')->insertGetId([
                'name'        => $p[0],
                'slug'        => $slug,
                'category_id' => $catIds['bridal'],
                'price'       => $p[1],
                'discount'    => $p[2],
                'duration'    => $p[3],
                'description' => $p[4],
                'bookings'    => rand(5, 50),
                'status'      => 'Active',
                'featured'    => true,
                'image'       => 'https://images.unsplash.com/photo-1522335789203-aa9fb3d5133b?q=80&w=400',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $pkgIds[] = $packageId;

            // Link to some sample services
            $sampleServices = array_slice($serviceIds, 0, rand(2, 4));
            foreach ($sampleServices as $sid) {
                DB::table('package_service')->insert([
                    'package_id' => $packageId,
                    'service_id' => $sid,
                ]);
            }
        }
        $this->command->info('✅ Packages seeded (4 Bridal packages)');

        // ══════════════════════════════════════════════════════════════
        // 7. PROFESSIONALS
        // ══════════════════════════════════════════════════════════════
        $proNames = ['Anjali Mehta', 'Kavita Singh', 'Pooja Reddy', 'Ritu Jain', 'Deepika Nair'];
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Pune', 'Hyderabad'];
        $proCats = ['Salon for Women', 'Spa for Women', 'Hair Studio for Women', 'Bridal', 'Salon for Women'];
        $proIds = [];
        foreach ($proNames as $i => $name) {
            $proIds[] = DB::table('professionals')->insertGetId([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@bellavella.com',
                'phone' => '97' . rand(10000000, 99999999),
                'city' => $cities[$i],
                'category' => $proCats[$i],
                'bio' => "Expert in {$proCats[$i]}.",
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
        $this->command->info('✅ Professionals seeded');

        // ══════════════════════════════════════════════════════════════
        // 8. BOOKINGS (customer_id instead of user_id)
        // ══════════════════════════════════════════════════════════════
        $statuses = ['pending', 'confirmed', 'assigned', 'in_progress', 'completed'];
        $bookingIds = [];
        for ($i = 0; $i < 5; $i++) {
            $bookingIds[] = DB::table('bookings')->insertGetId([
                'customer_id' => $customers[$i],
                'customer_name' => $custNames[$i],
                'customer_phone' => '98' . rand(10000000, 99999999),
                'city' => $cities[$i],
                'service_id' => $serviceIds[$i],
                'service_name' => $svcData[$i][0],
                'professional_id' => $proIds[$i],
                'professional_name' => $proNames[$i],
                'date' => now()->addDays(rand(1, 30)),
                'slot' => ['10:00 AM', '12:00 PM', '2:00 PM', '4:00 PM', '6:00 PM'][$i],
                'status' => $statuses[$i],
                'price' => $svcData[$i][4],
                'notes' => 'Sample booking #' . ($i + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ Bookings seeded');
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
                'type'        => $i <= 3 ? 'banner' : 'video',
                'title'       => $i <= 3 ? "Summer Sale Banner {$i}" : "Tutorial Video {$i}",
                'subtitle'    => $i <= 3 ? "Limited time offer #{$i}" : null,
                'url'         => "https://placehold.co/1200x400?text=Banner+{$i}",
                'thumbnail'   => "https://placehold.co/300x100?text=Thumb+{$i}",
                'target_page' => ['services', 'packages', 'offers', 'services', 'packages'][$i - 1],
                'status'      => 'Active',
                'order'       => $i,
                'created_at'  => now(),
                'updated_at'  => now(),
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
                'description' => "Special {$offerNames[$i]} offer for our valued customers.",
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
                'category_id' => null,
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
                'item_name' => $i % 2 === 0 ? $svcData[$i][0] : $pkgData[$i][0],
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
        // Legacy promotions seeding removed.
        // Offers are the canonical source of truth for coupon and discount flows.

        // ══════════════════════════════════════════════════════════════
        // 31-33. SERVICE OPTIONS, VARIANTS, TAGS
        // ══════════════════════════════════════════════════════════════
        $optionNames = ['Keratin Treatment', 'Gold Facial Add-on', 'Chrome Finish', 'HD Foundation', 'Hot Stone'];
        $variantNames = ['Short Hair', 'Medium Hair', 'Long Hair', 'Extra Long', 'Shoulder Length'];
        $tagNames = ['trending', 'premium', 'budget-friendly', 'bestseller', 'new-arrival'];
        $tagIds = [];
        foreach ($serviceIds as $i => $sid) {
            $optIdx = $i % count($optionNames);
            $varIdx = $i % count($variantNames);
            DB::table('service_options')->insert([
                'service_id' => $sid,
                'name' => $optionNames[$optIdx],
                'description' => "Premium {$optionNames[$optIdx]} add-on",
                'price_paise' => rand(19900, 99900),
                'duration_minutes' => rand(15, 45),
                'is_required' => false,
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
            DB::table('service_tag')->insert(['service_id' => $sid, 'tag_id' => $tagIds[$i % count($tagIds)]]);
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
