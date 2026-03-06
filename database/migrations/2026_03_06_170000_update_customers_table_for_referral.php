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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('referral_code', 50)->nullable()->unique()->after('status');
            $table->unsignedBigInteger('referred_by_customer_id')->nullable()->after('referral_code');
            $table->string('referral_code_used', 50)->nullable()->after('referred_by_customer_id');

            $table->foreign('referred_by_customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['referred_by_customer_id']);
            $table->dropColumn(['referral_code', 'referred_by_customer_id', 'referral_code_used']);
        });
    }
};
