<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('reviewed_id');
            $table->enum('reviewer_role', ['client', 'professional']);
            $table->enum('reviewed_role', ['client', 'professional']);
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->enum('content_type', ['text', 'video'])->default('text');
            $table->text('video_path')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();

            $table->unique(['booking_id', 'reviewer_id']);
            $table->index(['reviewer_role', 'reviewed_role']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reviews');
    }
};
