<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ServiceVariant extends Model
{
    protected $fillable = [
        'service_id',
        'seed_key',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'sale_price',
        'duration_minutes',
        'sku',
        'status',
        'is_default',
        'is_bookable',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'float',
        'sale_price' => 'float',
        'is_default' => 'boolean',
        'is_bookable' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getDisplayPriceAttribute(): float
    {
        return (float) ($this->sale_price ?: $this->price ?: 0);
    }

    public function getOriginalPriceAttribute(): float
    {
        return (float) ($this->price ?: 0);
    }

    public function getIsDiscountedAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getResolvedDurationMinutesAttribute(): ?int
    {
        return $this->duration_minutes ?? $this->service?->resolved_duration_minutes;
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) (DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.service_variant_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->avg('rating') ?? 0.0);
    }

    public function getTotalReviewsAttribute(): int
    {
        return (int) DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.service_variant_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->count();
    }

    public function isBookable(): bool
    {
        return $this->status === 'Active'
            && $this->is_bookable
            && $this->service
            && $this->service->status === 'Active';
    }
}
