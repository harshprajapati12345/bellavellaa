<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAppFeedback extends Model
{
    protected $table = 'customer_app_feedback';

    protected $fillable = [
        'customer_id',
        'rating',
        'feedback',
        'device_info',
        'app_version',
    ];

    /**
     * Get the customer that owns the feedback.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
