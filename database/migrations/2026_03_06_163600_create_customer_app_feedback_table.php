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
        Schema::create('customer_app_feedback', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $blueprint->tinyInteger('rating');
            $blueprint->text('feedback')->nullable();
            $blueprint->string('device_info')->nullable();
            $blueprint->string('app_version')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_app_feedback');
    }
};
