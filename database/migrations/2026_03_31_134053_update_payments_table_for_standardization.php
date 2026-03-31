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
        Schema::table('payments', function (Blueprint $table) {
            // Standardize columns
            if (Schema::hasColumn('payments', 'gateway_response')) {
                $table->renameColumn('gateway_response', 'meta_json');
            } else if (!Schema::hasColumn('payments', 'meta_json')) {
                $table->json('meta_json')->nullable()->after('status');
            }

            // Ensure amount_paise is bigInteger
            $table->unsignedBigInteger('amount_paise')->change();
            
            // Standardize payment_method and gateway
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->after('customer_id')->comment('ONLINE, COD, WALLET');
            }

            // Standardize status
            $table->string('status')->default('PENDING')->comment('PENDING, SUCCESS, FAILED, REFUNDED')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'meta_json')) {
                $table->renameColumn('meta_json', 'gateway_response');
            }
            if (Schema::hasColumn('payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            $table->string('status')->default('pending')->change();
        });
    }
};
