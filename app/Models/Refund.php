<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'payment_id', 'order_id', 'gateway_refund_id',
        'amount_paise', 'reason', 'status', 'processed_at',
    ];

    protected $casts = [
        'amount_paise' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function getAmountAttribute(): float { return $this->amount_paise / 100; }

    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
}
