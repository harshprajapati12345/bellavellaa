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
            if (!Schema::hasColumn('professionals', 'gender')) {
                $table->string('gender')->nullable()->after('city');
            }
            if (!Schema::hasColumn('professionals', 'dob')) {
                $table->date('dob')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('professionals', 'bio')) {
                $table->text('bio')->nullable()->after('dob');
            }
            if (!Schema::hasColumn('professionals', 'languages')) {
                $table->json('languages')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('professionals', 'service_area')) {
                $table->string('service_area')->nullable()->after('languages');
            }
            if (!Schema::hasColumn('professionals', 'service_radius')) {
                $table->decimal('service_radius', 8, 2)->default(10)->after('service_area');
            }
            if (!Schema::hasColumn('professionals', 'payout')) {
                $table->json('payout')->nullable()->after('service_radius');
            }
            if (!Schema::hasColumn('professionals', 'portfolio')) {
                $table->json('portfolio')->nullable()->after('payout');
            }
            if (!Schema::hasColumn('professionals', 'working_hours')) {
                $table->json('working_hours')->nullable()->after('portfolio');
            }
            if (!Schema::hasColumn('professionals', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('working_hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['gender', 'dob', 'bio', 'languages', 'service_area', 'service_radius', 'payout', 'portfolio', 'working_hours']);
        });
    }
};
