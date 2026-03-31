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
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code')->index();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn('referred_by');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('referred_by');
        });
    }
};
