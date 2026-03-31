<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderKitScan extends Model
{
    protected $fillable = [
        'order_id', 'kit_unit_id', 'professional_id',
        'scan_type', 'is_valid', 'rejection_reason', 'scanned_at',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'scanned_at' => 'datetime',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function kitUnit(): BelongsTo { return $this->belongsTo(KitUnit::class); }
    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
}
