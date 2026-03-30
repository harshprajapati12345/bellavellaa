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
            $table->string('aadhaar_status')->default('pending');
            $table->string('pan_status')->default('pending');
            $table->string('light_bill_status')->default('pending');
            $table->string('bank_proof_status')->default('pending');
            $table->string('upi_status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn([
                'aadhaar_status', 
                'pan_status', 
                'light_bill_status', 
                'bank_proof_status', 
                'upi_status'
            ]);
        });
    }
};
