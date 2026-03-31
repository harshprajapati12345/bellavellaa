<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewerCustomer()
    {
        return $this->belongsTo(Customer::class, 'reviewer_id');
    }

    public function reviewedCustomer()
    {
        return $this->belongsTo(Customer::class, 'reviewed_id');
    }

    public function reviewerProfessional()
    {
        return $this->belongsTo(Professional::class, 'reviewer_id');
    }

    public function reviewedProfessional()
    {
        return $this->belongsTo(Professional::class, 'reviewed_id');
    }
}
