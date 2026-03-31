<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScratchCard extends Model
{
    protected $fillable = [
        'customer_id',
        'amount',
        'title',
        'description',
        'is_scratched',
        'scratched_at',
        'source',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
