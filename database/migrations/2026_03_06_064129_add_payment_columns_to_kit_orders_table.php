<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->default(0)->after('quantity');
            $table->string('payment_id')->nullable()->after('total_amount');
            $table->string('razorpay_order_id')->nullable()->after('payment_id');
            $table->enum('payment_status', ['Pending', 'Paid', 'Failed'])->default('Pending')->after('razorpay_order_id');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->enum('order_status', ['Processing', 'Packed', 'Shipped', 'Delivered', 'Cancelled'])->default('Processing')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_amount',
                'payment_id',
                'razorpay_order_id',
                'payment_status',
                'payment_method',
                'order_status',
            ]);
        });
    }
};
