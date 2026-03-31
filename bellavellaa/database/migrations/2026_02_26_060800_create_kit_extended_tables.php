<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kit_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('kit_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('kit_category_id');
        });

        Schema::create('kit_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_product_id')->constrained()->cascadeOnDelete();
            $table->string('serial_number')->unique();
            $table->string('qr_code')->unique()->nullable();
            $table->string('status')->default('available')->comment('available, assigned, used, expired, damaged');
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->index(['kit_product_id', 'status']);
            $table->index('status');
        });

        Schema::create('professional_kit_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kit_unit_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['professional_id', 'used_at']);
            $table->unique(['kit_unit_id', 'used_at']);
        });

        Schema::create('kit_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_number')->nullable();
            $table->string('courier')->nullable();
            $table->string('status')->default('preparing')->comment('preparing, shipped, in_transit, delivered, returned');
            $table->text('address')->nullable();
            $table->json('items')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['professional_id', 'status']);
        });

        Schema::create('order_kit_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kit_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->string('scan_type')->default('qr')->comment('qr, manual');
            $table->boolean('is_valid')->default(true);
            $table->string('rejection_reason')->nullable();
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();

            $table->index(['order_id', 'kit_unit_id']);
            $table->unique(['kit_unit_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_kit_scans');
        Schema::dropIfExists('kit_shipments');
        Schema::dropIfExists('professional_kit_units');
        Schema::dropIfExists('kit_units');
        Schema::dropIfExists('kit_types');
        Schema::dropIfExists('kit_categories');
    }
};
