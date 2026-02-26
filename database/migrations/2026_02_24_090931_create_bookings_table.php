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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_avatar')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->string('service_name')->nullable();
            $table->foreignId('package_id')->nullable()->constrained()->onDelete('set null');
            $table->string('package_name')->nullable();
            $table->foreignId('professional_id')->nullable()->constrained()->onDelete('set null');
            $table->string('professional_name')->nullable();
            $table->date('date');
            $table->string('slot');
            $table->enum('status', ['Unassigned', 'Pending', 'Confirmed', 'Assigned', 'In Progress', 'Completed', 'Cancelled'])->default('Unassigned');
            $table->decimal('price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
