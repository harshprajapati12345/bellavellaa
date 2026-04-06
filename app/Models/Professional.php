<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Professional extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'professionals';

    protected $guarded = [];


    protected $casts = [
        'services'      => 'array',
        'languages'     => 'array',
        'payout'        => 'array',
        'portfolio'     => 'array',
        'working_hours' => 'array',
        'docs'          => 'boolean',
        'kits'          => 'integer',
        'last_seen'      => 'datetime',
        'shift_start_time' => 'datetime',
        'shift_end_time' => 'datetime',
        'shift_duration' => 'integer',
        'last_withdrawal_at' => 'datetime',
        'last_deposit_at' => 'datetime',
        'reject_count' => 'integer',
        'last_reset_date' => 'date',
        'last_reject_date' => 'date',
        'is_suspended' => 'boolean',
        'active_request_id' => 'integer',
    ];

    protected $hidden = ['status'];

    protected $appends = ['status'];

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
    public function bookings() { return $this->hasMany(Booking::class); }
    public function activeBooking() { return $this->hasOne(Booking::class, 'id', 'active_request_id'); }
    public function wallet() { return $this->morphOne(Wallet::class, 'holder'); }
    public function cashWallet() { return $this->wallet()->where('type', 'cash'); }

    public function kitOrders()
    {
        return $this->hasMany(KitOrder::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function referrals()
    {
        return $this->morphMany(Referral::class, 'referrer');
    }

    public function referrer()
    {
        return $this->belongsTo(Professional::class, 'referred_by');
    }

    protected static function booted()
    {
        static::creating(function ($professional) {
            if (empty($professional->referral_code)) {
                $professional->referral_code = self::generateUniqueReferralCode($professional->name);
            }
        });

        static::saving(function ($professional) {
            // 🛡️ Data Integrity Rule: Suspended professionals cannot have active job requests
            if ($professional->is_suspended) {
                $professional->active_request_id = null;
            }
        });

        // 🛡️ REMOVED: Manual status column management.
        // Availability is now purely computed based on is_suspended and active_request_id.
    }

    public function getStatusAttribute()
    {
        // 1. Account Level Check (Offline)
        if ($this->is_suspended) {
            return 'offline';
        }

        // 2. Task Level Check (Busy)
        return $this->active_request_id ? 'busy' : 'online';
    }

    public static function generateUniqueReferralCode($name = null): string
    {
        $namePart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name ?? 'PRO'), 0, 5));
        if (empty($namePart)) $namePart = 'PRO';
        
        do {
            $code = $namePart . rand(100, 999);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }
}

