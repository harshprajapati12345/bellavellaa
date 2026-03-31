<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BackfillCategoryArchitectureSeeder extends Seeder
{
    public function run(): void
    {
        $this->backfillServiceSlugs();
        $this->backfillPackageSlugs();
        $this->backfillPackageCategoryIds();
        $this->backfillPackageServicePivot();
    }

    // ─── Step 1: Generate slugs for services ─────────────────────────

    private function backfillServiceSlugs(): void
    {
        $count = 0;
        Service::whereNull('slug')->get()->each(function ($service) use (&$count) {
            $base = Str::slug($service->name);
            $slug = $base;
            $i = 1;
            // Ensure uniqueness with ID suffix fallback
            while (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $service->updateQuietly(['slug' => $slug]);
            $count++;
        });
        $this->command->info("Services: backfilled {$count} slugs.");
    }

    // ─── Step 2: Generate slugs for packages ─────────────────────────

    private function backfillPackageSlugs(): void
    {
        $count = 0;
        Package::whereNull('slug')->get()->each(function ($package) use (&$count) {
            $base = Str::slug($package->name);
            $slug = $base;
            $i = 1;
            while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $package->updateQuietly(['slug' => $slug]);
            $count++;
        });
        $this->command->info("Packages: backfilled {$count} slugs.");
    }

    // ─── Step 3: Map old packages.category string → packages.category_id FK ──

    private function backfillPackageCategoryIds(): void
    {
        // Maps old hardcoded 'category' string values → root category slugs
        $map = [
            'Bridal'   => 'bride',
            'Hair'     => 'hair-studio-for-women',
            'Wellness' => 'spa-for-women',
            'Spa'      => 'spa-for-women',
            'Salon'    => 'salon-for-women',
            'Skincare' => 'salon-for-women',
            'Makeup'   => 'salon-for-women',
            'Nails'    => 'salon-for-women',
        ];

        $mapped = 0;
        foreach ($map as $oldValue => $newSlug) {
            $category = Category::where('slug', $newSlug)->first();
            if (! $category) {
                $this->command->warn("Category not found for slug '{$newSlug}' — skipping '{$oldValue}'.");
                continue;
            }
            $updated = Package::where('category', $oldValue)
                ->whereNull('category_id')
                ->update(['category_id' => $category->id]);
            $mapped += $updated;
        }

        // Log any packages that still have no category_id — needs manual review
        $unmapped = Package::whereNull('category_id')->get(['id', 'name', 'category']);
        if ($unmapped->isNotEmpty()) {
            Log::warning('Packages with unmapped category_id after backfill', $unmapped->toArray());
            $this->command->warn("⚠  {$unmapped->count()} packages have no category_id — check logs!");
        } else {
            $this->command->info("Packages: mapped {$mapped} category_id FKs. No unmapped rows.");
        }
    }

    // ─── Step 4: Backfill package_service pivot from JSON column ─────

    private function backfillPackageServicePivot(): void
    {
        $pivotted = 0;
        $skipped  = 0;

        Package::all()->each(function ($package) use (&$pivotted, &$skipped) {
            // Read from the old JSON column (still present in DB during transition)
            $raw = $package->getRawOriginal('services') ?? null;
            $ids = is_array($raw) ? $raw : json_decode($raw, true);

            if (empty($ids)) {
                $skipped++;
                return;
            }

            // Filter out any IDs that no longer exist (deleted services)
            $validIds = Service::whereIn('id', $ids)->pluck('id')->all();

            if (count($ids) !== count($validIds)) {
                $missing = array_diff($ids, $validIds);
                Log::info("Package #{$package->id}: skipped missing service IDs during pivot backfill", $missing);
            }

            if (! empty($validIds)) {
                $package->services()->sync($validIds);
                $pivotted++;
            }
        });

        $this->command->info("Packages: {$pivotted} pivot-synced, {$skipped} had no services JSON.");
    }
}
