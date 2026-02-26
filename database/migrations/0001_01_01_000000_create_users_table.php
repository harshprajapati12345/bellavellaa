<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── CUSTOMERS (mobile + OTP auth, API/JWT only) ────────────
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('mobile', 15)->unique();
            $table->string('avatar')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 10)->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['Active', 'Blocked'])->default('Active');
            $table->integer('bookings')->default(0);
            $table->date('joined')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        // ── ADMINS (email + password, admin panel login) ───────────
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 15)->nullable();
            $table->string('avatar')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'manager', 'support', 'viewer'])->default('admin');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── OTP VERIFICATIONS ──────────────────────────────────────
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 15);
            $table->string('otp', 6);
            $table->string('purpose')->default('login')->comment('login, verify');
            $table->boolean('verified')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['mobile', 'otp', 'verified']);
            $table->index('expires_at');
        });

        // ── PERSONAL ACCESS TOKENS (JWT/Sanctum) ───────────────────
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('tokenable_type');
            $table->unsignedBigInteger('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['tokenable_type', 'tokenable_id'], 'pat_tokenable_idx');
        });

        // ── PASSWORD RESET (admins only) ───────────────────────────
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // ── SESSIONS ───────────────────────────────────────────────
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('otps');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('customers');
    }
};
