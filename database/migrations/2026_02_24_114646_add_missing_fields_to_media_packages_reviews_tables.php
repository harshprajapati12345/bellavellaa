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
        Schema::table('media', function (Blueprint $table) {
            $table->string('linked_section')->nullable()->after('thumbnail');
            $table->string('target_page')->nullable()->after('linked_section');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('desc_title')->nullable()->after('description');
            $table->string('desc_image')->nullable()->after('desc_title');
            $table->text('aftercare_content')->nullable()->after('desc_image');
            $table->string('aftercare_image')->nullable()->after('aftercare_content');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('review_type', ['text', 'video'])->default('text')->after('comment');
            $table->string('video_path')->nullable()->after('review_type');
            $table->integer('points_given')->default(0)->after('video_path');
            $table->boolean('is_featured')->default(false)->after('points_given');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['linked_section', 'target_page']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['desc_title', 'desc_image', 'aftercare_content', 'aftercare_image']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['review_type', 'video_path', 'points_given', 'is_featured']);
        });
    }
};
