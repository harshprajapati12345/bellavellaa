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
            if (!Schema::hasColumn('packages', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }

            if (!Schema::hasColumn('packages', 'tag_label')) {
                $table->string('tag_label')->nullable()->after('short_description');
            }

            if (!Schema::hasColumn('packages', 'pricing_rule')) {
                $table->string('pricing_rule')->default('sum_selected_options')->after('tag_label');
            }

            if (!Schema::hasColumn('packages', 'duration_rule')) {
                $table->string('duration_rule')->default('sum_selected_options')->after('pricing_rule');
            }

            if (!Schema::hasColumn('packages', 'is_configurable')) {
                $table->boolean('is_configurable')->default(false)->after('duration_rule');
            }

            if (!Schema::hasColumn('packages', 'quantity_allowed')) {
                $table->boolean('quantity_allowed')->default(true)->after('is_configurable');
            }
        });

        if (!Schema::hasTable('package_contexts')) {
            Schema::create('package_contexts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->string('context_type');
                $table->unsignedBigInteger('context_id');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['package_id', 'context_type', 'context_id'], 'pkg_context_unique');
                $table->index(['context_type', 'context_id', 'sort_order'], 'pkg_context_lookup_idx');
            });
        }

        if (!Schema::hasTable('package_groups')) {
            Schema::create('package_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['package_id', 'sort_order']);
            });
        }

        if (!Schema::hasTable('package_items')) {
            Schema::create('package_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_group_id')->constrained('package_groups')->cascadeOnDelete();
                $table->string('name');
                $table->string('subtitle')->nullable();
                $table->boolean('is_required')->default(false);
                $table->boolean('is_default_selected')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['package_group_id', 'sort_order']);
            });
        }

        if (!Schema::hasTable('package_item_options')) {
            Schema::create('package_item_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_item_id')->constrained('package_items')->cascadeOnDelete();
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
                $table->foreignId('service_variant_id')->nullable()->constrained('service_variants')->nullOnDelete();
                $table->string('name');
                $table->string('subtitle')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->unsignedInteger('duration_minutes')->default(0);
                $table->boolean('is_default')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['package_item_id', 'sort_order'], 'pkg_item_option_sort_idx');
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'meta')) {
                $table->json('meta')->nullable()->after('notes');
            }
        });

        DB::table('packages')
            ->whereNotNull('description')
            ->whereNull('short_description')
            ->update(['short_description' => DB::raw('description')]);

        $now = now();
        $rows = DB::table('packages')
            ->whereNotNull('category_id')
            ->select('id', 'category_id')
            ->get()
            ->map(fn ($row) => [
                'package_id' => $row->id,
                'context_type' => 'category',
                'context_id' => $row->category_id,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($rows !== []) {
            DB::table('package_contexts')->upsert(
                $rows,
                ['package_id', 'context_type', 'context_id'],
                ['sort_order', 'updated_at']
            );
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'meta')) {
                $table->dropColumn('meta');
            }
        });

        Schema::dropIfExists('package_item_options');
        Schema::dropIfExists('package_items');
        Schema::dropIfExists('package_groups');
        Schema::dropIfExists('package_contexts');

        Schema::table('packages', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('packages', 'short_description') ? 'short_description' : null,
                Schema::hasColumn('packages', 'tag_label') ? 'tag_label' : null,
                Schema::hasColumn('packages', 'pricing_rule') ? 'pricing_rule' : null,
                Schema::hasColumn('packages', 'duration_rule') ? 'duration_rule' : null,
                Schema::hasColumn('packages', 'is_configurable') ? 'is_configurable' : null,
                Schema::hasColumn('packages', 'quantity_allowed') ? 'quantity_allowed' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
