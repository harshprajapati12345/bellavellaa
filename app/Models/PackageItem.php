<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_required' => 'boolean',
        'is_default_selected' => 'boolean',
        'custom_price' => 'float',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PackageGroup::class, 'package_group_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PackageItemOption::class)->orderBy('sort_order');
    }

    public function linkedOptions(): HasMany
    {
        return $this->options()->whereNotNull('service_variant_id');
    }

    public function customOptions(): HasMany
    {
        return $this->options()->whereNull('service_variant_id');
    }
}
