<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'cancel_reason_code')) {
                $table->string('cancel_reason_code', 50)->nullable()->after('cancelled_at');
            }

            if (!Schema::hasColumn('bookings', 'cancel_reason_note')) {
                $table->text('cancel_reason_note')->nullable()->after('cancel_reason_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'cancel_reason_note')) {
                $table->dropColumn('cancel_reason_note');
            }

            if (Schema::hasColumn('bookings', 'cancel_reason_code')) {
                $table->dropColumn('cancel_reason_code');
            }
        });
    }
};