<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function package() { return $this->belongsTo(Package::class); }
    public function professional() { return $this->belongsTo(Professional::class); }
    public function review() { return $this->hasOne(Review::class); }
}
