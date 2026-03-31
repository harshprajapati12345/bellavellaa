<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $guarded = [];

    public function serviceGroup()
    {
        return $this->belongsTo(ServiceGroup::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class)->orderBy('sort_order');
    }

    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            ServiceGroup::class,
            'id',
            'id',
            'service_group_id',
            'category_id'
        );
    }
}
