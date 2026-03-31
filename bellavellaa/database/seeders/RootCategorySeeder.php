<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class RootCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Salon for Women',
                'slug'        => 'salon-for-women',
                'type'        => 'services',
                'sort_order'  => 1,
                'status'      => 'Active',
                'featured'    => true,
                'description' => 'Beauty and grooming services at home.',
            ],
            [
                'name'        => 'Spa for Women',
                'slug'        => 'spa-for-women',
                'type'        => 'services',
                'sort_order'  => 2,
                'status'      => 'Active',
                'featured'    => true,
                'description' => 'Relaxation and wellness treatments at home.',
            ],
            [
                'name'        => 'Hair Studio for Women',
                'slug'        => 'hair-studio-for-women',
                'type'        => 'services',
                'sort_order'  => 3,
                'status'      => 'Active',
                'featured'    => true,
                'description' => 'Expert hair care and styling at home.',
            ],
            [
                'name'        => 'Bride',
                'slug'        => 'bride',
                'type'        => 'packages',
                'sort_order'  => 4,
                'status'      => 'Active',
                'featured'    => true,
                'description' => 'Complete bridal beauty packages.',
            ],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $this->command->info('Root categories seeded: ' . count($categories));
    }
}
