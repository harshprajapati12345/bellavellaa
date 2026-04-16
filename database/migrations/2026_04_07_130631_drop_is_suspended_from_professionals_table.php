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
        // 1. Drop Triggers first (as they depend on the column)
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS trg_professionals_consistency_update');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS trg_professionals_consistency_insert');

        Schema::table('professionals', function (Blueprint $table) {
            if (Schema::hasColumn('professionals', 'is_suspended')) {
                $table->dropColumn('is_suspended');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->boolean('is_suspended')->default(false)->after('status');
        });
    }
};
