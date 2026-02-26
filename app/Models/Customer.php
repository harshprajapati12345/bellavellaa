<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use SoftDeletes, HasApiTokens;

    protected $fillable = [
        'name', 'mobile', 'avatar', 'city', 'zip',
        'address', 'status', 'bookings', 'joined',
    ];

    protected $casts = [
        'joined' => 'date',
        'bookings' => 'integer',
    ];

    protected $hidden = [];

    // ── Relationships ──────────────────────────────────────────────
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function bookingsRel(): HasMany { return $this->hasMany(Booking::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function notifications(): HasMany { return $this->hasMany(CustomerNotification::class); }
    public function promotionUsages(): HasMany { return $this->hasMany(PromotionUsage::class); }

    public function wallets(): HasMany { return $this->hasMany(Wallet::class, 'holder_id')->where('holder_type', 'customer'); }
    public function coinWallet(): HasOne { return $this->hasOne(Wallet::class, 'holder_id')->where('holder_type', 'customer')->where('type', 'coin'); }

    public function activeMembership(): HasOne
    {
        return $this->hasOne(CustomerMembership::class)->where('status', 'active')->where('expires_at', '>', now());
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeActive($q) { return $q->where('status', 'Active'); }
    public function scopeBlocked($q) { return $q->where('status', 'Blocked'); }
}
