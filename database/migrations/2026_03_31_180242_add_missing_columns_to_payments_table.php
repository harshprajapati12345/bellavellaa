<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('customer_id');
            }

            if (!Schema::hasColumn('payments', 'gateway')) {
                $table->string('gateway')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('payments', 'amount_paise')) {
                $table->bigInteger('amount_paise')->default(0)->after('gateway');
            }

            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency')->default('INR')->after('amount_paise');
            }

            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status')->default('PENDING')->after('currency');
            }

            if (!Schema::hasColumn('payments', 'gateway_payment_id')) {
                $table->string('gateway_payment_id')->nullable()->after('status');
            }

            if (!Schema::hasColumn('payments', 'gateway_order_id')) {
                $table->string('gateway_order_id')->nullable()->after('gateway_payment_id');
            }

            if (!Schema::hasColumn('payments', 'gateway_signature')) {
                $table->string('gateway_signature')->nullable()->after('gateway_order_id');
            }

            if (!Schema::hasColumn('payments', 'meta_json')) {
                $table->json('meta_json')->nullable()->after('gateway_signature');
            }

            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('meta_json');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'gateway',
                'amount_paise',
                'currency',
                'status',
                'gateway_payment_id',
                'gateway_order_id',
                'gateway_signature',
                'meta_json',
                'paid_at'
            ]);
        });
    }
};
