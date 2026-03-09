<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use Illuminate\Database\Seeder;

class FixUnmappedPackageCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Hair Care'    => 'hair-studio-for-women',
            'Spa & Wellness' => 'spa-for-women',
            'Nail Art'     => 'salon-for-women',
        ];

        foreach ($map as $oldValue => $newSlug) {
            $category = Category::where('slug', $newSlug)->first();
            if ($category) {
                $updated = Package::where('category', $oldValue)
                    ->whereNull('category_id')
                    ->update(['category_id' => $category->id]);
                $this->command->info("Mapped '{$oldValue}' → #{$category->id}: {$updated} package(s)");
            }
        }

        $remaining = Package::whereNull('category_id')->count();
        $this->command->info("Remaining unmapped: {$remaining}");
    }
}
