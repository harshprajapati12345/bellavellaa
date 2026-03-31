<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('booking_id')->constrained('services')->nullOnDelete();
            }

            if (!Schema::hasColumn('reviews', 'service_variant_id')) {
                $table->foreignId('service_variant_id')->nullable()->after('service_id')->constrained('service_variants')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'service_variant_id')) {
                $table->dropConstrainedForeignId('service_variant_id');
            }

            if (Schema::hasColumn('reviews', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }
        });
    }
};
