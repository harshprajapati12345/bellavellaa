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
            // Add new fields
            $table->date('date_of_birth')->nullable()->after('avatar');
            
            // Remove address-related fields from customers table
            $table->dropColumn(['city', 'zip', 'address']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Reverse the changes
            $table->dropColumn(['date_of_birth']);
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->text('address')->nullable();
        });
    }
};
