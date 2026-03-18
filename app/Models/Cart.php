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

    public function getResolvedUnitPriceAttribute(): float
    {
        if ($this->item_type === 'package') {
            return (float) (
                data_get($this->meta, 'totals.final_total')
                ?? data_get($this->meta, 'totals.discounted_total')
                ?? data_get($this->meta, 'package_snapshot.display_price')
                ?? $this->package?->price
                ?? 0
            );
        }

        return (float) (($this->sellable_item->display_price ?? $this->sellable_item->price) ?? 0);
    }

    public function getResolvedDurationMinutesAttribute(): ?int
    {
        if ($this->item_type === 'package') {
            return data_get($this->meta, 'totals.duration_minutes')
                ?? $this->package?->duration;
        }

        return $this->sellable_item->resolved_duration_minutes
            ?? $this->sellable_item->duration_minutes
            ?? $this->sellable_item->duration
            ?? null;
    }

    public function getResolvedDisplayNameAttribute(): string
    {
        if ($this->item_type === 'package') {
            return (string) (
                data_get($this->meta, 'package_snapshot.title')
                ?? $this->package?->name
                ?? 'Package'
            );
        }

        return (string) ($this->variant?->name ?? $this->service?->name ?? $this->item?->name ?? 'Unknown');
    }
}
