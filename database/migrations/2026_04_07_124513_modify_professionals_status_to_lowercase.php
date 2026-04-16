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
        // 1. First allow lowercase values and existing values
        DB::statement("ALTER TABLE professionals MODIFY status ENUM('active', 'suspended', 'Active', 'Suspended') NOT NULL DEFAULT 'active'");
        
        // 2. update existing records
        DB::statement("UPDATE professionals SET status = 'active' WHERE status = 'Active'");
        DB::statement("UPDATE professionals SET status = 'suspended' WHERE status = 'Suspended'");
        
        // 3. drop uppercase values
        DB::statement("ALTER TABLE professionals MODIFY status ENUM('active', 'suspended') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE professionals MODIFY status ENUM('Active', 'Suspended') NOT NULL DEFAULT 'Active'");
    }
};
