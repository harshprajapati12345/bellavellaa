<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'scan_kit' to the status enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'unassigned',
            'pending',
            'confirmed',
            'assigned',
            'accepted',
            'on_the_way',
            'arrived',
            'scan_kit',
            'in_progress',
            'payment_pending',
            'completed',
            'cancelled',
            'rejected'
        ) NOT NULL DEFAULT 'unassigned'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert by removing 'scan_kit'
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'unassigned',
            'pending',
            'confirmed',
            'assigned',
            'accepted',
            'on_the_way',
            'arrived',
            'in_progress',
            'payment_pending',
            'completed',
            'cancelled',
            'rejected'
        ) NOT NULL DEFAULT 'unassigned'");
    }
};
