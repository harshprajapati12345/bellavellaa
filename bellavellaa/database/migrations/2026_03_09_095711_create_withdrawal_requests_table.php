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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('amount')->comment('amount in paise');
            $table->string('method')->comment('bank, upi');
            $table->string('status')->default('pending')->comment('pending, approved, rejected, paid');
            $table->string('bank_account_id')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['professional_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
