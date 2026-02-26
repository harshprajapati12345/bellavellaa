<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceVariant extends Model
{
    protected $fillable = [
        'service_id', 'name', 'price_paise',
        'duration_minutes', 'is_default', 'sort_order',
    ];

    protected $casts = [
        'price_paise' => 'integer',
        'is_default' => 'boolean',
    ];

    public function getPriceAttribute(): float { return $this->price_paise / 100; }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
}
