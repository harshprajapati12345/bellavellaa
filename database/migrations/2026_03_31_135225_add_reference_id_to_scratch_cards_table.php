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
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->string('reference_id')->nullable()->after('customer_id');
            $table->index(['reference_id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->dropColumn(['reference_id']);
        });
    }

};
