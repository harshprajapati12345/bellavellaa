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
        // Nullify existing category_ids because they point to the 'categories' table
        \DB::table('kit_products')->update(['category_id' => null]);

        Schema::table('kit_products', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('kit_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kit_products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
    }
};
