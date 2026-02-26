<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitShipment extends Model
{
    protected $fillable = [
        'professional_id', 'tracking_number', 'courier',
        'status', 'address', 'items', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'items' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }

    public function scopePending($q) { return $q->where('status', 'preparing'); }
    public function scopeShipped($q) { return $q->where('status', 'shipped'); }
    public function scopeDelivered($q) { return $q->where('status', 'delivered'); }
}
