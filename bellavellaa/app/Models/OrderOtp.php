<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderOtp extends Model
{
    protected $fillable = [
        'order_id', 'otp', 'type', 'verified',
        'expires_at', 'verified_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }

    public function isExpired(): bool { return now()->isAfter($this->expires_at); }
    public function isValid(string $otp): bool { return !$this->verified && !$this->isExpired() && $this->otp === $otp; }
}
