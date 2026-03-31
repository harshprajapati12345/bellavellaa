<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'light_bill')) {
                $table->string('light_bill')->nullable()->after('certificate_img');
            }
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (Schema::hasColumn('professionals', 'light_bill')) {
                $table->dropColumn('light_bill');
            }
        });
    }
};
