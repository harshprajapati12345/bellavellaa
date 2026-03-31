<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('item_type')->comment('service, package');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
