<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAssignment extends Model
{
    protected $fillable = [
        'order_id', 'professional_id', 'status',
        'assigned_at', 'responded_at', 'rejection_reason',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeAccepted($q) { return $q->where('status', 'accepted'); }
}
