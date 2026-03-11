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
        'service_id',
        'service_variant_id',
        'package_id',
        'quantity',
        'meta',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class, 'service_variant_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getSellableItemAttribute()
    {
        return $this->variant ?? $this->service ?? $this->package ?? $this->item;
    }
}
