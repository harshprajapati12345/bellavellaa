<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Standardize section names: replace hyphens with underscores
        DB::table('homepage_contents')->where('section', 'hero-banner')
            ->update(['section' => 'hero_banner']);

        DB::table('homepage_contents')->where('section', 'image-banner')
            ->update(['section' => 'image_banner']);

        // Optional: Remove test/demo sections if needed
        // DB::table('homepage_contents')->whereIn('section', ['sdfasd', 'test', 'harsh'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the standardization
        DB::table('homepage_contents')->where('section', 'hero_banner')
            ->where('name', 'hero_banner')  // Only revert the ones we changed
            ->update(['section' => 'hero-banner']);

        DB::table('homepage_contents')->where('section', 'image_banner')
            ->update(['section' => 'image-banner']);
    }
};
