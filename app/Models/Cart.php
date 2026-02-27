<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Cart extends Model
{
    protected $fillable = [
        'customer_id',
        'item_type',
        'item_id',
        'quantity',
        'meta'
    ];

    protected $casts = [
        'meta' => 'json',
        'quantity' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }
}
