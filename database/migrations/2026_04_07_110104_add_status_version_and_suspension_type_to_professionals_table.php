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
            if (!Schema::hasColumn('professionals', 'status_version')) {
                $table->integer('status_version')->default(1)->after('is_suspended');
            }
            if (!Schema::hasColumn('professionals', 'suspension_type')) {
                $table->enum('suspension_type', ['soft', 'hard'])->default('hard')->after('status_version');
            }
            if (!Schema::hasColumn('professionals', 'suspension_reason')) {
                $table->string('suspension_reason')->nullable()->after('suspension_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['status_version', 'suspension_type', 'suspension_reason']);
        });
    }
};
