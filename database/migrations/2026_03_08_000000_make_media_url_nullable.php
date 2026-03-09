<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Makes media.url and media.thumbnail nullable.
 *
 * This is required because:
 * 1. Seeded media rows may not have a URL yet (admin uploads content later)
 * 2. The URL normalizer may set external (non-local) URLs to null
 * 3. It is semantically correct — a media entry can exist before its file is uploaded
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('url')->nullable()->change();
            $table->string('thumbnail')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Note: this may fail if any url values are null when reverting
            $table->string('url')->nullable(false)->change();
            $table->string('thumbnail')->nullable(false)->change();
        });
    }
};
