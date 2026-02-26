<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id', 'type', 'amount', 'balance_after',
        'source', 'reference_id', 'reference_type',
        'description', 'expires_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function wallet(): BelongsTo { return $this->belongsTo(Wallet::class); }

    public function scopeCredits($q) { return $q->where('type', 'credit'); }
    public function scopeDebits($q) { return $q->where('type', 'debit'); }
    public function scopeExpired($q) { return $q->whereNotNull('expires_at')->where('expires_at', '<', now()); }
}
