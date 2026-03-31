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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('customers', 'address')) {
                $table->text('address')->nullable()->after('city');
            }
            if (!Schema::hasColumn('customers', 'zip')) {
                $table->string('zip', 10)->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['city', 'address', 'zip']);
        });
    }
};
