<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceIncludedItem extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'sort_order',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
