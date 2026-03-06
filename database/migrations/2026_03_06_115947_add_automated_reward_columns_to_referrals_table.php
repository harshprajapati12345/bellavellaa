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
        Schema::table('referrals', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->after('referred_type');
            $table->integer('reward_referrer')->default(0)->after('reward_amount');
            $table->integer('reward_user')->default(0)->after('reward_referrer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'reward_referrer', 'reward_user']);
        });
    }
};
