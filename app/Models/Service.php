<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [];

    protected $casts = [
        'service_types' => 'array',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
}
