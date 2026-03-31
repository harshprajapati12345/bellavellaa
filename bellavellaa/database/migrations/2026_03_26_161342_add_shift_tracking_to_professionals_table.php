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
            if (!Schema::hasColumn('professionals', 'shift_end_time')) {
                $table->timestamp('shift_end_time')->nullable()->after('last_seen');
            }
            if (!Schema::hasColumn('professionals', 'shift_duration')) {
                $table->integer('shift_duration')->default(240)->after('shift_end_time'); // Default 4 hours
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['shift_end_time', 'shift_duration']);
        });
    }
};
