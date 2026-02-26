<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Package;
use App\Models\Professional;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Media;
use App\Models\HomepageContent;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class LegacyDataSeeder extends Seeder
{
    public function run(): void
    {
        try {
            Model::unguard();

            // 1. Static Admin (High ID to avoid conflict with legacy data)
            User::create([
                'id' => 99,
                'name' => 'Admin',
                'email' => 'admin@bellavella.com',
                'password' => Hash::make('admin123'),
            ]);

            // 2. Categories
            $categories = [
                ['id'=>10,'name'=>'Bridal',       'slug'=>'bridal',       'services_count'=>14,'bookings_count'=>342,'status'=>'Active',  'featured'=>true, 'color'=>'#be185d','image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80','description'=>'Comprehensive bridal beauty packages for the most important day.'],
                ['id'=>11,'name'=>'Spa & Wellness','slug'=>'spa-wellness', 'services_count'=>8, 'bookings_count'=>189,'status'=>'Active',  'featured'=>true, 'color'=>'#1d4ed8','image'=>'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=400&q=80','description'=>'Relaxing spa treatments and wellness rituals for mind and body.'],
                ['id'=>12,'name'=>'Makeup',       'slug'=>'makeup',       'services_count'=>9, 'bookings_count'=>267,'status'=>'Active',  'featured'=>true, 'color'=>'#7c3aed','image'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=400&q=80','description'=>'Expert makeup artistry for parties, events, and everyday glam.'],
                ['id'=>13,'name'=>'Facial',       'slug'=>'facial',       'services_count'=>6, 'bookings_count'=>210,'status'=>'Active',  'featured'=>false,'color'=>'#db2777','image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','description'=>'Luxury facial treatments using premium skincare products.'],
                ['id'=>20,'name'=>'Hair Care',     'slug'=>'hair-care',    'services_count'=>7, 'bookings_count'=>155,'status'=>'Active',  'featured'=>false,'color'=>'#0891b2','image'=>'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=400&q=80','description'=>'Complete hair care from cuts and styling to treatments.'],
                ['id'=>21,'name'=>'Hair Color',    'slug'=>'hair-color',   'services_count'=>5, 'bookings_count'=>132,'status'=>'Active',  'featured'=>false,'color'=>'#d97706','image'=>'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=400&q=80','description'=>'Professional hair colouring, highlights, and balayage.'],
                ['id'=>22,'name'=>'Nail Art',      'slug'=>'nail-art',     'services_count'=>5, 'bookings_count'=>98, 'status'=>'Inactive','featured'=>false,'color'=>'#e11d48','image'=>'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=400&q=80','description'=>'Creative nail art, gel extensions, and nail care treatments.'],
                ['id'=>23,'name'=>'Grooming',      'slug'=>'grooming',     'services_count'=>6, 'bookings_count'=>520,'status'=>'Active',  'featured'=>false,'color'=>'#15803d','image'=>'https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=400&q=80','description'=>'Professional grooming services for a sharp, polished look.'],
                ['id'=>24,'name'=>'Skin Care',     'slug'=>'skin-care',    'services_count'=>11,'bookings_count'=>411,'status'=>'Active',  'featured'=>false,'color'=>'#a16207','image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','description'=>'Advanced skin care treatments using the latest technology.'],
            ];
            foreach ($categories as $cat) Category::create($cat);

            // Uncommented for full test
            // 3. Services
            $services = [
                ['id'=>1,'name'=>'HD Bridal Makeup','category'=>'Bridal','duration'=>120,'price'=>8500,'status'=>'Active','featured'=>true,'bookings'=>142,'image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80','description'=>'Full HD bridal makeup with airbrush finish, long-lasting formula, and premium products.'],
                ['id'=>2,'name'=>'Gold Facial','category'=>'Skin Care','duration'=>90,'price'=>3500,'status'=>'Active','featured'=>false,'bookings'=>89,'image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','description'=>'Luxury gold facial for skin tightening and instant bridal glow.'],
                ['id'=>3,'name'=>'Aromatherapy Massage','category'=>'Spa & Wellness','duration'=>60,'price'=>2200,'status'=>'Active','featured'=>true,'bookings'=>64,'image'=>'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=400&q=80','description'=>'Relaxing full body massage with essential oils for stress relief.'],
                ['id'=>4,'name'=>'Express Grooming','category'=>'Grooming','duration'=>45,'price'=>1200,'status'=>'Active','featured'=>false,'bookings'=>212,'image'=>'https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=400&q=80','description'=>'Quick beard trim, haircut, and facial for the busy professional.'],
                ['id'=>5,'name'=>'Deep Cleanse Facial','category'=>'Skin Care','duration'=>75,'price'=>2800,'status'=>'Inactive','featured'=>false,'bookings'=>31,'image'=>'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?auto=format&fit=crop&w=400&q=80','description'=>'Purifying deep cleansing treatment to remove impurities and dead skin.'],
                ['id'=>6,'name'=>'Hydra Facial','category'=>'Skin Care','duration'=>60,'price'=>4500,'status'=>'Active','featured'=>true,'bookings'=>156,'image'=>'https://images.unsplash.com/photo-1512290923902-8a9f81dc2069?auto=format&fit=crop&w=400&q=80','description'=>'Advanced 3-step hydration treatment for medical-grade skin rejuvenation.'],
            ];
            foreach ($services as $svc) Service::create($svc);

            // 4. Packages
            $packages = [
                ['id'=>1,'name'=>'HD Bridal Makeup','category'=>'Luxe','services'=>['Gold Facial','Hair Styling','Manicure & Pedicure'],'price'=>12000,'discount'=>20,'duration'=>300,'bookings'=>48,'status'=>'Active','featured'=>true,'image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=800&q=80','description'=>'Complete bridal transformation package with premium gold facial, professional hair styling, and luxury manicure & pedicure.','created_at_legacy'=>'2023-08-15'],
                ['id'=>2,'name'=>'Weekend Rejuvenation','category'=>'Luxe','services'=>['Aromatherapy Massage','Deep Cleansing Facial'],'price'=>5000,'discount'=>0,'duration'=>120,'bookings'=>91,'status'=>'Active','featured'=>false,'image'=>'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=800&q=80','description'=>'Unwind with a soothing aromatherapy massage followed by a deep cleansing facial for glowing skin.','created_at_legacy'=>'2023-09-01'],
                ['id'=>3,'name'=>'Express Grooming','category'=>'Prime','services'=>['Haircut','Beard Trim','Express Facial'],'price'=>1500,'discount'=>5,'duration'=>60,'bookings'=>134,'status'=>'Active','featured'=>false,'image'=>'https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=800&q=80','description'=>'Quick yet thorough grooming session covering haircut, beard trim, and an express facial.','created_at_legacy'=>'2023-10-10'],
                ['id'=>4,'name'=>'Party Glam','category'=>'Luxe','services'=>['Party Makeup','Blow Dry','Nail Art'],'price'=>4500,'discount'=>10,'duration'=>180,'bookings'=>62,'status'=>'Active','featured'=>true,'image'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=800&q=80','description'=>'Get party-ready with a full glam makeup look, salon blow dry, and trendy nail art.','created_at_legacy'=>'2023-11-05'],
                ['id'=>5,'name'=>'Spa Bliss','category'=>'Luxe','services'=>['Swedish Massage','Hydra Facial','Foot Spa'],'price'=>7500,'discount'=>15,'duration'=>240,'bookings'=>29,'status'=>'Inactive','featured'=>false,'image'=>'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80','description'=>'A full-day spa experience with Swedish massage, hydra facial, and relaxing foot spa.','created_at_legacy'=>'2024-01-20'],
                ['id'=>6,'name'=>'Nail Art Deluxe','category'=>'Prime','services'=>['Gel Nails','Nail Art','Cuticle Care'],'price'=>2000,'discount'=>0,'duration'=>90,'bookings'=>77,'status'=>'Active','featured'=>false,'image'=>'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=800&q=80','description'=>'Premium nail art session with gel nails, intricate designs, and cuticle care.','created_at_legacy'=>'2024-02-01'],
            ];
            foreach ($packages as $pkg) {
                if (isset($pkg['services']) && is_array($pkg['services'])) {
                    $pkg['services'] = json_encode($pkg['services']);
                }
                Package::create($pkg);
            }

            // 5. Professionals
            $professionals = [
                ['id'=>1,'name'=>'Zara J.','avatar'=>'https://i.pravatar.cc/100?img=47','category'=>'Beauty Specialist','phone'=>'+91 98765 43210','city'=>'Mumbai','status'=>'Active','verification'=>'Verified','orders'=>142,'earnings'=>84200,'commission'=>15,'experience'=>'5 years','joined'=>'2023-06-12','services'=>['HD Bridal Makeup','Party Glam','Gold Facial'],'docs'=>true,'rating'=>4.9],
                ['id'=>2,'name'=>'Michael C.','avatar'=>'https://i.pravatar.cc/100?img=12','category'=>'Hair Artist','phone'=>'+91 87654 32109','city'=>'Delhi','status'=>'Active','verification'=>'Pending','orders'=>67,'earnings'=>32100,'commission'=>12,'experience'=>'3 years','joined'=>'2023-09-01','services'=>['Classic Haircut','Nail Art'],'docs'=>true,'rating'=>4.6],
                ['id'=>3,'name'=>'Sarah P.','avatar'=>'https://i.pravatar.cc/100?img=45','category'=>'Spa Therapist','phone'=>'+91 76543 21098','city'=>'Bangalore','status'=>'Active','verification'=>'Verified','orders'=>211,'earnings'=>126600,'commission'=>15,'experience'=>'7 years','joined'=>'2023-03-20','services'=>['Hydra Facial','Aromatherapy Massage','Deep Tissue Massage'],'docs'=>true,'rating'=>4.8],
                ['id'=>4,'name'=>'Elena M.','avatar'=>'https://i.pravatar.cc/100?img=32','category'=>'Skin Expert','phone'=>'+91 65432 10987','city'=>'Pune','status'=>'Active','verification'=>'Verified','orders'=>34,'earnings'=>15300,'commission'=>12,'experience'=>'2 years','joined'=>'2024-01-05','services'=>['Express Grooming'],'docs'=>true,'rating'=>3.8],
                ['id'=>5,'name'=>'Robert K.','avatar'=>'https://i.pravatar.cc/100?img=68','category'=>'Grooming Pro','phone'=>'+91 54321 09876','city'=>'Chennai','status'=>'Active','verification'=>'Rejected','orders'=>0,'earnings'=>0,'commission'=>15,'experience'=>'4 years','joined'=>'2024-02-10','services'=>[],'docs'=>false,'rating'=>0],
                ['id'=>6,'name'=>'Julia S.','avatar'=>'https://i.pravatar.cc/100?img=44','category'=>'Bridal Specialist','phone'=>'+91 43210 98765','city'=>'Hyderabad','status'=>'Active','verification'=>'Verified','orders'=>89,'earnings'=>44500,'commission'=>12,'experience'=>'4 years','joined'=>'2023-11-15','services'=>['Weekend Rejuvenation','Nail Art Deluxe'],'docs'=>true,'rating'=>4.7],
            ];
            foreach ($professionals as $pro) {
                if (isset($pro['services']) && is_array($pro['services'])) {
                    $pro['services'] = json_encode($pro['services']);
                }
                Professional::create($pro);
            }

            // 6. Customers (Keep existing)
            $customers = [
                ['id'=>1, 'name'=>'Ananya Kapoor',   'email'=>'ananya@example.com',   'phone'=>'+91 98765 43210', 'city'=>'Mumbai',    'status'=>'Active',   'bookings'=>14, 'joined'=>'2024-01-15', 'avatar'=>'https://i.pravatar.cc/80?img=1', 'password' => Hash::make('user123')],
                ['id'=>2, 'name'=>'Priya Sharma',    'email'=>'priya@example.com',    'phone'=>'+91 91234 56789', 'city'=>'Delhi',     'status'=>'Active',   'bookings'=>9,  'joined'=>'2024-02-03', 'avatar'=>'https://i.pravatar.cc/80?img=5', 'password' => Hash::make('user123')],
                ['id'=>3, 'name'=>'Meera Patel',     'email'=>'meera@example.com',    'phone'=>'+91 99887 76655', 'city'=>'Ahmedabad', 'status'=>'Active',   'bookings'=>22, 'joined'=>'2024-01-28', 'avatar'=>'https://i.pravatar.cc/80?img=9', 'password' => Hash::make('user123')],
                ['id'=>4, 'name'=>'Sneha Gupta',     'email'=>'sneha@example.com',    'phone'=>'+91 87654 32100', 'city'=>'Pune',      'status'=>'Inactive', 'bookings'=>3,  'joined'=>'2024-03-10', 'avatar'=>'https://i.pravatar.cc/80?img=10', 'password' => Hash::make('user123')],
                ['id'=>5, 'name'=>'Kavya Reddy',     'email'=>'kavya@example.com',    'phone'=>'+91 95555 11223', 'city'=>'Hyderabad', 'status'=>'Active',   'bookings'=>18, 'joined'=>'2024-02-20', 'avatar'=>'https://i.pravatar.cc/80?img=21', 'password' => Hash::make('user123')],
                ['id'=>6, 'name'=>'Divya Nair',      'email'=>'divya@example.com',    'phone'=>'+91 80000 12345', 'city'=>'Bangalore', 'status'=>'Active',   'bookings'=>7,  'joined'=>'2024-03-05', 'avatar'=>'https://i.pravatar.cc/80?img=25', 'password' => Hash::make('user123')],
                ['id'=>7, 'name'=>'Riya Mehta',      'email'=>'riya@example.com',     'phone'=>'+91 70001 23456', 'city'=>'Jaipur',    'status'=>'Inactive', 'bookings'=>1,  'joined'=>'2024-04-01', 'avatar'=>'https://i.pravatar.cc/80?img=30', 'password' => Hash::make('user123')],
                ['id'=>8, 'name'=>'Neha Joshi',      'email'=>'neha@example.com',     'phone'=>'+91 96666 54321', 'city'=>'Chennai',   'status'=>'Active',   'bookings'=>11, 'joined'=>'2024-01-10', 'avatar'=>'https://i.pravatar.cc/80?img=35', 'password' => Hash::make('user123')],
                ['id'=>9, 'name'=>'Aisha Khan',      'email'=>'aisha@example.com',    'phone'=>'+91 93333 22111', 'city'=>'Lucknow',   'status'=>'Active',   'bookings'=>5,  'joined'=>'2024-03-22', 'avatar'=>'https://i.pravatar.cc/80?img=40', 'password' => Hash::make('user123')],
                ['id'=>10,'name'=>'Tanvi Singh',     'email'=>'tanvi@example.com',    'phone'=>'+91 94444 33222', 'city'=>'Kolkata',   'status'=>'Active',   'bookings'=>16, 'joined'=>'2024-02-14', 'avatar'=>'https://i.pravatar.cc/80?img=44', 'password' => Hash::make('user123')],
            ];
            foreach ($customers as $cust) User::create($cust);
            
            // 7. Bookings
            $bookings = [
                ['id'=>1001,'user_id'=>2,'customer_name'=>'Priya Sharma','customer_avatar'=>'https://i.pravatar.cc/80?img=5','city'=>'Delhi','service_id'=>1,'service_name'=>'HD Bridal Makeup','package_name'=>'Luxe Bridal','date'=>'2024-10-24','slot'=>'09:00 AM','status'=>'Pending','price'=>8500],
                ['id'=>1002,'user_id'=>3,'customer_name'=>'Meera Patel','customer_avatar'=>'https://i.pravatar.cc/80?img=9', 'city'=>'Ahmedabad','service_id'=>2,'service_name'=>'Gold Facial','date'=>'2024-10-24','slot'=>'10:00 AM','status'=>'Confirmed','price'=>3500],
                ['id'=>1003,'user_id'=>1,'customer_name'=>'Ananya Kapoor','customer_avatar'=>'https://i.pravatar.cc/80?img=1', 'city'=>'Mumbai','service_id'=>6,'service_name'=>'Hydra Facial','date'=>'2024-10-24','slot'=>'11:30 AM','status'=>'Pending','price'=>4500],
                ['id'=>1004,'user_id'=>4,'customer_name'=>'Sneha Gupta','customer_avatar'=>'https://i.pravatar.cc/80?img=10', 'city'=>'Pune','service_id'=>3,'service_name'=>'Hair Styling','date'=>'2024-10-25','slot'=>'02:00 PM','status'=>'Pending','price'=>2200],
                ['id'=>1005,'user_id'=>5,'customer_name'=>'Kavya Reddy','customer_avatar'=>'https://i.pravatar.cc/80?img=21', 'city'=>'Hyderabad','service_id'=>3,'service_name'=>'Spa Therapy','date'=>'2024-10-25','slot'=>'04:30 PM','status'=>'Pending','price'=>2800],
                ['id'=>1006,'user_id'=>6,'customer_name'=>'Divya Nair','customer_avatar'=>'https://i.pravatar.cc/80?img=25', 'city'=>'Bangalore','service_id'=>1,'service_name'=>'Bridal Makeup','date'=>'2024-10-26','slot'=>'10:00 AM','status'=>'Confirmed','price'=>8500],
                ['id'=>1007,'user_id'=>9,'customer_name'=>'Aisha Khan','customer_avatar'=>'https://i.pravatar.cc/80?img=40', 'city'=>'Lucknow','service_id'=>2,'service_name'=>'Skin Treatment','date'=>'2024-10-26','slot'=>'01:00 PM','status'=>'Pending','price'=>3500],
            ];
            foreach ($bookings as $b) Booking::create($b);

            // 8. Reviews
            $reviews = [
                ['user_id'=>1,'customer_name'=>'Ananya Kapoor','customer_avatar'=>'https://i.pravatar.cc/80?img=1','rating'=>5,'comment'=>'"Absolutely loved the bridal package! My skin was glowing on my big day."','status'=>'Approved','created_at'=>Carbon::now()->subDays(3)],
                ['user_id'=>2,'customer_name'=>'Priya Sharma','customer_avatar'=>'https://i.pravatar.cc/80?img=5','rating'=>4,'comment'=>'"Great service, my hair feels so much healthier now. Will definitely come back."','status'=>'Approved','created_at'=>Carbon::now()->subDays(4)],
            ];
            foreach ($reviews as $rev) Review::create($rev);

            // 9. Media
            $mediaItems = [
                ['type'=>'banner','title'=>'Summer Sale','url'=>'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=80','status'=>'Active','order'=>1],
                ['type'=>'video','title'=>'Bridal Masterclass','url'=>'https://example.com/video.mp4','thumbnail'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80','status'=>'Active','order'=>2],
            ];
            foreach ($mediaItems as $m) Media::create($m);

            // 10. Homepage Content
            HomepageContent::create([
                'section' => 'hero',
                'content' => ['title' => 'Luxury Beauty Services', 'subtitle' => 'Professional care for your special moments'],
            ]);

            Model::reguard();
        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            echo "TRACE: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }
}
