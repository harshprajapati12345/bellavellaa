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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_id');
            // Using string instead of enum for better flexibility, but documenting the expected values
            $table->string('payment_status')->default('pending')->comment('pending, paid, failed')->change();
            $table->string('payment_method')->nullable()->comment('online, wallet, cash')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_id', 'paid_at']);
            $table->string('payment_status')->default('pending')->comment('')->change();
            $table->string('payment_method')->nullable()->comment('online, cod, wallet')->change();
        });
    }
};
