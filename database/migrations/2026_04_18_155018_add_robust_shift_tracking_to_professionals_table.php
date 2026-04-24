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
        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'accumulated_seconds_today')) {
                $table->integer('accumulated_seconds_today')->default(0)->after('is_online');
            }
            if (!Schema::hasColumn('professionals', 'last_online_at')) {
                $table->timestamp('last_online_at')->nullable()->after('accumulated_seconds_today');
            }
            if (!Schema::hasColumn('professionals', 'last_reset_at')) {
                $table->timestamp('last_reset_at')->nullable()->after('last_online_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['accumulated_seconds_today', 'last_online_at', 'last_reset_at']);
        });
    }
};
