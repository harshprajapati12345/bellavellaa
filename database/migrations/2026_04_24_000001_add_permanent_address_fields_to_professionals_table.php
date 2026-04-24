<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('service_area');
            }

            if (!Schema::hasColumn('professionals', 'permanent_state')) {
                $table->string('permanent_state')->nullable()->after('state');
            }

            if (!Schema::hasColumn('professionals', 'permanent_city')) {
                $table->string('permanent_city')->nullable()->after('city');
            }

            if (!Schema::hasColumn('professionals', 'permanent_pincode')) {
                $table->string('permanent_pincode', 10)->nullable()->after('pincode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('professionals', 'permanent_address') ? 'permanent_address' : null,
                Schema::hasColumn('professionals', 'permanent_state') ? 'permanent_state' : null,
                Schema::hasColumn('professionals', 'permanent_city') ? 'permanent_city' : null,
                Schema::hasColumn('professionals', 'permanent_pincode') ? 'permanent_pincode' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
