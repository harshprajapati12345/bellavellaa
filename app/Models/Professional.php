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
        'active_request_id' => 'integer',
        'accumulated_seconds_today' => 'integer',
        'last_online_at' => 'datetime',
        'last_reset_at' => 'datetime',
    ];

    protected $appends = ['availability_status'];

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
            if ($professional->status === 'suspended') {
                $professional->active_request_id = null;
            }
        });

        // 🛡️ REMOVED: Manual status column management.
    }

    public function getAvatarAttribute($value)
    {
        if (empty($value)) {
            return asset('assets/images/default-avatar.png');
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return asset('storage/' . $value);
    }

    public function getAvailabilityStatusAttribute()
    {
        // 1. Account Level Check (Offline)
        if ($this->status !== 'active') {
            return 'offline';
        }

        // 2. Task Level Check (Busy)
        if ($this->active_request_id) {
            return 'busy';
        }
        
        return $this->is_online ? 'online' : 'offline';
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

    public function getQuotaSeconds()
    {
        // Prioritize admin global setting if shift_duration matches the old default (480) or is null
        $quotaInMinutes = $this->shift_duration;
        
        // If the pro has the old default or null, we check the global setting
        if (!$quotaInMinutes || $quotaInMinutes == 480) {
            $quotaInMinutes = (int) Setting::get('shift_duration', 480);
        }

        return $quotaInMinutes * 60;
    }

    public function getResetThreshold()
    {
        $now = now();
        // Shift reset threshold is 6 AM of the current day.
        $resetThreshold = now()->startOfDay()->addHours(6);

        // If it's currently before 6 AM, the threshold belongs to "yesterday's" 6 AM cycle.
        if ($now->lt($resetThreshold)) {
            $resetThreshold->subDay();
        }

        return $resetThreshold;
    }

    public function getRemainingSecondsTodayAttribute()
    {
        $totalQuotaSeconds = $this->getQuotaSeconds();

        $currentTime = now();
        $sessionSeconds = ($this->is_online && $this->last_online_at) 
            ? $currentTime->diffInSeconds($this->last_online_at) 
            : 0;

        return max(0, $totalQuotaSeconds - ($this->accumulated_seconds_today + $sessionSeconds));
    }

    public function shouldResetShift()
    {
        return !$this->last_reset_at || $this->last_reset_at->lt($this->getResetThreshold());
    }
}


