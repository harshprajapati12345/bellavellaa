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
                'description' => 'Salon for Women services.',
                'sort_order' => 1,
                'status' => 'Active',
                'featured' => true,
                'color' => '#F9A8D4',
            ],
            [
                'seed_key' => 'category.spa_for_women',
                'name' => 'Spa for Women',
                'slug' => 'spa-for-women',
                'type' => 'services',
                'tagline' => 'Relaxation, recovery, and rituals',
                'description' => 'Spa for Women services.',
                'sort_order' => 2,
                'status' => 'Active',
                'featured' => true,
                'color' => '#6EE7B7',
            ],
            [
                'seed_key' => 'category.hair_studio_for_women',
                'name' => 'Hair Studio for Women',
                'slug' => 'hair-studio-for-women',
                'type' => 'services',
                'tagline' => 'Cuts, styling, and treatment care',
                'description' => 'Hair Studio for Women services.',
                'sort_order' => 3,
                'status' => 'Active',
                'featured' => true,
                'color' => '#93C5FD',
            ],
            [
                'seed_key' => 'category.bridal',
                'name' => 'Bridal',
                'slug' => 'bridal',
                'type' => 'packages',
                'tagline' => 'Bridal packages and occasion beauty',
                'description' => 'Bridal services.',
                'sort_order' => 4,
                'status' => 'Active',
                'featured' => true,
                'color' => '#FDE68A',
            ],
        ];

        $result = [];
        foreach ($rows as $row) {
            $category = Category::query()
                ->where('seed_key', $row['seed_key'])
                ->orWhere('slug', $row['slug'])
                ->first();

            if (!$category) {
                $category = new Category();
            }

            $category->fill($row);
            $category->save();
            $result[$row['seed_key']] = $category;
        }

        return $result;
    }

    private function seedGroups(array $categories): array
    {
        $rows = [
            [
                'seed_key' => 'group.salon_for_women.luxe',
                'category_key' => 'category.salon_for_women',
                'name' => 'Luxe',
                'slug' => 'salon-for-women-luxe',
                'legacy_slugs' => ['salon-luxe'],
                'description' => 'Premium beauty services for salon appointments at home.',
                'tag_label' => 'Premium',
                'badge' => 'Premium',
                'sort_order' => 1,
                'status' => 'Active',
            ],
            [
                'seed_key' => 'group.salon_for_women.prime',
                'category_key' => 'category.salon_for_women',
                'name' => 'Prime',
                'slug' => 'salon-for-women-prime',
                'legacy_slugs' => ['salon-prime'],
                'description' => 'Everyday value salon services.',
                'tag_label' => 'Affordable',
                'badge' => 'Affordable',
                'sort_order' => 2,
                'status' => 'Active',
            ],
            [
                'seed_key' => 'group.spa_for_women.ayurveda',
                'category_key' => 'category.spa_for_women',
                'name' => 'Ayurveda',
                'slug' => 'spa-for-women-ayurveda',
                'legacy_slugs' => ['spa-ayurveda'],
                'description' => 'Ayurvedic spa rituals and restorative therapies.',
                'tag_label' => 'Holistic',
                'badge' => 'Holistic',
                'sort_order' => 1,
                'status' => 'Active',
            ],
            [
                'seed_key' => 'group.spa_for_women.prime',
                'category_key' => 'category.spa_for_women',
                'name' => 'Prime',
                'slug' => 'spa-for-women-prime',
                'legacy_slugs' => ['spa-prime'],
                'description' => 'Quick spa care for routine wellness.',
                'tag_label' => 'Affordable',
                'badge' => 'Affordable',
                'sort_order' => 2,
                'status' => 'Active',
            ],
        ];

        $result = [];
        foreach ($rows as $row) {
            $group = ServiceGroup::query()
                ->where('seed_key', $row['seed_key'])
                ->orWhere('slug', $row['slug'])
                ->orWhereIn('slug', $row['legacy_slugs'])
                ->first();

            if (!$group) {
                $group = new ServiceGroup();
            }

            $group->fill([
                'seed_key' => $row['seed_key'],
                'category_id' => $categories[$row['category_key']]->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'description' => $row['description'],
                'tag_label' => $row['tag_label'],
                'badge' => $row['badge'],
                'sort_order' => $row['sort_order'],
                'status' => $row['status'],
            ]);
            $group->save();
            $result[$row['seed_key']] = $group;
        }

        return $result;
    }

    private function seedTypes(array $groups): array
    {
        $rows = [
            ['seed_key' => 'type.facials', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Facials', 'slug' => 'facials', 'description' => 'Facials', 'sort_order' => 1],
            ['seed_key' => 'type.cleanup', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Cleanup', 'slug' => 'cleanup', 'description' => 'Cleanup', 'sort_order' => 2],
            ['seed_key' => 'type.threading', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Threading', 'slug' => 'threading', 'description' => 'Threading', 'sort_order' => 3],
            ['seed_key' => 'type.bleach_massage', 'group_key' => 'group.salon_for_women.luxe', 'name' => 'Bleach and Massage', 'slug' => 'bleach-and-massage', 'description' => 'Bleach and Massage', 'sort_order' => 4],
            ['seed_key' => 'type.manicure', 'group_key' => 'group.salon_for_women.prime', 'name' => 'Manicure', 'slug' => 'manicure', 'description' => 'Manicure', 'sort_order' => 1],
            ['seed_key' => 'type.pedicure', 'group_key' => 'group.salon_for_women.prime', 'name' => 'Pedicure', 'slug' => 'pedicure', 'description' => 'Pedicure', 'sort_order' => 2],
            ['seed_key' => 'type.massage', 'group_key' => 'group.spa_for_women.prime', 'name' => 'Massage', 'slug' => 'massage', 'description' => 'Massage', 'sort_order' => 1],
            ['seed_key' => 'type.healing_rituals', 'group_key' => 'group.spa_for_women.ayurveda', 'name' => 'Healing Rituals', 'slug' => 'healing-rituals', 'description' => 'Healing Rituals', 'sort_order' => 1],
        ];

        $result = [];
        foreach ($rows as $row) {
            $type = ServiceType::query()
                ->where('seed_key', $row['seed_key'])
                ->orWhere(function ($query) use ($row, $groups) {
                    $query->where('slug', $row['slug'])
                        ->where('service_group_id', $groups[$row['group_key']]->id);
                })
                ->first();

            if (!$type) {
                $type = new ServiceType();
            }

            $type->fill([
                'seed_key' => $row['seed_key'],
                'service_group_id' => $groups[$row['group_key']]->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'description' => $row['description'],
                'sort_order' => $row['sort_order'],
                'status' => 'Active',
            ]);
            $type->save();
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
                'description' => 'Premium Korean Facial service.',
                'duration_minutes' => 60,
                'base_price' => 999,
                'sale_price' => null,
                'has_variants' => true,
                'is_bookable' => false,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 66,
                'rating_avg' => 4,
                'review_count' => 1,
                'image' => null,
            ],
            [
                'seed_key' => 'service.signature_facial',
                'type_key' => 'type.facials',
                'name' => 'Signature Facial',
                'slug' => 'signature-facial',
                'description' => 'Premium Signature Facial service.',
                'duration_minutes' => 60,
                'base_price' => 1299,
                'sale_price' => 999,
                'has_variants' => false,
                'is_bookable' => true,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 10,
                'rating_avg' => 0,
                'review_count' => 0,
                'image' => null,
            ],
            [
                'seed_key' => 'service.cleanup',
                'type_key' => 'type.cleanup',
                'name' => 'Cleanup',
                'slug' => 'cleanup',
                'description' => 'Premium Cleanup service.',
                'duration_minutes' => 45,
                'base_price' => 599,
                'sale_price' => null,
                'has_variants' => true,
                'is_bookable' => false,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 98,
                'rating_avg' => 3,
                'review_count' => 1,
                'image' => null,
            ],
            [
                'seed_key' => 'service.threading',
                'type_key' => 'type.threading',
                'name' => 'Threading',
                'slug' => 'threading',
                'description' => 'Premium Threading service.',
                'duration_minutes' => 20,
                'base_price' => 30,
                'sale_price' => null,
                'has_variants' => true,
                'is_bookable' => false,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 18,
                'rating_avg' => 0,
                'review_count' => 0,
                'image' => null,
            ],
            [
                'seed_key' => 'service.bleach_detan_massage',
                'type_key' => 'type.bleach_massage',
                'name' => 'Bleach, Detan & Massage',
                'slug' => 'bleach-detan-massage',
                'description' => 'Premium Bleach, Detan & Massage service.',
                'duration_minutes' => 30,
                'base_price' => 399,
                'sale_price' => null,
                'has_variants' => true,
                'is_bookable' => false,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 92,
                'rating_avg' => 0,
                'review_count' => 0,
                'image' => null,
            ],
            [
                'seed_key' => 'service.ice_cream_delight_manicure',
                'type_key' => 'type.manicure',
                'name' => 'Ice Cream Delight Manicure',
                'slug' => 'ice-cream-delight-manicure',
                'description' => 'A creamy, strawberry-infused retreat to soften skin and refresh tired hands.',
                'duration_minutes' => 60,
                'base_price' => 1299,
                'sale_price' => null,
                'has_variants' => false,
                'is_bookable' => true,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 41,
                'rating_avg' => 4.86,
                'review_count' => 4000,
                'image' => null,
            ],
            [
                'seed_key' => 'service.ice_cream_delight_pedicure',
                'type_key' => 'type.pedicure',
                'name' => 'Ice Cream Delight Pedicure',
                'slug' => 'ice-cream-delight-pedicure',
                'description' => 'A creamy, strawberry-infused retreat to soften skin and refresh tired feet.',
                'duration_minutes' => 60,
                'base_price' => 1579,
                'sale_price' => null,
                'has_variants' => true,
                'is_bookable' => false,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 39,
                'rating_avg' => 4.86,
                'review_count' => 13000,
                'image' => null,
            ],
            [
                'seed_key' => 'service.swedish_massage',
                'type_key' => 'type.massage',
                'name' => 'Swedish Massage',
                'slug' => 'swedish-massage',
                'description' => 'Relaxing full body massage for everyday stress relief.',
                'duration_minutes' => 60,
                'base_price' => 1499,
                'sale_price' => null,
                'has_variants' => false,
                'is_bookable' => true,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 55,
                'rating_avg' => 4.5,
                'review_count' => 27,
                'image' => null,
            ],
            [
                'seed_key' => 'service.abhyanga',
                'type_key' => 'type.healing_rituals',
                'name' => 'Abhyanga Ritual',
                'slug' => 'abhyanga-ritual',
                'description' => 'Warm herbal oil massage rooted in ayurvedic practice.',
                'duration_minutes' => 75,
                'base_price' => 1899,
                'sale_price' => null,
                'has_variants' => false,
                'is_bookable' => true,
                'allow_direct_booking_with_variants' => false,
                'bookings' => 22,
                'rating_avg' => 4.7,
                'review_count' => 12,
                'image' => null,
            ],
        ];

        $result = [];
        foreach ($rows as $row) {
            $service = Service::query()
                ->where('seed_key', $row['seed_key'])
                ->orWhere('slug', $row['slug'])
                ->first();

            if (!$service) {
                $service = new Service();
            }

            $type = $types[$row['type_key']];
            $group = $type->serviceGroup;
            $category = $group->category;

            $service->fill([
                'seed_key' => $row['seed_key'],
                'category_id' => $category->id,
                'service_group_id' => $group->id,
                'service_type_id' => $type->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'short_description' => $row['description'],
                'long_description' => $row['description'],
                'description' => $row['description'],
                'duration' => $row['duration_minutes'],
                'duration_minutes' => $row['duration_minutes'],
                'price' => $row['base_price'],
                'base_price' => $row['base_price'],
                'sale_price' => $row['sale_price'],
                'has_variants' => $row['has_variants'],
                'is_bookable' => $row['is_bookable'],
                'allow_direct_booking_with_variants' => $row['allow_direct_booking_with_variants'],
                'status' => 'Active',
                'featured' => true,
                'sort_order' => 0,
                'bookings' => $row['bookings'],
                'rating_avg' => $row['rating_avg'],
                'review_count' => $row['review_count'],
                'image' => $service->image ?: $row['image'],
            ]);
            $service->save();
            $result[$row['seed_key']] = $service;
        }

        return $result;
    }

    private function seedVariants(array $services): void
    {
        $rows = [
            'service.korean_facial' => [
                ['seed_key' => 'variant.korean_facial.glass_skin', 'name' => 'Glass Skin Facial', 'slug' => 'glass-skin-facial', 'price' => 1499, 'duration_minutes' => 60],
                ['seed_key' => 'variant.korean_facial.age_rewind', 'name' => 'Age-Rewind Facial', 'slug' => 'age-rewind-facial', 'price' => 1999, 'duration_minutes' => 75],
            ],
            'service.cleanup' => [
                ['seed_key' => 'variant.cleanup.detox_cleanup', 'name' => 'Detox & Cleanup', 'slug' => 'detox-cleanup', 'price' => 599, 'duration_minutes' => 45],
                ['seed_key' => 'variant.cleanup.casmara_charcoal', 'name' => 'Casmara Charcoal', 'slug' => 'casmara-charcoal', 'price' => 899, 'duration_minutes' => 45],
            ],
            'service.threading' => [
                ['seed_key' => 'variant.threading.eyebrows', 'name' => 'Eyebrows', 'slug' => 'eyebrows', 'price' => 30, 'duration_minutes' => 5],
                ['seed_key' => 'variant.threading.forehead', 'name' => 'Forehead', 'slug' => 'forehead', 'price' => 30, 'duration_minutes' => 5],
                ['seed_key' => 'variant.threading.upper_lip', 'name' => 'Upper Lip', 'slug' => 'upper-lip', 'price' => 20, 'duration_minutes' => 5],
                ['seed_key' => 'variant.threading.chin', 'name' => 'Chin', 'slug' => 'chin', 'price' => 20, 'duration_minutes' => 5],
                ['seed_key' => 'variant.threading.full_face', 'name' => 'Full Face', 'slug' => 'full-face', 'price' => 120, 'duration_minutes' => 20],
            ],
            'service.bleach_detan_massage' => [
                ['seed_key' => 'variant.bleach_detan_massage.full_face_bleach', 'name' => 'Full Face Bleach', 'slug' => 'full-face-bleach', 'price' => 299, 'duration_minutes' => 20],
                ['seed_key' => 'variant.bleach_detan_massage.full_back_detan', 'name' => 'Full Back Detan', 'slug' => 'full-back-detan', 'price' => 499, 'duration_minutes' => 30],
                ['seed_key' => 'variant.bleach_detan_massage.head_massage', 'name' => 'Head Massage (20m)', 'slug' => 'head-massage-20m', 'price' => 199, 'duration_minutes' => 20],
            ],
            'service.ice_cream_delight_pedicure' => [
                ['seed_key' => 'variant.ice_cream_delight_pedicure.basic', 'name' => 'Basic', 'slug' => 'basic', 'price' => 1579, 'duration_minutes' => 60],
                ['seed_key' => 'variant.ice_cream_delight_pedicure.premium', 'name' => 'Premium', 'slug' => 'premium', 'price' => 1899, 'duration_minutes' => 75],
            ],
        ];

        foreach ($rows as $serviceKey => $variants) {
            $service = $services[$serviceKey] ?? null;
            if (!$service) {
                continue;
            }

            foreach ($variants as $index => $row) {
                $variant = ServiceVariant::query()
                    ->where('seed_key', $row['seed_key'])
                    ->orWhere(function ($query) use ($service, $row) {
                        $query->where('service_id', $service->id)
                            ->where('slug', $row['slug']);
                    })
                    ->first();

                if (!$variant) {
                    $variant = new ServiceVariant();
                }

                $variant->fill([
                    'service_id' => $service->id,
                    'seed_key' => $row['seed_key'],
                    'name' => $row['name'],
                    'slug' => $this->uniqueVariantSlug($service, $row['slug'], $variant->id),
                    'description' => $row['name'],
                    'price' => $row['price'],
                    'sale_price' => null,
                    'duration_minutes' => $row['duration_minutes'],
                    'status' => 'Active',
                    'is_default' => $index === 0,
                    'is_bookable' => true,
                    'sort_order' => $index,
                ]);
                $variant->save();
            }
        }
    }

    private function uniqueVariantSlug(Service $service, string $slug, ?int $ignoreId = null): string
    {
        $candidate = Str::slug($slug);
        $suffix = 1;

        while (
            ServiceVariant::query()
                ->where('service_id', $service->id)
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = Str::slug($slug) . '-' . $suffix++;
        }

        return $candidate;
    }
}
