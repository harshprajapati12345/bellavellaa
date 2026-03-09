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
    ];



    // ─── Relationships ───────────────────────────────────────────────

    public function variants()
    {
        return $this->hasMany(ServiceVariant::class)->orderBy('sort_order');
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

    /**
     * The service group (Luxe, Prime, Ayurveda) this service belongs to.
     * Null for categories without a group layer (e.g. Hair Studio).
     */
    public function serviceGroup()
    {
        return $this->belongsTo(ServiceGroup::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Packages that include this service (via pivot).
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_service');
    }

    // ─── Computed attributes ─────────────────────────────────────────

    /**
     * Average rating from approved reviews linked via bookings.
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.service_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->avg('rating') ?? 0.0;
    }

    /**
     * Total count of approved reviews.
     */
    public function getTotalReviewsAttribute(): int
    {
        return (int) DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.service_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->count();
    }
}
