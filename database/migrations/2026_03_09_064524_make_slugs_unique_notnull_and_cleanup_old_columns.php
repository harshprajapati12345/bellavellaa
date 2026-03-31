<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PHASE 9 — FINAL CLEANUP MIGRATION
 * Run AFTER verifying everything works end-to-end.
 *
 * What it does:
 *  1. Makes services.slug and packages.slug NOT NULL + UNIQUE
 *  2. Drops legacy columns: services.subcategory, services.category (string),
 *     packages.category (string), packages.services (JSON)
 *
 * ⚠ KEEP THIS FILE UNRUN until you have confirmed:
 *   - All existing services/packages have a non-null unique slug
 *   - All packages have category_id set (not null)
 *   - All package pivot data is in package_service table
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---------- services ----------
        Schema::table('services', function (Blueprint $table) {
            // Make slug non-nullable unique
            $table->string('slug')->nullable(false)->unique()->change();

            // Drop old columns (only if they exist)
            if (Schema::hasColumn('services', 'subcategory')) {
                $table->dropColumn('subcategory');
            }
            // Note: 'category' string column was renamed to category_id FK — drop string version
            if (Schema::hasColumn('services', 'category')) {
                $table->dropColumn('category');
            }
        });

        // ---------- packages ----------
        Schema::table('packages', function (Blueprint $table) {
            // Make slug non-nullable unique
            $table->string('slug')->nullable(false)->unique()->change();

            // Drop old string category column
            if (Schema::hasColumn('packages', 'category')) {
                $table->dropColumn('category');
            }
            // Drop old JSON services array column
            if (Schema::hasColumn('packages', 'services')) {
                $table->dropColumn('services');
            }
        });
    }

    public function down(): void
    {
        // Restore legacy columns
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
            $table->string('subcategory')->nullable();
            $table->string('category')->nullable();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
            $table->string('category')->nullable();
            $table->json('services')->nullable();
        });
    }
};
