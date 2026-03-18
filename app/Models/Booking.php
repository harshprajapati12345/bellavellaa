<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function variant()
    {
        return $this->belongsTo(ServiceVariant::class, 'service_variant_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getSellableItemAttribute()
    {
        if ($this->sellable_type === 'package') {
            return $this->package;
        }

        if ($this->service_variant_id) {
            return $this->variant;
        }

        if ($this->service_id) {
            return $this->service;
        }

        return $this->package;
    }

    public function getSellableNameAttribute(): ?string
    {
        if ($this->sellable_type === 'package') {
            return data_get($this->meta, 'package_snapshot.title')
                ?? $this->package_name
                ?? $this->package?->name;
        }

        return $this->variant?->name
            ?? $this->service_name
            ?? $this->service?->name
            ?? $this->package_name
            ?? $this->package?->name;
    }
}
