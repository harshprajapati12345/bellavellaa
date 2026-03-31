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
        Schema::create('reward_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique()->comment('signup, referrer, referred_user');
            $table->string('title');
            $table->integer('coins')->default(0);
            $table->boolean('status')->default(true);
            $table->integer('max_per_user')->default(0)->comment('0 for unlimited');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_rules');
    }
};
