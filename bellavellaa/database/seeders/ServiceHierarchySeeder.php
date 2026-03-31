<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $groups = $this->seedGroups($categories);
        $types = $this->seedTypes($groups);
        $services = $this->seedServices($types, $groups, $categories);
        $this->seedVariants($services);
    }

    private function seedCategories(): array
    {
        $rows = [
            [
                'seed_key' => 'category.salon_for_women',
                'name' => 'Salon for Women',
                'slug' => 'salon-for-women',
                'type' => 'services',
                'tagline' => 'Professional salon care at home',
                'description' => 'Expert grooming, facials, and waxing delivered to your doorstep.',
                'sort_order' => 1,
                'status' => 'Active',
                'featured' => true,
                'color' => '#F9A8D4',
                'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?q=80&w=800',
                'icon' => 'https://img.icons8.com/color/96/hair-dryer.png',
            ],
            [
                'seed_key' => 'category.spa_for_women',
                'name' => 'Spa for Women',
                'slug' => 'spa-for-women',
                'type' => 'services',
                'tagline' => 'Relaxation, recovery, and rituals',
                'description' => 'Deep tissue massages and ayurvedic healing rituals.',
                'sort_order' => 2,
                'status' => 'Active',
                'featured' => true,
                'color' => '#6EE7B7',
                'image' => 'https://images.unsplash.com/photo-1544161515-4ae6ce6db87e?q=80&w=800',
                'icon' => 'https://img.icons8.com/color/96/spa-flower.png',
            ],
            [
                'seed_key' => 'category.hair_studio_for_women',
                'name' => 'Hair Studio for Women',
                'slug' => 'hair-studio-for-women',
                'type' => 'services',
                'tagline' => 'Cuts, styling, and treatment care',
                'description' => 'Transform your look with expert hair care and styling.',
                'sort_order' => 3,
                'status' => 'Active',
                'featured' => true,
                'color' => '#93C5FD',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aa9fb3d5133b?q=80&w=800',
                'icon' => 'https://img.icons8.com/color/96/comb.png',
            ],
            [
                'seed_key' => 'category.bridal',
                'name' => 'Bridal',
                'slug' => 'bridal',
                'type' => 'packages',
                'tagline' => 'Bridal packages and occasion beauty',
                'description' => 'Comprehensive bridal glow and makeup packages.',
                'sort_order' => 4,
                'status' => 'Active',
                'featured' => true,
                'color' => '#FDE68A',
                'image' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?q=80&w=800',
                'icon' => 'https://img.icons8.com/color/96/diamond-ring.png',
            ],
        ];

        $result = [];
        foreach ($rows as $row) {
            $category = Category::where('seed_key', $row['seed_key'])
                ->orWhere('slug', $row['slug'])
                ->first();
                
            if ($category) {
                $category->update($row);
            } else {
                $category = Category::create($row);
            }
            $result[$row['seed_key']] = $category;
        }

        return $result;
    }

    private function seedGroups(array $categories): array
    {
        $rows = [
            // Salon for Women
            [
                'seed_key' => 'group.salon_for_women.luxe',
                'category_key' => 'category.salon_for_women',
                'name' => 'Luxe',
                'slug' => 'salon-for-women-luxe',
                'description' => 'Premium beauty services with top-tier products.',
                'tag_label' => 'Premium',
                'badge' => 'High End',
                'sort_order' => 1,
                'status' => 'Active',
            ],
            [
                'seed_key' => 'group.salon_for_women.prime',
                'category_key' => 'category.salon_for_women',
                'name' => 'Prime',
                'slug' => 'salon-for-women-prime',
                'description' => 'Everyday value salon services.',
                'tag_label' => 'Affordable',
                'badge' => 'Value',
                'sort_order' => 2,
                'status' => 'Active',
            ],
            // Spa for Women
            [
                'seed_key' => 'group.spa_for_women.ayurveda',
                'category_key' => 'category.spa_for_women',
                'name' => 'Ayurveda',
                'slug' => 'spa-for-women-ayurveda',
                'description' => 'Ayurvedic rituals and restorative therapies.',
                'tag_label' => 'Holistic',
                'badge' => 'Traditional',
                'sort_order' => 1,
                'status' => 'Active',
            ],
            [
                'seed_key' => 'group.spa_for_women.prime',
                'category_key' => 'category.spa_for_women',
                'name' => 'Prime',
                'slug' => 'spa-for-women-prime',
                'description' => 'Relaxing massages for routine wellness.',
                'tag_label' => 'Standard',
                'badge' => 'Reliable',
                'sort_order' => 2,
                'status' => 'Active',
            ],
            // Hair Studio
            [
                'seed_key' => 'group.hair_studio.expert',
                'category_key' => 'category.hair_studio_for_women',
                'name' => 'Expert Care',
                'slug' => 'hair-studio-expert',
                'description' => 'Professional hair treatments and styling.',
                'tag_label' => 'Stylist Choice',
                'badge' => 'Certified',
                'sort_order' => 1,
                'status' => 'Active',
            ],
            // Bridal
            [
                'seed_key' => 'group.bridal.elite',
                'category_key' => 'category.bridal',
                'name' => 'Elite Packages',
                'slug' => 'bridal-elite',
                'description' => 'Curated bridal luxury for your big day.',
                'tag_label' => 'Bestseller',
                'badge' => 'Luxury',
                'sort_order' => 1,
                'status' => 'Active',
            ],
        ];

        $result = [];
        foreach ($rows as $row) {
            $group = ServiceGroup::where('seed_key', $row['seed_key'])
                ->orWhere('slug', $row['slug'])
                ->first();

            $data = [
                'seed_key' => $row['seed_key'],
                'category_id' => $categories[$row['category_key']]->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'description' => $row['description'],
                'tag_label' => $row['tag_label'],
                'badge' => $row['badge'] ?? null,
                'sort_order' => $row['sort_order'],
                'status' => $row['status'],
            ];

            if ($group) {
                $group->update($data);
            } else {
                $group = ServiceGroup::create($data);
            }
            $result[$row['seed_key']] = $group;
        }

        return $result;
    }

    private function seedTypes(array $groups): array
    {
        $rows = [
            // Luxe Salon
            ['seed_key' => 'type.facials', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Facials', 'slug' => 'facials', 'description' => 'Premium skin treatments.', 'sort_order' => 1, 'image' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?q=80&w=400'],
            ['seed_key' => 'type.cleanup', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Cleanup', 'slug' => 'cleanup', 'description' => 'Quick refreshing cleanses.', 'sort_order' => 2, 'image' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?q=80&w=400'],
            ['seed_key' => 'type.threading', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Threading', 'slug' => 'threading', 'description' => 'Precision facial hair removal.', 'sort_order' => 3, 'image' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?q=80&w=400'],
            ['seed_key' => 'type.bleach_massage', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Bleach and Massage', 'slug' => 'bleach-and-massage', 'description' => 'Detan and relaxation.', 'sort_order' => 4, 'image' => 'https://images.unsplash.com/photo-1519735891795-23f99092d647?q=80&w=400'],
            
            // Prime Salon
            ['seed_key' => 'type.manicure', 'group_key' => 'group.salon_for_women.prime', 'name' => 'Manicure', 'slug' => 'manicure', 'description' => 'Hand care and nail styling.', 'sort_order' => 1, 'image' => 'https://images.unsplash.com/photo-1604654894611-6973b376cbde?q=80&w=400'],
            ['seed_key' => 'type.pedicure', 'group_key' => 'group.salon_for_women.prime', 'name' => 'Pedicure', 'slug' => 'pedicure', 'description' => 'Foot care and therapy.', 'sort_order' => 2, 'image' => 'https://images.unsplash.com/photo-1519415510236-8557bada8b09?q=80&w=400'],
            
            // Spa
            ['seed_key' => 'type.massage', 'group_key' => 'group.spa_for_women.prime', 'name' => 'Massage', 'slug' => 'massage', 'description' => 'Relaxing body therapies.', 'sort_order' => 1, 'image' => 'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb2?q=80&w=400'],
            ['seed_key' => 'type.healing_rituals', 'group_key' => 'group.spa_for_women.ayurveda', 'name' => 'Healing Rituals', 'slug' => 'healing-rituals', 'description' => 'Traditional wellness rituals.', 'sort_order' => 1, 'image' => 'https://images.unsplash.com/photo-1531983412531-1f49a365f698?q=80&w=400'],

            // Hair Studio
            ['seed_key' => 'type.hair_cuts', 'group_key' => 'group.hair_studio.expert', 'name' => 'Cuts & Styling', 'slug' => 'hair-cuts', 'description' => 'Professional hair transformation.', 'sort_order' => 1, 'image' => 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?q=80&w=400'],
            ['seed_key' => 'type.hair_treatments', 'group_key' => 'group.hair_studio.expert', 'name' => 'Treatments', 'slug' => 'hair-treatments', 'description' => 'Spa for your hair scalp.', 'sort_order' => 2, 'image' => 'https://images.unsplash.com/photo-1527799822394-46585d80058b?q=80&w=400'],

            // Bridal
            ['seed_key' => 'type.makeup', 'group_key' => 'group.bridal.elite', 'name' => 'Bridal Makeup', 'slug' => 'bridal-makeup', 'description' => 'Redefining bridal elegance.', 'sort_order' => 1, 'image' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?q=80&w=400'],
        ];

        $result = [];
        foreach ($rows as $row) {
            $type = ServiceType::where('seed_key', $row['seed_key'])
                ->orWhere(function ($query) use ($row, $groups) {
                    $query->where('slug', $row['slug'])
                        ->where('service_group_id', $groups[$row['group_key']]->id);
                })
                ->first();

            $data = [
                'seed_key' => $row['seed_key'],
                'service_group_id' => $groups[$row['group_key']]->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'description' => $row['description'],
                'image' => $row['image'],
                'sort_order' => $row['sort_order'],
                'status' => 'Active',
            ];

            if ($type) {
                $type->update($data);
            } else {
                $type = ServiceType::create($data);
            }
            $result[$row['seed_key']] = $type;
        }

        return $result;
    }

    private function seedServices(array $types, array $groups, array $categories): array
    {
        $rows = [
            [
                'seed_key' => 'service.korean_facial',
                'type_key' => 'type.facials',
                'name' => 'Korean Facial',
                'slug' => 'korean-facial',
                'short_description' => 'Advanced 7-step Korean glass skin facial.',
                'long_description' => 'Experience the multi-step Korean ritual for deeply hydrated, luminous skin. Includes double cleansing, exfoliation, essence, and massage.',
                'duration_minutes' => 60,
                'base_price' => 1499,
                'has_variants' => true,
                'is_bookable' => false,
                'bookings' => 1250,
                'rating_avg' => 4.9,
                'review_count' => 84,
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?q=80&w=800',
            ],
            [
                'seed_key' => 'service.signature_facial',
                'type_key' => 'type.facials',
                'name' => 'Signature Facial',
                'slug' => 'signature-facial',
                'short_description' => 'Customized glow-boosting treatment.',
                'long_description' => 'A personalized skincare treatment designed to restore your natural radiance using organic extracts.',
                'duration_minutes' => 60,
                'base_price' => 1299,
                'sale_price' => 999,
                'has_variants' => false,
                'is_bookable' => true,
                'bookings' => 850,
                'rating_avg' => 4.8,
                'review_count' => 42,
                'image' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?q=80&w=800',
            ],
            [
                'seed_key' => 'service.ice_cream_delight_pedicure',
                'type_key' => 'type.pedicure',
                'name' => 'Ice Cream Pedicure',
                'slug' => 'ice-cream-delight-pedicure',
                'short_description' => 'A sweet retreat for your tired feet.',
                'long_description' => 'A creamy, strawberry-infused retreat to soften skin and refresh tired feet. Includes a specialized ice-cream scoop scrub.',
                'duration_minutes' => 60,
                'base_price' => 1579,
                'has_variants' => true,
                'is_bookable' => false,
                'bookings' => 2400,
                'rating_avg' => 4.9,
                'review_count' => 156,
                'image' => 'https://images.unsplash.com/photo-1519415510236-8557bada8b09?q=80&w=800',
            ],
            [
                'seed_key' => 'service.swedish_massage',
                'type_key' => 'type.massage',
                'name' => 'Swedish Massage',
                'slug' => 'swedish-massage',
                'short_description' => 'Full body relaxation and stress relief.',
                'long_description' => 'Traditional Swedish techniques to improve blood flow, ease muscle tension, and promote deep relaxation.',
                'duration_minutes' => 60,
                'base_price' => 1499,
                'has_variants' => false,
                'is_bookable' => true,
                'bookings' => 3200,
                'rating_avg' => 4.7,
                'review_count' => 210,
                'image' => 'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb2?q=80&w=800',
            ],
            [
                'seed_key' => 'service.threading_full_face',
                'type_key' => 'type.threading',
                'name' => 'Full Face Threading',
                'slug' => 'full-face-threading',
                'short_description' => 'Complete facial hair removal.',
                'long_description' => 'Precise threading for eyebrows, upper lip, chin, and forehead for a clean, smooth finish.',
                'duration_minutes' => 30,
                'base_price' => 120,
                'has_variants' => false,
                'is_bookable' => true,
                'bookings' => 5400,
                'rating_avg' => 4.6,
                'review_count' => 412,
                'image' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?q=80&w=800',
            ],
            // Hair Studio Services
            [
                'seed_key' => 'service.expert_haircut',
                'type_key' => 'type.hair_cuts',
                'name' => 'Expert Haircut',
                'slug' => 'expert-haircut',
                'short_description' => 'Style transformation by expert stylists.',
                'long_description' => 'Get a trend-setting look with our expert stylists. Includes wash, cut, and blow-dry styling.',
                'duration_minutes' => 45,
                'base_price' => 799,
                'has_variants' => false,
                'is_bookable' => true,
                'bookings' => 1200,
                'rating_avg' => 4.8,
                'review_count' => 95,
                'image' => 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?q=80&w=800',
            ],
            [
                'seed_key' => 'service.keratin_treatment',
                'type_key' => 'type.hair_treatments',
                'name' => 'Keratin Treatment',
                'slug' => 'keratin-treatment',
                'short_description' => 'Frizz-free, smooth and shiny hair.',
                'long_description' => 'Hydrate and restore your hair with our premium keratin therapy for long-lasting smoothness and shine.',
                'duration_minutes' => 120,
                'base_price' => 2999,
                'has_variants' => false,
                'is_bookable' => true,
                'bookings' => 450,
                'rating_avg' => 4.9,
                'review_count' => 32,
                'image' => 'https://images.unsplash.com/photo-1527799822394-46585d80058b?q=80&w=800',
            ],
            // Bridal Services
            [
                'seed_key' => 'service.elite_bridal_makeup',
                'type_key' => 'type.makeup',
                'name' => 'Elite Bridal Makeup',
                'slug' => 'elite-bridal-makeup',
                'short_description' => 'HD Bridal makeup with airbrush finish.',
                'long_description' => 'Look your best on your special day with our elite bridal makeup services using international brands.',
                'duration_minutes' => 180,
                'base_price' => 8999,
                'has_variants' => true,
                'is_bookable' => false,
                'bookings' => 120,
                'rating_avg' => 5.0,
                'review_count' => 18,
                'image' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?q=80&w=800',
            ],
        ];

        $result = [];
        foreach ($rows as $row) {
            $type = $types[$row['type_key']];
            $group = $type->serviceGroup;
            $category = $group->category;

            $service = Service::where('seed_key', $row['seed_key'])
                ->orWhere('slug', $row['slug'])
                ->first();

            $data = [
                'seed_key' => $row['seed_key'],
                'category_id' => $category->id,
                'service_group_id' => $group->id,
                'service_type_id' => $type->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'short_description' => $row['short_description'],
                'long_description' => $row['long_description'],
                'description' => $row['short_description'],
                'duration' => $row['duration_minutes'],
                'duration_minutes' => $row['duration_minutes'],
                'price' => $row['base_price'],
                'base_price' => $row['base_price'],
                'sale_price' => $row['sale_price'] ?? null,
                'has_variants' => $row['has_variants'],
                'is_bookable' => $row['is_bookable'],
                'status' => 'Active',
                'featured' => true,
                'bookings' => $row['bookings'],
                'rating_avg' => $row['rating_avg'],
                'review_count' => $row['review_count'],
                'image' => $row['image'],
            ];

            if ($service) {
                $service->update($data);
            } else {
                $service = Service::create($data);
            }
            $result[$row['seed_key']] = $service;
        }

        return $result;
    }

    private function seedVariants(array $services): void
    {
        $rows = [
            'service.korean_facial' => [
                ['seed_key' => 'variant.korean_facial.glass_skin', 'name' => 'Glass Skin Ritual', 'slug' => 'glass-skin-facial', 'price' => 1499, 'duration_minutes' => 60],
                ['seed_key' => 'variant.korean_facial.age_rewind', 'name' => 'Age-Rewind Therapy', 'slug' => 'age-rewind-facial', 'price' => 1999, 'duration_minutes' => 75],
            ],
            'service.ice_cream_delight_pedicure' => [
                ['seed_key' => 'variant.ice_cream_pedicure.basic', 'name' => 'Classic Ice-Cream', 'slug' => 'basic', 'price' => 1579, 'duration_minutes' => 60],
                ['seed_key' => 'variant.ice_cream_pedicure.premium', 'name' => 'Magnum Therapy', 'slug' => 'premium', 'price' => 1899, 'duration_minutes' => 75],
            ],
            'service.elite_bridal_makeup' => [
                ['seed_key' => 'variant.bridal_makeup.standard', 'name' => 'Standard Bridal', 'slug' => 'standard-bridal', 'price' => 8999, 'duration_minutes' => 120],
                ['seed_key' => 'variant.bridal_makeup.airbrush', 'name' => 'Airbrush HD', 'slug' => 'airbrush-hd', 'price' => 14999, 'duration_minutes' => 180],
            ],
        ];

        foreach ($rows as $serviceKey => $variants) {
            $service = $services[$serviceKey] ?? null;
            if (!$service) continue;

            foreach ($variants as $index => $row) {
                $variant = ServiceVariant::where('seed_key', $row['seed_key'])
                    ->orWhere(function ($query) use ($service, $row) {
                        $query->where('service_id', $service->id)
                            ->where('slug', $row['slug']);
                    })
                    ->first();

                $data = [
                    'seed_key' => $row['seed_key'],
                    'service_id' => $service->id,
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'description' => $row['name'] . ' premium variant.',
                    'price' => $row['price'],
                    'duration_minutes' => $row['duration_minutes'],
                    'status' => 'Active',
                    'is_default' => $index === 0,
                    'is_bookable' => true,
                    'sort_order' => $index,
                ];

                if ($variant) {
                    $variant->update($data);
                } else {
                    $variant = ServiceVariant::create($data);
                }
            }
        }
    }
}
