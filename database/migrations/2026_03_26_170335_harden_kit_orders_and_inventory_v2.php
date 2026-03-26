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
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->string('idempotency_key')->nullable()->unique()->after('id');
            $table->json('idempotency_response')->nullable()->after('idempotency_key');
            $table->string('payment_session_id')->nullable()->unique()->after('razorpay_order_id');
            
            // Update status enums to be more granular if needed, but we'll use payment_status and order_status for now
            // and maybe add an internal_status or similar if required.
            // For now, let's just make sure payment_status and order_status cover the new requirements.
        });

        Schema::table('professionals', function (Blueprint $table) {
            $table->integer('kits')->default(0)->after('orders');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->json('meta')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->dropColumn(['idempotency_key', 'idempotency_response', 'payment_session_id']);
        });

        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn('kits');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
};
