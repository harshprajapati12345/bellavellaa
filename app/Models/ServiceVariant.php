<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceVariant extends Model
{
    protected $fillable = [
        'service_id', 'name', 'slug', 'image', 'price',
        'duration_minutes', 'status', 'is_default', 'sort_order',
    ];

    protected $casts = [
        'price' => 'float',
        'is_default' => 'boolean',
    ];

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
}
