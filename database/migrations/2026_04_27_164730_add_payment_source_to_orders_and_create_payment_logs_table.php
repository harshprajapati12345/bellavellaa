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
            $table->enum('payment_source', ['razorpay', 'upi_qr', 'wallet', 'manual_cash'])->nullable()->after('payment_method');
        });

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('status');
            $table->string('method')->nullable();
            $table->string('source')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('raw_response')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_source');
        });
    }
};
