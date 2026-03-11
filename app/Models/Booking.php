<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

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
        return $this->variant?->name
            ?? $this->service_name
            ?? $this->service?->name
            ?? $this->package_name
            ?? $this->package?->name;
    }
}
