<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Package extends Model
{
    protected $guarded = [];

    protected $casts = [
        'featured' => 'boolean',
        'is_configurable' => 'boolean',
        'quantity_allowed' => 'boolean',
        'price' => 'float',
        'base_price_threshold' => 'float',
        'discount_value' => 'float',
    ];

    // NOTE: $casts['services' => 'array'] intentionally removed.
    // The JSON column still exists in DB during the transition period.
    // Once package_service pivot is backfilled and verified,
    // run the cleanup migration to drop the JSON column entirely.

    // ─── Relationships ───────────────────────────────────────────────

    /**
     * Services included in this package via the package_service pivot table.
     * Named services() — not servicesPivot() — for clean, readable code.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_service');
    }

    public function contexts()
    {
        return $this->hasMany(PackageContext::class)->orderBy('sort_order');
    }

    public function groups()
    {
        return $this->hasMany(PackageGroup::class)->orderBy('sort_order');
    }

    public function linkedGroups()
    {
        return $this->groups()->where('source_type', 'linked');
    }

    public function customGroups()
    {
        return $this->groups()->where('source_type', 'custom');
    }

    /**
     * The category this package belongs to (type = 'packages', e.g. Bride).
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function isHierarchyMode(): bool
    {
        return ($this->package_mode ?? 'hierarchy') === 'hierarchy';
    }

    public function isHybridMode(): bool
    {
        return ($this->package_mode ?? 'hierarchy') === 'hybrid';
    }

    public function isManualMode(): bool
    {
        return ($this->package_mode ?? 'hierarchy') === 'manual';
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) (DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.package_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->avg('rating') ?? 0.0);
    }

    public function getTotalReviewsAttribute(): int
    {
        return (int) DB::table('reviews')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('bookings.package_id', $this->id)
            ->where('reviews.status', 'Approved')
            ->count();
    }
}
