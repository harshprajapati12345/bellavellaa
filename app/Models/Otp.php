<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'mobile', 'otp', 'purpose', 'verified',
        'expires_at', 'verified_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Generate a 6-digit OTP for the given mobile number
     */
    public static function generate(string $mobile, string $purpose = 'login', int $expiryMinutes = 5): self
    {
        // Invalidate old OTPs for this mobile
        self::where('mobile', $mobile)
            ->where('purpose', $purpose)
            ->where('verified', false)
            ->delete();

        return self::create([
            'mobile' => $mobile,
            'otp' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);
    }

    /**
     * Verify an OTP
     */
    public static function verify(string $mobile, string $otp, string $purpose = 'login'): ?self
    {
        $record = self::where('mobile', $mobile)
            ->where('otp', $otp)
            ->where('purpose', $purpose)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($record) {
            $record->update(['verified' => true, 'verified_at' => now()]);
        }

        return $record;
    }

    public function isExpired(): bool { return now()->isAfter($this->expires_at); }
    public function isValid(string $otp): bool { return !$this->verified && !$this->isExpired() && $this->otp === $otp; }
}
