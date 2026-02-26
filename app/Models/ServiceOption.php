<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOption extends Model
{
    protected $fillable = [
        'service_id', 'name', 'description',
        'price_paise', 'duration_minutes', 'is_required', 'sort_order',
    ];

    protected $casts = [
        'price_paise' => 'integer',
        'is_required' => 'boolean',
    ];

    public function getPriceAttribute(): float { return $this->price_paise / 100; }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
}
