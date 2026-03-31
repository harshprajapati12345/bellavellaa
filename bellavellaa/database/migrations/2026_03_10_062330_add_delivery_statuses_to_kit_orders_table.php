<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand the status column to include delivery workflow statuses.
     * Old values: Assigned, Returned, Lost
     * New values: Pending, Dispatched, Delivered, Returned, Lost
     */
    public function up(): void
    {
        // MySQL: change ENUM to include new values.
        // We use a raw statement because Blueprint::enum() cannot modify an existing column on all drivers.
        DB::statement("ALTER TABLE kit_orders MODIFY COLUMN status ENUM('Pending','Dispatched','Delivered','Returned','Lost','Assigned') NOT NULL DEFAULT 'Pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE kit_orders MODIFY COLUMN status ENUM('Assigned','Returned','Lost') NOT NULL DEFAULT 'Assigned'");
    }
};
