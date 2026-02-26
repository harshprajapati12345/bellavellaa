<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $guarded = [];

    protected $casts = [
        'services' => 'array',
    ];

    public function bookings() { return $this->hasMany(Booking::class); }

    public function kitOrders()
    {
        return $this->hasMany(KitOrder::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
