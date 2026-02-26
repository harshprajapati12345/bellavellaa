<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'item_type', 'item_id', 'item_name',
        'quantity', 'unit_price_paise', 'total_price_paise',
        'duration_minutes', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'unit_price_paise' => 'integer',
        'total_price_paise' => 'integer',
    ];

    public function getUnitPriceAttribute(): float { return $this->unit_price_paise / 100; }
    public function getTotalPriceAttribute(): float { return $this->total_price_paise / 100; }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
