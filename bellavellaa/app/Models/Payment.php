<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'customer_id', 'gateway', 'gateway_payment_id',
        'gateway_order_id', 'gateway_signature', 'amount_paise',
        'currency', 'status', 'gateway_response', 'paid_at',
    ];

    protected $casts = [
        'amount_paise' => 'integer',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function getAmountAttribute(): float { return $this->amount_paise / 100; }
    public function getFormattedAmountAttribute(): string { return '₹' . number_format($this->amount, 2); }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeCaptured($q) { return $q->where('status', 'captured'); }
}
