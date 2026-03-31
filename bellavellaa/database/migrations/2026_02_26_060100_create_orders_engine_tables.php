<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('professional_id')->nullable()->constrained()->nullOnDelete();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Scheduling
            $table->date('scheduled_date');
            $table->string('scheduled_slot')->nullable();

            // Pricing (paise-based for precision)
            $table->unsignedBigInteger('subtotal_paise')->default(0);
            $table->unsignedBigInteger('discount_paise')->default(0);
            $table->unsignedBigInteger('tax_paise')->default(0);
            $table->unsignedBigInteger('total_paise')->default(0);
            $table->unsignedInteger('coins_used')->default(0);

            // Status
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable()->comment('online, cod, wallet');

            // Promotion (FK added after promotions table exists)
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->string('coupon_code')->nullable();

            // Meta
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('payment_status');
            $table->index(['customer_id', 'status']);
            $table->index(['professional_id', 'status']);
            $table->index('scheduled_date');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('item_type')->comment('service, package, addon');
            $table->unsignedBigInteger('item_id');
            $table->string('item_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price_paise')->default(0);
            $table->unsignedBigInteger('total_price_paise')->default(0);
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['item_type', 'item_id']);
        });

        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('changed_by_type')->nullable()->comment('user, professional, admin, system');
            $table->unsignedBigInteger('changed_by_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });

        Schema::create('order_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending')->comment('pending, accepted, rejected, cancelled');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['professional_id', 'status']);
            $table->index(['order_id', 'status']);
        });

        Schema::create('order_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('otp', 6);
            $table->string('type')->default('start')->comment('start, complete');
            $table->boolean('verified')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'type', 'verified']);
        });

        Schema::create('professional_location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('logged_at')->useCurrent();

            $table->index(['professional_id', 'logged_at']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_location_logs');
        Schema::dropIfExists('order_otps');
        Schema::dropIfExists('order_assignments');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
