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
            $table->string('aadhaar')->nullable()->after('phone');
            $table->string('pan')->nullable()->after('aadhaar');
            $table->string('aadhaar_front')->nullable()->after('pan');
            $table->string('aadhaar_back')->nullable()->after('aadhaar_front');
            $table->string('pan_img')->nullable()->after('aadhaar_back');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['aadhaar', 'pan', 'aadhaar_front', 'aadhaar_back', 'pan_img']);
        });
    }
};
