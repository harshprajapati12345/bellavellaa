<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'tagline')) {
                $table->string('tagline')->nullable()->after('name');
            }

            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('image');
            }
        });

        Schema::table('service_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('service_groups', 'badge')) {
                $table->string('badge')->nullable()->after('image');
            }
        });

        if (!Schema::hasTable('service_types')) {
            Schema::create('service_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_group_id')->constrained('service_groups')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->timestamps();

                $table->unique(['service_group_id', 'slug']);
                $table->index(['service_group_id', 'status', 'sort_order']);
            });
        }

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'service_type_id')) {
                $table->foreignId('service_type_id')
                    ->nullable()
                    ->after('service_group_id')
                    ->constrained('service_types')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('services', 'short_description')) {
                $table->text('short_description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('services', 'long_description')) {
                $table->longText('long_description')->nullable()->after('short_description');
            }

            if (!Schema::hasColumn('services', 'duration_minutes')) {
                $table->unsignedInteger('duration_minutes')->nullable()->after('duration');
            }

            if (!Schema::hasColumn('services', 'base_price')) {
                $table->decimal('base_price', 10, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('services', 'is_bookable')) {
                $table->boolean('is_bookable')->default(true)->after('has_variants');
            }

            if (!Schema::hasColumn('services', 'allow_direct_booking_with_variants')) {
                $table->boolean('allow_direct_booking_with_variants')->default(false)->after('is_bookable');
            }
        });

        Schema::table('service_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('service_variants', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('service_variants', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('service_variants', 'sku')) {
                $table->string('sku')->nullable()->after('sale_price');
            }

            if (!Schema::hasColumn('service_variants', 'is_bookable')) {
                $table->boolean('is_bookable')->default(true)->after('is_default');
            }
        });

        Schema::table('service_variants', function (Blueprint $table) {
            try {
                $table->unique(['service_id', 'slug'], 'service_variants_service_slug_unique');
            } catch (\Throwable $e) {
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('item_id')->constrained('services')->nullOnDelete();
            }

            if (!Schema::hasColumn('carts', 'service_variant_id')) {
                $table->foreignId('service_variant_id')->nullable()->after('service_id')->constrained('service_variants')->nullOnDelete();
            }

            if (!Schema::hasColumn('carts', 'package_id')) {
                $table->foreignId('package_id')->nullable()->after('service_variant_id')->constrained('packages')->nullOnDelete();
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'sellable_type')) {
                $table->string('sellable_type')->nullable()->after('service_variant_id');
            }

            if (!Schema::hasColumn('bookings', 'sellable_id')) {
                $table->unsignedBigInteger('sellable_id')->nullable()->after('sellable_type');
                $table->index(['sellable_type', 'sellable_id'], 'bookings_sellable_idx');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('item_id')->constrained('services')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'service_variant_id')) {
                $table->foreignId('service_variant_id')->nullable()->after('service_id')->constrained('service_variants')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'package_id')) {
                $table->foreignId('package_id')->nullable()->after('service_variant_id')->constrained('packages')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'sellable_type')) {
                $table->string('sellable_type')->nullable()->after('package_id');
            }

            if (!Schema::hasColumn('order_items', 'sellable_id')) {
                $table->unsignedBigInteger('sellable_id')->nullable()->after('sellable_type');
                $table->index(['sellable_type', 'sellable_id'], 'order_items_sellable_idx');
            }
        });

        DB::table('services')
            ->whereNull('short_description')
            ->update(['short_description' => DB::raw('description')]);

        DB::table('services')
            ->whereNull('long_description')
            ->update(['long_description' => DB::raw('description')]);

        DB::table('services')
            ->whereNull('duration_minutes')
            ->update(['duration_minutes' => DB::raw('duration')]);

        DB::table('services')
            ->whereNull('base_price')
            ->update(['base_price' => DB::raw('price')]);

        DB::table('bookings')
            ->whereNotNull('service_variant_id')
            ->update([
                'sellable_type' => 'variant',
                'sellable_id' => DB::raw('service_variant_id'),
            ]);

        DB::table('bookings')
            ->whereNull('sellable_type')
            ->whereNotNull('service_id')
            ->update([
                'sellable_type' => 'service',
                'sellable_id' => DB::raw('service_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            foreach (['order_items_sellable_idx'] as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Throwable $e) {
                }
            }

            $table->dropConstrainedForeignIdIfExists('service_id');
            $table->dropConstrainedForeignIdIfExists('service_variant_id');
            $table->dropConstrainedForeignIdIfExists('package_id');

            if (Schema::hasColumn('order_items', 'sellable_type')) {
                $table->dropColumn(['sellable_type', 'sellable_id']);
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            foreach (['bookings_sellable_idx'] as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Throwable $e) {
                }
            }

            if (Schema::hasColumn('bookings', 'sellable_type')) {
                $table->dropColumn(['sellable_type', 'sellable_id']);
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropConstrainedForeignIdIfExists('service_id');
            $table->dropConstrainedForeignIdIfExists('service_variant_id');
            $table->dropConstrainedForeignIdIfExists('package_id');
        });

        Schema::table('service_variants', function (Blueprint $table) {
            try {
                $table->dropUnique('service_variants_service_slug_unique');
            } catch (\Throwable $e) {
            }

            $columns = array_filter([
                Schema::hasColumn('service_variants', 'description') ? 'description' : null,
                Schema::hasColumn('service_variants', 'sale_price') ? 'sale_price' : null,
                Schema::hasColumn('service_variants', 'sku') ? 'sku' : null,
                Schema::hasColumn('service_variants', 'is_bookable') ? 'is_bookable' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignIdIfExists('service_type_id');

            $columns = array_filter([
                Schema::hasColumn('services', 'short_description') ? 'short_description' : null,
                Schema::hasColumn('services', 'long_description') ? 'long_description' : null,
                Schema::hasColumn('services', 'duration_minutes') ? 'duration_minutes' : null,
                Schema::hasColumn('services', 'base_price') ? 'base_price' : null,
                Schema::hasColumn('services', 'is_bookable') ? 'is_bookable' : null,
                Schema::hasColumn('services', 'allow_direct_booking_with_variants') ? 'allow_direct_booking_with_variants' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('service_types');

        Schema::table('service_groups', function (Blueprint $table) {
            if (Schema::hasColumn('service_groups', 'badge')) {
                $table->dropColumn('badge');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('categories', 'tagline') ? 'tagline' : null,
                Schema::hasColumn('categories', 'icon') ? 'icon' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
