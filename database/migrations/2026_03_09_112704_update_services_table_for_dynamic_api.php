<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('services', 'rating_avg')) {
                $table->decimal('rating_avg', 3, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('services', 'review_count')) {
                $table->unsignedInteger('review_count')->default(0)->after('rating_avg');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['sale_price', 'rating_avg', 'review_count']);
        });
    }
};
