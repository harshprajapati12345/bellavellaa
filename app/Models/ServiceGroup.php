<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceGroup extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceTypes()
    {
        return $this->hasMany(ServiceType::class)->orderBy('sort_order');
    }

    public function services()
    {
        return $this->hasManyThrough(
            Service::class,
            ServiceType::class,
            'service_group_id',
            'service_type_id'
        )->orderBy('sort_order');
    }

    public function directServices()
    {
        return $this->hasMany(Service::class, 'service_group_id')->orderBy('sort_order');
    }

    public static function generateSlug(Category $category, string $name): string
    {
        $base = Str::slug($category->slug . '-' . $name);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
