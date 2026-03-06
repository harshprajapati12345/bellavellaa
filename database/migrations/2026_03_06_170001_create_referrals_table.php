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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_customer_id');
            $table->unsignedBigInteger('referred_customer_id');
            $table->string('referral_code_used', 50);
            $table->string('status')->default('pending'); // pending, rewarded
            $table->integer('reward_coins')->default(0);
            $table->timestamp('reward_given_at')->nullable();
            $table->timestamps();

            $table->foreign('referrer_customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('referred_customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
