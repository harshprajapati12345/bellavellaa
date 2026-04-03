<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add trigger to ensure is_suspended & status stay consistent at DB level
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_professionals_consistency_update 
            BEFORE UPDATE ON professionals
            FOR EACH ROW
            BEGIN
                IF NEW.status = 'Active' AND NEW.is_suspended != 0 THEN
                    SET NEW.is_suspended = 0;
                ELSEIF NEW.status = 'Suspended' AND NEW.is_suspended != 1 THEN
                    SET NEW.is_suspended = 1;
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_professionals_consistency_insert 
            BEFORE INSERT ON professionals
            FOR EACH ROW
            BEGIN
                IF NEW.status = 'Active' AND NEW.is_suspended != 0 THEN
                    SET NEW.is_suspended = 0;
                ELSEIF NEW.status = 'Suspended' AND NEW.is_suspended != 1 THEN
                    SET NEW.is_suspended = 1;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_professionals_consistency_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_professionals_consistency_insert');
    }
};
