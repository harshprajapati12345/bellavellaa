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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('house_number')->nullable()->after('label');
            $table->string('landmark')->nullable()->after('house_number');
            $table->string('phone')->nullable()->after('zip');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['house_number', 'landmark', 'phone']);
        });
    }
};
