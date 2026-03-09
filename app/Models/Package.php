<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [];

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
}
