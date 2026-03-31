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
        Schema::table('kit_products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('brand')->constrained('categories')->nullOnDelete();
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kit_products', function (Blueprint $table) {
            $table->string('category')->nullable()->after('brand');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
