<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    protected $guarded = [];

    protected $casts = [
        'service_types' => 'array',
    ];

    protected $appends = ['average_rating', 'total_reviews'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get average rating for the service from reviews linked via bookings.
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
     * Get total review count for the service.
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
