<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar',
        'role', 'is_active', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function profile(): HasOne { return $this->hasOne(AdminProfile::class); }
    public function notifications(): HasMany { return $this->hasMany(AdminNotification::class); }

    // ── Helpers ────────────────────────────────────────────────────
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool { return in_array($this->role, ['super_admin', 'admin']); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}
