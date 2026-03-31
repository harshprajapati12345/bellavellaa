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
        Schema::table('orders', function (Blueprint $table) {
            // Drop redundant column if exists
            if (Schema::hasColumn('orders', 'discount_paise')) {
                $table->dropColumn('discount_paise');
            }

            if (!Schema::hasColumn('orders', 'subtotal_paise')) {
                $table->bigInteger('subtotal_paise')->after('scheduled_slot')->default(0);
            }
            if (!Schema::hasColumn('orders', 'offer_discount_paise')) {
                $table->bigInteger('offer_discount_paise')->after('subtotal_paise')->default(0);
            }
            if (!Schema::hasColumn('orders', 'payment_discount_paise')) {
                $table->bigInteger('payment_discount_paise')->after('offer_discount_paise')->default(0);
            }
            if (!Schema::hasColumn('orders', 'wallet_discount_paise')) {
                $table->bigInteger('wallet_discount_paise')->after('payment_discount_paise')->default(0);
            }
            if (!Schema::hasColumn('orders', 'total_discount_paise')) {
                $table->bigInteger('total_discount_paise')->after('wallet_discount_paise')->default(0);
            }
            if (!Schema::hasColumn('orders', 'wallet_redeemed_paise')) {
                $table->bigInteger('wallet_redeemed_paise')->after('total_discount_paise')->default(0);
            }
            if (!Schema::hasColumn('orders', 'discount_snapshot')) {
                $table->json('discount_snapshot')->nullable()->after('wallet_redeemed_paise');
            }
            if (!Schema::hasColumn('orders', 'final_payable_paise')) {
                $table->bigInteger('final_payable_paise')->after('total_paise')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'offer_discount_paise',
                'payment_discount_paise',
                'wallet_discount_paise',
                'total_discount_paise',
                'wallet_redeemed_paise',
                'discount_snapshot',
                'final_payable_paise'
            ]);
        });
    }


};

