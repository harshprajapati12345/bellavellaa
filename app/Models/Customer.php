<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use SoftDeletes, Notifiable;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'mobile',
        'avatar',
        'city',
        'zip',
        'address',
        'status',
        'bookings',
        'joined',
    ];

    protected $casts = [
        'joined' => 'date',
        'bookings' => 'integer',
    ];

    protected $hidden = [];

    // ── JWT ────────────────────────────────────────────────────────
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // ── Relationships ──────────────────────────────────────────────
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function bookingsRel(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function notifications(): HasMany
    {
        return $this->hasMany(CustomerNotification::class);
    }
    public function promotionUsages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'holder_id')->where('holder_type', 'customer');
    }
    public function coinWallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'holder_id')->where('holder_type', 'customer')->where('type', 'coin');
    }

    public function activeMembership(): HasOne
    {
        return $this->hasOne(CustomerMembership::class)->where('status', 'active')->where('expires_at', '>', now());
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeActive($q)
    {
        return $q->where('status', 'Active');
    }
    public function scopeBlocked($q)
    {
        return $q->where('status', 'Blocked');
    }
}
