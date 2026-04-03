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
    public function wallet() { return $this->morphOne(Wallet::class, 'holder'); }

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
            $status = strtolower(trim($professional->status ?? ''));

            // 🛡️ 1. Force Sync: status -> is_suspended
            if ($status === 'active' && $professional->is_suspended !== false) {
                $professional->is_suspended = false;
            } else if ($status === 'suspended' && $professional->is_suspended !== true) {
                $professional->is_suspended = true;
            }

            // 🛡️ 2. Force Sync: is_suspended -> status (Bidirectional)
            if ($professional->isDirty('is_suspended')) {
                $expectedStatus = $professional->is_suspended ? 'Suspended' : 'Active';
                if (strtolower(trim($professional->status ?? '')) !== strtolower($expectedStatus)) {
                    $professional->status = $expectedStatus;
                }
            }

            // 📜 3. Audit Logging (Production Visibility)
            if ($professional->isDirty(['status', 'is_suspended'])) {
                $oldStatus = $professional->getOriginal('status') ?? 'N/A';
                $newStatus = $professional->status;
                $oldSuspended = $professional->getOriginal('is_suspended') ?? 'N/A';
                $newSuspended = $professional->is_suspended;
                
                \Log::info("Professional #{$professional->id} consistency sync: status ({$oldStatus} -> {$newStatus}), is_suspended ({$oldSuspended} -> {$newSuspended})");
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

