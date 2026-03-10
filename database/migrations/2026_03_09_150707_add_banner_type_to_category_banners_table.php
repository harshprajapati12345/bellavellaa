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
        Schema::table('category_banners', function (Blueprint $table) {
            $table->enum('banner_type', ['slider', 'promo'])->default('slider')->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_banners', function (Blueprint $table) {
            $table->dropColumn('banner_type');
        });
    }
};
