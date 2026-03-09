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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['source', 'reference_id']);
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('reference_id')->nullable()->change();
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index(['source', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['source', 'reference_id']);
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_id')->nullable()->change();
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index(['source', 'reference_id']);
        });
    }
};
