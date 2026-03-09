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
        'last_seen'     => 'datetime',
    ];

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

