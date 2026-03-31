<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hierarchy_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('placement_type')->index();
            $table->string('media_type')->default('image');
            $table->string('media_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('target_type')->index();
            $table->unsignedBigInteger('target_id')->index();
            $table->string('action_link')->nullable();
            $table->string('button_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('status')->default('Active')->index();
            $table->timestamps();

            $table->index(
                ['placement_type', 'target_type', 'target_id'],
                'hierarchy_banners_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hierarchy_banners');
    }
};
