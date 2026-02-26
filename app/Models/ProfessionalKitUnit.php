<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalKitUnit extends Model
{
    protected $fillable = [
        'professional_id', 'kit_unit_id',
        'assigned_at', 'used_at', 'order_id',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
    public function kitUnit(): BelongsTo { return $this->belongsTo(KitUnit::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }

    public function isUsed(): bool { return $this->used_at !== null; }
}
