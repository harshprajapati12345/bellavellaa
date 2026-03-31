<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ServiceGroup;
use Illuminate\Database\Seeder;

class ServiceGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Slugs are globally unique: category-slug + group-name
        $groups = [
            [
                'category_slug' => 'salon-for-women',
                'name'          => 'Luxe',
                'slug'          => 'salon-luxe',
                'tag_label'     => 'Premium',
                'description'   => 'Premium luxury salon experience at home.',
                'status'        => 'Active',
                'sort_order'    => 1,
            ],
            [
                'category_slug' => 'salon-for-women',
                'name'          => 'Prime',
                'slug'          => 'salon-prime',
                'tag_label'     => 'Bestseller',
                'description'   => 'Most popular salon services, best value.',
                'status'        => 'Active',
                'sort_order'    => 2,
            ],
            [
                'category_slug' => 'spa-for-women',
                'name'          => 'Ayurveda',
                'slug'          => 'spa-ayurveda',
                'tag_label'     => 'Traditional',
                'description'   => 'Authentic Ayurvedic spa therapies at home.',
                'status'        => 'Active',
                'sort_order'    => 1,
            ],
            [
                'category_slug' => 'spa-for-women',
                'name'          => 'Prime',
                'slug'          => 'spa-prime',
                'tag_label'     => 'Bestseller',
                'description'   => 'Most popular spa treatments, best value.',
                'status'        => 'Active',
                'sort_order'    => 2,
            ],
        ];

        foreach ($groups as $data) {
            $category = Category::where('slug', $data['category_slug'])->first();

            if (! $category) {
                $this->command->warn("Category not found: {$data['category_slug']} — skipping {$data['name']}");
                continue;
            }

            ServiceGroup::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $category->id,
                    'name'        => $data['name'],
                    'slug'        => $data['slug'],
                    'tag_label'   => $data['tag_label'],
                    'description' => $data['description'],
                    'status'      => $data['status'],
                    'sort_order'  => $data['sort_order'],
                ]
            );
        }

        $this->command->info('Service groups seeded: ' . count($groups));
    }
}
