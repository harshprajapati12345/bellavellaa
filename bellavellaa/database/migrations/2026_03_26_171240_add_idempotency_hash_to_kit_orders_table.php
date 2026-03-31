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
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->string('idempotency_hash')->nullable()->after('idempotency_key');
            
            // Ensure payment_session_id is unique if not already
            // (It was added as nullable unique in previous migration, but let's be safe)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kit_orders', function (Blueprint $table) {
            $table->dropColumn('idempotency_hash');
        });
    }
};
