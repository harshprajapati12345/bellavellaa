<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    protected $guarded = [];

    protected $casts = [
        'service_types' => 'array',
        'has_variants' => 'boolean',
        'is_bookable' => 'boolean',
        'allow_direct_booking_with_variants' => 'boolean',
        'base_price' => 'float',
        'sale_price' => 'float',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function variants()
    {
        return $this->hasMany(ServiceVariant::class)->orderBy('sort_order');
    }

    public function activeVariants()
    {
        return $this->variants()->where('status', 'Active');
    }

    public function includedItems()
    {
        return $this->hasMany(ServiceIncludedItem::class)->orderBy('sort_order');
    }

    public function addons()
    {
        return $this->hasMany(ServiceAddon::class)->orderBy('sort_order');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceGroup()
    {
        return $this->belongsTo(ServiceGroup::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_service');
    }

    public function getResolvedCategoryAttribute()
    {
        return $this->serviceType?->serviceGroup?->category ?? $this->category;
    }

    public function getResolvedServiceGroupAttribute()
    {
        return $this->serviceType?->serviceGroup ?? $this->serviceGroup;
    }

    public function getDisplayPriceAttribute(): float
    {
        return (float) ($this->sale_price ?: ($this->base_price ?: $this->price ?: 0));
    }

    public function getOriginalPriceAttribute(): float
    {
        return (float) ($this->base_price ?: $this->price ?: 0);
    }

    public function getIsDiscountedAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->original_price;
    }

    public function getResolvedDurationMinutesAttribute(): ?int
    {
        return $this->duration_minutes ?? $this->duration;
    }

    public function canBeBookedDirectly(): bool
    {
        if ($this->status !== 'Active' || !$this->is_bookable) {
            return false;
        }

        if (!$this->has_variants) {
            return true;
        }

        $activeVariantsCount = $this->relationLoaded('variants')
            ? $this->variants->where('status', 'Active')->count()
            : $this->activeVariants()->count();

        if ($activeVariantsCount === 0) {
            return true;
        }

        return (bool) $this->allow_direct_booking_with_variants;
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.service_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->avg('rating') ?? 0.0;
    }

    public function getTotalReviewsAttribute(): int
    {
        return (int) DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.service_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->count();
    }
}
