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
            $table->unsignedBigInteger('referrer_id')->comment('Professional ID');
            $table->unsignedBigInteger('referred_id')->nullable()->comment('Newly signed up User/Professional ID');
            $table->string('referred_type')->nullable()->comment('professional, client');
            $table->string('referred_phone')->index();
            $table->enum('status', ['pending', 'success', 'expired'])->default('pending');
            $table->bigInteger('reward_amount')->default(0);
            $table->string('reward_type')->default('cash')->comment('cash, coin');
            $table->timestamps();

            $table->index(['referrer_id', 'status']);
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
