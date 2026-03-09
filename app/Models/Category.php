<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    protected $casts = [
        'featured' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    /**
     * Child service groups (Luxe, Prime, Ayurveda) under this category.
     * Only applicable for type = 'services' categories.
     */
    public function serviceGroups()
    {
        return $this->hasMany(ServiceGroup::class);
    }

    /**
     * Category banners for the top carousels.
     */
    public function banners()
    {
        return $this->hasMany(CategoryBanner::class)->orderBy('sort_order');
    }

    /**
     * ALL services under this category (grouped + ungrouped).
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Only services with NO service group (e.g. Hair Studio flat list).
     * Use this for the /categories/{slug}/services API endpoint.
     */
    public function directServices()
    {
        return $this->hasMany(Service::class)->whereNull('service_group_id');
    }

    /**
     * Packages linked to this category (type = 'packages' categories only).
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Bookings through services (kept for compatibility).
     */
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Service::class);
    }

    // ─── has_groups NOTE ─────────────────────────────────────────────
    // Do NOT use a $appends accessor for has_groups — it fires a DB query per model.
    // Instead, use withCount() in the query:
    //
    //   Category::withCount([
    //       'serviceGroups as active_service_groups_count' => fn($q) => $q->where('status','Active')
    //   ])->get();
    //
    // Then in CategoryResource: 'has_groups' => ($this->active_service_groups_count ?? 0) > 0
}
