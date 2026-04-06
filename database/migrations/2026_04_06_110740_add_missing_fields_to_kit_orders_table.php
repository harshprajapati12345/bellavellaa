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
            if (!Schema::hasColumn('kit_orders', 'total_amount')) {
                $table->bigInteger('total_amount')->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('kit_orders', 'status')) {
                $table->enum('status', ['Assigned', 'Pending', 'In Transit', 'Received'])->default('Assigned')->after('total_amount');
            }
            if (!Schema::hasColumn('kit_orders', 'order_status')) {
                $table->enum('order_status', ['Processing', 'Packed', 'Shipped', 'Delivered', 'Cancelled'])->default('Processing')->after('status');
            }
            if (!Schema::hasColumn('kit_orders', 'payment_status')) {
                $table->enum('payment_status', ['Pending', 'Paid', 'Failed'])->default('Pending')->after('order_status');
            }
            if (!Schema::hasColumn('kit_orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('kit_orders', 'payment_id')) {
                $table->string('payment_id')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('kit_orders', 'razorpay_order_id')) {
                $table->string('razorpay_order_id')->nullable()->after('payment_id');
            }
            if (!Schema::hasColumn('kit_orders', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('razorpay_order_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_amount',
                'status',
                'order_status',
                'payment_status',
                'payment_method',
                'payment_id',
                'razorpay_order_id',
                'assigned_at',
            ]);
        });
    }
};
