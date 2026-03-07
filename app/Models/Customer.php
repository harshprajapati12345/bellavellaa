<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use SoftDeletes, Notifiable;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'avatar',
        'date_of_birth',
        'status',
        'bookings',
        'joined',
        'referral_code',
        'referred_by_customer_id',
        'referral_code_used',
    ];

    protected $casts = [
        'joined' => 'date',
        'date_of_birth' => 'date',
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

    // ── Referral ───────────────────────────────────────────────────
    public function referrals()
    {
        return $this->morphMany(Referral::class, 'referrer');
    }

    public function referrer()
    {
        return $this->belongsTo(Customer::class, 'referred_by');
    }

    public function referralsReceived()
    {
        return $this->morphMany(Referral::class, 'referred');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->referral_code)) {
                $customer->referral_code = self::generateUniqueReferralCode($customer->name);
            }
        });
    }

    public static function generateUniqueReferralCode($name = null): string
    {
        $namePart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name ?? 'USR'), 0, 5));
        if (empty($namePart)) $namePart = 'USR';
        
        do {
            $code = $namePart . rand(100, 999);
        } while (self::where('referral_code', $code)->exists());

        return $code;
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
