<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('type')->comment('percentage, flat, bogo, free_addon');
            $table->unsignedBigInteger('value')->default(0)->comment('percentage value or paise');
            $table->unsignedBigInteger('max_discount_paise')->nullable()->comment('cap for percentage');
            $table->unsignedBigInteger('min_order_paise')->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_user_limit')->default(1);
            $table->unsignedInteger('times_used')->default(0);
            $table->string('target_type')->nullable()->comment('category, service, option, variant, package, all');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active']);
            $table->index(['starts_at', 'ends_at', 'is_active']);
            $table->index(['target_type', 'target_id']);
        });

        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('discount_paise');
            $table->timestamps();

            $table->index(['promotion_id', 'customer_id']);
        });

        // Deferred FK: orders.promotion_id → promotions.id
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('promotion_id')->references('id')->on('promotions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('promotions');
    }
};
