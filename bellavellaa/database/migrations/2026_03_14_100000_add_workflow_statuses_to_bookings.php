<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'On The Way' and 'Payment Pending' to the status ENUM
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'Unassigned',
            'Pending',
            'Confirmed',
            'Assigned',
            'Accepted',
            'On The Way',
            'Arrived',
            'Started',
            'In Progress',
            'Payment Pending',
            'Completed',
            'Cancelled'
        ) DEFAULT 'Unassigned'");
    }

    public function down(): void
    {
        // Revert: move active jobs back to a safe state
        DB::statement("UPDATE bookings SET status = 'Assigned' WHERE status IN ('On The Way', 'Payment Pending')");

        // Note: keeping 'Accepted' as it was added in a previous migration
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
};
