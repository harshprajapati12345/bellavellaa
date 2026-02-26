<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->string('type')->comment('aadhaar, pan, license, certificate, photo');
            $table->string('file_path');
            $table->string('status')->default('pending')->comment('pending, approved, rejected');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['professional_id', 'type']);
        });

        Schema::create('professional_service_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->string('city');
            $table->string('area')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius_km')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['professional_id', 'is_active']);
            $table->index('city');
        });

        Schema::create('professional_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->string('device_type')->comment('android, ios');
            $table->string('device_model')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index('professional_id');
            $table->index('fcm_token');
        });

        Schema::create('professional_online_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->timestamp('went_online_at');
            $table->timestamp('went_offline_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['professional_id', 'went_online_at'], 'pro_online_sessions_pro_id_online_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_online_sessions');
        Schema::dropIfExists('professional_devices');
        Schema::dropIfExists('professional_service_areas');
        Schema::dropIfExists('professional_documents');
    }
};
