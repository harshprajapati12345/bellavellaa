<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('assigned_at')->nullable()->after('current_step');
            $table->timestamp('accepted_at')->nullable()->after('assigned_at');
            $table->timestamp('on_the_way_at')->nullable()->after('accepted_at');
            $table->timestamp('arrived_at')->nullable()->after('on_the_way_at');
            $table->timestamp('completed_at')->nullable()->after('service_started_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'assigned_at',
                'accepted_at',
                'on_the_way_at',
                'arrived_at',
                'completed_at',
                'cancelled_at',
            ]);
        });
    }
};
