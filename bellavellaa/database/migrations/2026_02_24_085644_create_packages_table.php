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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->json('services')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('discount')->default(0);
            $table->integer('duration')->comment('in minutes');
            $table->integer('bookings')->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->boolean('featured')->default(false);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->date('created_at_legacy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
