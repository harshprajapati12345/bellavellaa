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
        Schema::dropIfExists('professional_kits');
        Schema::create('professional_kits', function (Blueprint $table) {
            $table->id();
            $table->uuid('professional_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('qty')->default(0);
            $table->timestamps();
            
            // Assuming professional UUID index
            $table->index('professional_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_kits');
    }
};
