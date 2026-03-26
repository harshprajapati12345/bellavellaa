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
        Schema::table('professional_kit_units', function (Blueprint $table) {
            $table->unsignedBigInteger('kit_order_id')->nullable()->after('kit_unit_id');
            
            $table->foreign('kit_order_id')
                  ->references('id')
                  ->on('kit_orders')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_kit_units', function (Blueprint $table) {
            $table->dropForeign(['kit_order_id']);
            $table->dropColumn('kit_order_id');
        });
    }
};
