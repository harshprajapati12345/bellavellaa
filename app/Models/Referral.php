<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $table = 'referrals';

    protected $fillable = [
        'referrer_customer_id',
        'referred_customer_id',
        'referral_code_used',
        'status',
        'reward_coins',
        'reward_given_at',
    ];

    protected $casts = [
        'reward_given_at' => 'datetime',
        'reward_coins' => 'integer',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referrer_customer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_customer_id');
    }
}
