<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price_paise');
            $table->unsignedInteger('duration_days');
            $table->unsignedInteger('discount_percentage')->default(0);
            $table->unsignedInteger('coins_reward')->default(0);
            $table->json('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customer_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('membership_plan_id')->constrained()->cascadeOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active')->comment('active, expired, cancelled');
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('expires_at');
        });

        Schema::create('performance_targets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('metric')->comment('services_completed, online_minutes, work_minutes, revenue_paise');
            $table->unsignedBigInteger('target_value');
            $table->string('period')->default('monthly')->comment('daily, weekly, monthly');
            $table->unsignedBigInteger('reward_coins')->default(0);
            $table->unsignedBigInteger('reward_cash_paise')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('professional_target_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performance_target_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('current_value')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('reward_claimed')->default(false);
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->index(['professional_id', 'is_completed'], 'pro_target_assign_pro_completed_idx');
            $table->index(['period_start', 'period_end'], 'pro_target_assign_period_idx');
            $table->unique(['professional_id', 'performance_target_id', 'period_start'], 'pro_target_assign_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_target_assignments');
        Schema::dropIfExists('performance_targets');
        Schema::dropIfExists('customer_memberships');
        Schema::dropIfExists('membership_plans');
    }
};
