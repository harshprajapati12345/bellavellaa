<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('holder_type')->comment('user, professional');
            $table->unsignedBigInteger('holder_id');
            $table->string('type')->comment('coin, cash');
            $table->bigInteger('balance')->default(0)->comment('coins or paise');
            $table->unsignedInteger('version')->default(0)->comment('optimistic locking');
            $table->timestamps();

            $table->unique(['holder_type', 'holder_id', 'type']);
            $table->index(['holder_type', 'holder_id']);
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type')->comment('credit, debit');
            $table->bigInteger('amount')->comment('always positive');
            $table->bigInteger('balance_after');
            $table->string('source')->nullable()->comment('order_completion, refund, admin_adjustment, promotion, review_reward, expiry');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'created_at']);
            $table->index(['source', 'reference_id']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
