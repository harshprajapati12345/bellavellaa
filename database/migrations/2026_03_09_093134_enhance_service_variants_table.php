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
        Schema::table('service_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('service_variants', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            if (!Schema::hasColumn('service_variants', 'image')) {
                $table->string('image')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('service_variants', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('image');
            }
            if (!Schema::hasColumn('service_variants', 'status')) {
                $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('duration_minutes');
            }
            
            if (Schema::hasColumn('service_variants', 'price_paise')) {
                $table->dropColumn('price_paise');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_variants', function (Blueprint $table) {
            //
        });
    }
};
