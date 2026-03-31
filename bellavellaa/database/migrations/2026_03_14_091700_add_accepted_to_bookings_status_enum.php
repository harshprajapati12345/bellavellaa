<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'Accepted' to the status ENUM (between 'Assigned' and 'Started')
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'Unassigned',
            'Pending',
            'Confirmed',
            'Assigned',
            'Accepted',
            'Started',
            'Arrived',
            'In Progress',
            'Completed',
            'Cancelled'
        ) DEFAULT 'Unassigned'");
    }

    public function down(): void
    {
        // Revert: migrate any 'Accepted' rows back to 'Assigned' first
        DB::statement("UPDATE bookings SET status = 'Assigned' WHERE status = 'Accepted'");

        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'Unassigned',
            'Pending',
            'Confirmed',
            'Assigned',
            'Started',
            'Arrived',
            'In Progress',
            'Completed',
            'Cancelled'
        ) DEFAULT 'Unassigned'");
    }
};
