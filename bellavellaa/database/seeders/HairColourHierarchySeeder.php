<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HairColourHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()
            ->where('seed_key', 'category.hair_studio_for_women')
            ->orWhere('slug', 'hair-studio-for-women')
            ->first();

        if (!$category) {
            $this->command?->warn('Hair Studio for Women category not found. Run ServiceHierarchySeeder first.');
            return;
        }

        $group = ServiceGroup::query()->firstOrNew([
            'seed_key' => 'group.hair_studio_for_women.hair_services',
        ]);

        $group->fill([
            'category_id' => $category->id,
            'name' => 'Hair Services',
            'slug' => 'hair-services',
            'description' => 'Hair colour, styling, treatment, and transformation services.',
            'tag_label' => 'Professional',
            'badge' => 'Professional',
            'sort_order' => 1,
            'status' => 'Active',
        ]);
        $group->save();

        $types = $this->seedTypes($group);
        $services = $this->seedServices($category, $group, $types);
        $this->seedVariants($services);
    }

    private function seedTypes(ServiceGroup $group): array
    {
        $rows = [
            ['seed_key' => 'type.hair_services.blow_dry_styling', 'name' => 'Blow-Dry & Styling', 'slug' => 'blow-dry-and-styling', 'sort_order' => 1],
            ['seed_key' => 'type.hair_services.cut_trim', 'name' => 'Cut & Trim', 'slug' => 'cut-and-trim', 'sort_order' => 2],
            ['seed_key' => 'type.hair_services.hair_care', 'name' => 'Hair Care', 'slug' => 'hair-care', 'sort_order' => 3],
            ['seed_key' => 'type.hair_services.keratin_botox', 'name' => 'Keratin & Botox', 'slug' => 'keratin-and-botox', 'sort_order' => 4],
            ['seed_key' => 'type.hair_services.hair_colour', 'name' => 'Hair Colour', 'slug' => 'hair-colour', 'sort_order' => 5],
            ['seed_key' => 'type.hair_services.fashion_colour', 'name' => 'Fashion Colour', 'slug' => 'fashion-colour', 'sort_order' => 6],
        ];

        $result = [];
        foreach ($rows as $row) {
            $type = ServiceType::query()
                ->where('seed_key', $row['seed_key'])
                ->orWhere(function ($query) use ($group, $row) {
                    $query->where('service_group_id', $group->id)
                        ->where('slug', $row['slug']);
                })
                ->first();

            if (!$type) {
                $type = new ServiceType();
            }

            $type->fill([
                'seed_key' => $row['seed_key'],
                'service_group_id' => $group->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'description' => $row['name'],
                'sort_order' => $row['sort_order'],
                'status' => 'Active',
            ]);
            $type->save();
            $result[$row['seed_key']] = $type;
        }

        return $result;
    }

    private function seedServices(Category $category, ServiceGroup $group, array $types): array
    {
        $rows = [
            ['seed_key' => 'service.hair_colour.loreal_inoa_root_touch_up', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'L\'Oréal Inoa Root Touch-Up', 'slug' => 'loreal-inoa-root-touch-up', 'duration_minutes' => 75, 'base_price' => 1499],
            ['seed_key' => 'service.hair_colour.loreal_majirel_root_touch_up', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'L\'Oréal Majirel Root Touch-Up', 'slug' => 'loreal-majirel-root-touch-up', 'duration_minutes' => 75, 'base_price' => 1599],
            ['seed_key' => 'service.hair_colour.loreal_majirel_global_colour', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'L\'Oréal Majirel Global Colour', 'slug' => 'loreal-majirel-global-colour', 'duration_minutes' => 120, 'base_price' => 2499],
            ['seed_key' => 'service.hair_colour.loreal_inoa_global_colour', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'L\'Oréal Inoa Global Colour', 'slug' => 'loreal-inoa-global-colour', 'duration_minutes' => 120, 'base_price' => 2699],
            ['seed_key' => 'service.hair_colour.loreal_dia_richesse', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'L\'Oréal Dia Richesse', 'slug' => 'loreal-dia-richesse', 'duration_minutes' => 105, 'base_price' => 2299],
            ['seed_key' => 'service.hair_colour.schwarzkopf_igora_royal', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'Schwarzkopf Igora Royal Colour', 'slug' => 'schwarzkopf-igora-royal-colour', 'duration_minutes' => 120, 'base_price' => 2599],
            ['seed_key' => 'service.hair_colour.wella_koleston_perfect', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'Wella Koleston Perfect Colour', 'slug' => 'wella-koleston-perfect-colour', 'duration_minutes' => 120, 'base_price' => 2499],
            ['seed_key' => 'service.hair_colour.matrix_socolor', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'Matrix SoColor Hair Colour', 'slug' => 'matrix-socolor-hair-colour', 'duration_minutes' => 110, 'base_price' => 2199],
            ['seed_key' => 'service.hair_colour.loreal_majirel_highlights', 'type_key' => 'type.hair_services.hair_colour', 'name' => 'L\'Oréal Majirel Highlights', 'slug' => 'loreal-majirel-highlights', 'duration_minutes' => 150, 'base_price' => 3299],
            ['seed_key' => 'service.fashion_colour.balayage', 'type_key' => 'type.hair_services.fashion_colour', 'name' => 'Balayage Hair Colour', 'slug' => 'balayage-hair-colour', 'duration_minutes' => 180, 'base_price' => 4499],
            ['seed_key' => 'service.fashion_colour.ombre', 'type_key' => 'type.hair_services.fashion_colour', 'name' => 'Ombre Hair Colour', 'slug' => 'ombre-hair-colour', 'duration_minutes' => 180, 'base_price' => 4299],
            ['seed_key' => 'service.fashion_colour.global_fashion_colour', 'type_key' => 'type.hair_services.fashion_colour', 'name' => 'Global Fashion Colour', 'slug' => 'global-fashion-colour', 'duration_minutes' => 165, 'base_price' => 3999],
        ];

        $result = [];
        foreach ($rows as $index => $row) {
            $service = Service::query()
                ->where('seed_key', $row['seed_key'])
                ->orWhere('slug', $row['slug'])
                ->first();

            if (!$service) {
                $service = new Service();
            }

            $type = $types[$row['type_key']];
            $description = $row['name'] . ' with professional salon-grade application and personalised consultation.';

            $service->fill([
                'seed_key' => $row['seed_key'],
                'category_id' => $category->id,
                'service_group_id' => $group->id,
                'service_type_id' => $type->id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'short_description' => $description,
                'long_description' => $description,
                'description' => $description,
                'duration' => $row['duration_minutes'],
                'duration_minutes' => $row['duration_minutes'],
                'price' => $row['base_price'],
                'base_price' => $row['base_price'],
                'sale_price' => null,
                'has_variants' => true,
                'is_bookable' => false,
                'allow_direct_booking_with_variants' => false,
                'status' => 'Active',
                'featured' => true,
                'sort_order' => $index + 1,
                'bookings' => 0,
                'rating_avg' => 0,
                'review_count' => 0,
            ]);
            $service->save();
            $result[$row['seed_key']] = $service;
        }

        return $result;
    }

    private function seedVariants(array $services): void
    {
        $shadeRows = [
            ['name' => 'Mahogany Ash Brown', 'slug' => 'mahogany-ash-brown', 'price_delta' => 0],
            ['name' => 'Light Golden Mahogany Brown', 'slug' => 'light-golden-mahogany-brown', 'price_delta' => 50],
            ['name' => 'Chocolate Brown', 'slug' => 'chocolate-brown', 'price_delta' => 0],
            ['name' => 'Dark Chocolate Brown', 'slug' => 'dark-chocolate-brown', 'price_delta' => 100],
            ['name' => 'Golden Brown', 'slug' => 'golden-brown', 'price_delta' => 0],
            ['name' => 'Honey Brown', 'slug' => 'honey-brown', 'price_delta' => 100],
            ['name' => 'Caramel Brown', 'slug' => 'caramel-brown', 'price_delta' => 150],
            ['name' => 'Chestnut Brown', 'slug' => 'chestnut-brown', 'price_delta' => 100],
            ['name' => 'Copper Brown', 'slug' => 'copper-brown', 'price_delta' => 150],
            ['name' => 'Burgundy Red', 'slug' => 'burgundy-red', 'price_delta' => 200],
            ['name' => 'Wine Red', 'slug' => 'wine-red', 'price_delta' => 200],
            ['name' => 'Cherry Red', 'slug' => 'cherry-red', 'price_delta' => 200],
            ['name' => 'Deep Violet', 'slug' => 'deep-violet', 'price_delta' => 250],
            ['name' => 'Plum Violet', 'slug' => 'plum-violet', 'price_delta' => 250],
            ['name' => 'Ash Blonde', 'slug' => 'ash-blonde', 'price_delta' => 300],
            ['name' => 'Golden Blonde', 'slug' => 'golden-blonde', 'price_delta' => 300],
            ['name' => 'Champagne Blonde', 'slug' => 'champagne-blonde', 'price_delta' => 350],
            ['name' => 'Pearl Blonde', 'slug' => 'pearl-blonde', 'price_delta' => 350],
            ['name' => 'Silver Grey', 'slug' => 'silver-grey', 'price_delta' => 400],
            ['name' => 'Smoky Grey', 'slug' => 'smoky-grey', 'price_delta' => 400],
        ];

        foreach ($services as $serviceKey => $service) {
            foreach ($shadeRows as $index => $shade) {
                $seedKey = 'variant.' . Str::after($serviceKey, 'service.') . '.' . Str::replace('-', '_', $shade['slug']);

                $variant = ServiceVariant::query()
                    ->where('seed_key', $seedKey)
                    ->orWhere(function ($query) use ($service, $shade) {
                        $query->where('service_id', $service->id)
                            ->where('slug', $shade['slug']);
                    })
                    ->first();

                if (!$variant) {
                    $variant = new ServiceVariant();
                }

                $variant->fill([
                    'service_id' => $service->id,
                    'seed_key' => $seedKey,
                    'name' => $shade['name'],
                    'slug' => $this->uniqueVariantSlug($service, $shade['slug'], $variant->id),
                    'description' => $shade['name'],
                    'price' => ($service->base_price ?? $service->price ?? 0) + $shade['price_delta'],
                    'sale_price' => null,
                    'duration_minutes' => $service->duration_minutes ?? $service->duration ?? 90,
                    'status' => 'Active',
                    'is_default' => $index === 0,
                    'is_bookable' => true,
                    'sort_order' => $index + 1,
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
