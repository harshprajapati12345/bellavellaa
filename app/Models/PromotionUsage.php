<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionUsage extends Model
{
    protected $fillable = [
        'promotion_id', 'customer_id', 'order_id', 'discount_paise',
    ];

    protected $casts = ['discount_paise' => 'integer'];

    public function promotion(): BelongsTo { return $this->belongsTo(Promotion::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
