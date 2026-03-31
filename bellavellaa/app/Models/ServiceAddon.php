<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAddon extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'price',
        'description',
        'sort_order',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
