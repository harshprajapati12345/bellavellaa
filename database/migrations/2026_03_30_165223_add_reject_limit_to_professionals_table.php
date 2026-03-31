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
            if (!Schema::hasColumn('professionals', 'reject_count')) {
                $table->integer('reject_count')->default(0);
            }
            if (!Schema::hasColumn('professionals', 'last_reset_date')) {
                $table->date('last_reset_date')->nullable();
            }
            if (!Schema::hasColumn('professionals', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['reject_count', 'last_reset_date', 'is_suspended']);
        });
    }
};
