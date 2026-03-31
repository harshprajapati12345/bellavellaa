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
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('category')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->enum('status', ['Active', 'Suspended'])->default('Active');
            $table->enum('verification', ['Verified', 'Pending', 'Rejected'])->default('Pending');
            $table->integer('orders')->default(0);
            $table->decimal('earnings', 15, 2)->default(0);
            $table->integer('commission')->default(15);
            $table->string('experience')->nullable();
            $table->date('joined')->nullable();
            $table->json('services')->nullable();
            $table->boolean('docs')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamps();
            $table->date('created_at_legacy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
