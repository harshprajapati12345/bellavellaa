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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('badge')->nullable()->after('status');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('badge')->nullable()->after('status');
            $table->string('subtitle')->nullable()->after('name');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('badge');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['badge', 'subtitle']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('subtitle');
        });
    }
};
