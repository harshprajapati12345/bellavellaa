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
            $table->foreignId('homepage_content_id')->nullable()->constrained('homepage_contents')->nullOnDelete();
            if (Schema::hasColumn('media', 'linked_section')) {
                $table->dropColumn('linked_section');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['homepage_content_id']);
            $table->dropColumn('homepage_content_id');
            if (!Schema::hasColumn('media', 'linked_section')) {
                $table->string('linked_section')->nullable();
            }
        });
    }
};
