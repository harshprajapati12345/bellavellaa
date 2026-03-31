<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'package_mode')) {
                $table->string('package_mode')->default('hierarchy')->after('tag_label');
            }

            if (!Schema::hasColumn('packages', 'base_price_threshold')) {
                $table->decimal('base_price_threshold', 15, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('packages', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('base_price_threshold');
            }

            if (!Schema::hasColumn('packages', 'discount_value')) {
                $table->decimal('discount_value', 15, 2)->nullable()->after('discount_type');
            }
        });

        Schema::table('package_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('package_groups', 'source_type')) {
                $table->string('source_type')->default('custom')->after('package_id');
            }

            if (!Schema::hasColumn('package_groups', 'linked_type')) {
                $table->string('linked_type')->nullable()->after('source_type');
            }

            if (!Schema::hasColumn('package_groups', 'linked_id')) {
                $table->unsignedBigInteger('linked_id')->nullable()->after('linked_type');
            }
        });

        Schema::table('package_items', function (Blueprint $table) {
            if (!Schema::hasColumn('package_items', 'source_type')) {
                $table->string('source_type')->default('custom')->after('package_group_id');
            }

            if (!Schema::hasColumn('package_items', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('source_type')->constrained('services')->nullOnDelete();
            }

            if (!Schema::hasColumn('package_items', 'custom_price')) {
                $table->decimal('custom_price', 15, 2)->nullable()->after('subtitle');
            }

            if (!Schema::hasColumn('package_items', 'custom_duration_minutes')) {
                $table->unsignedInteger('custom_duration_minutes')->nullable()->after('custom_price');
            }
        });

        DB::table('packages')
            ->whereNull('package_mode')
            ->orWhere('package_mode', '')
            ->update(['package_mode' => DB::raw("CASE WHEN is_configurable = 1 THEN 'manual' ELSE 'hierarchy' END")]);

        DB::table('packages')
            ->whereNull('base_price_threshold')
            ->update(['base_price_threshold' => DB::raw('price')]);

        DB::table('packages')
            ->where(function ($query) {
                $query->whereNull('discount_type')->orWhere('discount_type', '');
            })
            ->update([
                'discount_type' => DB::raw("CASE WHEN COALESCE(discount, 0) > 0 THEN 'percentage' ELSE NULL END"),
            ]);

        DB::table('packages')
            ->whereNull('discount_value')
            ->update(['discount_value' => DB::raw('NULLIF(discount, 0)')]);

        DB::table('package_groups')
            ->whereNull('source_type')
            ->update(['source_type' => 'custom']);

        DB::table('package_items')
            ->whereNull('source_type')
            ->update(['source_type' => 'custom']);
    }

    public function down(): void
    {
        Schema::table('package_items', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('package_items', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }

            foreach (['source_type', 'custom_price', 'custom_duration_minutes'] as $column) {
                if (Schema::hasColumn('package_items', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('package_groups', function (Blueprint $table) {
            $drops = [];
            foreach (['source_type', 'linked_type', 'linked_id'] as $column) {
                if (Schema::hasColumn('package_groups', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('packages', function (Blueprint $table) {
            $drops = [];
            foreach (['package_mode', 'base_price_threshold', 'discount_type', 'discount_value'] as $column) {
                if (Schema::hasColumn('packages', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
