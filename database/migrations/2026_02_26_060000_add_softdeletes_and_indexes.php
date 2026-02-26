<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Soft deletes on core tables (customers/admins already have softDeletes)
        Schema::table('professionals', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('services', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('status');
            $table->index('date');
            $table->index(['professional_id', 'status']);
            $table->index(['customer_id', 'status']);
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('status');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->softDeletes();
            $table->index(['status', 'valid_from', 'valid_until']);
        });

        // Indexes on other tables
        Schema::table('services', function (Blueprint $table) {
            $table->index('status');
            $table->index(['category_id', 'status']);
        });
        Schema::table('professionals', function (Blueprint $table) {
            $table->index(['status', 'verification']);
            $table->index('city');
        });
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->index(['professional_id', 'status']);
        });
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        $tables = ['professionals', 'services', 'packages', 'bookings', 'reviews', 'offers'];
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
