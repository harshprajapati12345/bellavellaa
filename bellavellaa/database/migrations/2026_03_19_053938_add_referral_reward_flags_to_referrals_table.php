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
        // 1. Professionals Table Updates
        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'coins_balance')) {
                $table->bigInteger('coins_balance')->default(0)->after('rating');
            }
            if (!Schema::hasColumn('professionals', 'total_completed_jobs')) {
                $table->integer('total_completed_jobs')->default(0)->after('coins_balance');
            }
        });

        // 2. Referrals Table Updates
        Schema::table('referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('referrals', 'reward_coins')) {
                $table->bigInteger('reward_coins')->default(500)->after('reward_amount');
            }
            if (!Schema::hasColumn('referrals', 'trigger_type')) {
                $table->string('trigger_type')->default('first_job')->after('reward_coins');
            }
            if (!Schema::hasColumn('referrals', 'is_rewarded')) {
                $table->boolean('is_rewarded')->default(false)->after('trigger_type');
            }
            if (!Schema::hasColumn('referrals', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('updated_at');
            }
        });

        // 3. Coin Transactions Table (MANDATORY for audit trail)
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('coins');
            $table->string('type'); // credit/debit
            $table->string('source'); // referral_bonus
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['source', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn(['reward_coins', 'trigger_type', 'is_rewarded', 'completed_at']);
        });

        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['coins_balance', 'total_completed_jobs']);
        });
    }
};
