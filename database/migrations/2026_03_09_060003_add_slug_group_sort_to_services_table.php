<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // slug: nullable for now — backfill script fills it,
            // then a separate migration makes it unique + not null
            $table->string('slug')->nullable()->after('name');

            // FK to service_groups — nullable because Hair Studio services
            // sit directly under a category with no group layer
            $table->foreignId('service_group_id')
                ->nullable()
                ->after('category_id')
                ->constrained('service_groups')
                ->nullOnDelete();

            $table->unsignedInteger('sort_order')->default(0)->after('featured');

            // Composite index for the two main API queries:
            // 1. directServices: category_id WHERE service_group_id IS NULL AND status = Active
            // 2. group services: service_group_id WHERE status = Active
            $table->index(['category_id', 'service_group_id', 'status', 'sort_order'], 'services_cat_group_status_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_cat_group_status_sort_idx');
            $table->dropForeign(['service_group_id']);
            $table->dropColumn(['slug', 'service_group_id', 'sort_order']);
        });
    }
};
