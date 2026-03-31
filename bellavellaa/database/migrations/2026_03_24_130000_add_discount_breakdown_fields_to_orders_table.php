<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_discount_paise')) {
                $table->unsignedBigInteger('payment_discount_paise')->default(0)->after('discount_paise');
            }

            if (!Schema::hasColumn('orders', 'wallet_discount_paise')) {
                $table->unsignedBigInteger('wallet_discount_paise')->default(0)->after('payment_discount_paise');
            }

            if (!Schema::hasColumn('orders', 'offer_discount_paise')) {
                $table->unsignedBigInteger('offer_discount_paise')->default(0)->after('wallet_discount_paise');
            }

            if (!Schema::hasColumn('orders', 'total_discount_paise')) {
                $table->unsignedBigInteger('total_discount_paise')->default(0)->after('offer_discount_paise');
            }

            if (!Schema::hasColumn('orders', 'discount_snapshot')) {
                $table->json('discount_snapshot')->nullable()->after('total_discount_paise');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'payment_discount_paise',
                'wallet_discount_paise',
                'offer_discount_paise',
                'total_discount_paise',
                'discount_snapshot',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
