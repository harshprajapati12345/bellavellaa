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
        $hasIsOnline = Schema::hasColumn('professionals', 'is_online');
        $hasLastSeen = Schema::hasColumn('professionals', 'last_seen');

        if ($hasIsOnline && $hasLastSeen) {
            return;
        }

        Schema::table('professionals', function (Blueprint $table) use ($hasIsOnline, $hasLastSeen) {
            if (!$hasIsOnline) {
                $table->boolean('is_online')->default(false)->after('working_hours');
            }

            if (!$hasLastSeen) {
                $table->timestamp('last_seen')->nullable()->after($hasIsOnline ? 'is_online' : 'working_hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dropColumns = [];

        if (Schema::hasColumn('professionals', 'last_seen')) {
            $dropColumns[] = 'last_seen';
        }

        if (Schema::hasColumn('professionals', 'is_online')) {
            $dropColumns[] = 'is_online';
        }

        if ($dropColumns === []) {
            return;
        }

        Schema::table('professionals', function (Blueprint $table) use ($dropColumns) {
            $table->dropColumn($dropColumns);
        });
    }
};
