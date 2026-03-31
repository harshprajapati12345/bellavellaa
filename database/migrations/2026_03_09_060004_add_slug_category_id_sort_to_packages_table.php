<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // slug: nullable for now — backfill script fills it,
            // then a separate migration makes it unique + not null
            $table->string('slug')->nullable()->after('name');

            // Proper FK replacing the old plain-string 'category' column
            // nullable during migration — backfill script maps old string → FK
            $table->foreignId('category_id')
                ->nullable()
                ->after('category')
                ->constrained('categories')
                ->nullOnDelete();

            $table->unsignedInteger('sort_order')->default(0)->after('featured');

            // Index for common API query: active packages under a category, ordered
            $table->index(['category_id', 'status', 'sort_order'], 'packages_cat_status_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex('packages_cat_status_sort_idx');
            $table->dropForeign(['category_id']);
            $table->dropColumn(['slug', 'category_id', 'sort_order']);
        });
    }
};
