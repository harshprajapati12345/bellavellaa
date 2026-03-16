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
        // 1. Temporarily change column to VARCHAR to allow any string
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'unassigned'");

        // 2. Map existing Title Case statuses to lowercase snake_case
        DB::statement("UPDATE bookings SET status = 'unassigned' WHERE status = 'Unassigned'");
        DB::statement("UPDATE bookings SET status = 'pending' WHERE status = 'Pending'");
        DB::statement("UPDATE bookings SET status = 'confirmed' WHERE status = 'Confirmed'");
        DB::statement("UPDATE bookings SET status = 'assigned' WHERE status = 'Assigned'");
        DB::statement("UPDATE bookings SET status = 'accepted' WHERE status = 'Accepted'");
        DB::statement("UPDATE bookings SET status = 'on_the_way' WHERE status = 'On The Way'");
        DB::statement("UPDATE bookings SET status = 'arrived' WHERE status = 'Arrived'");
        // Handle both 'Started' and 'In Progress' to map to 'in_progress'
        DB::statement("UPDATE bookings SET status = 'in_progress' WHERE status IN ('Started', 'In Progress', 'in_progress')");
        DB::statement("UPDATE bookings SET status = 'payment_pending' WHERE status = 'Payment Pending'");
        DB::statement("UPDATE bookings SET status = 'completed' WHERE status = 'Completed'");
        DB::statement("UPDATE bookings SET status = 'cancelled' WHERE status = 'Cancelled'");

        // 3. Normalize anything that might have been lowercase already or mixed
        DB::statement("UPDATE bookings SET status = LOWER(REPLACE(status, ' ', '_'))");

        // 4. Modify the column to the new strict lowercase ENUM
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the Title Case strings if needed
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'Unassigned',
            'Pending',
            'Confirmed',
            'Assigned',
            'Accepted',
            'On The Way',
            'Arrived',
            'In Progress',
            'Payment Pending',
            'Completed',
            'Cancelled'
        ) NOT NULL DEFAULT 'Unassigned'");
    }
};
