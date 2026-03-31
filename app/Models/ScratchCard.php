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
        'expires_at',
        'source',
        'reference_id',
    ];


    protected $casts = [
        'scratched_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_scratched' => 'boolean',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
