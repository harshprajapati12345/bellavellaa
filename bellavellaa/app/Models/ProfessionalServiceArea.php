<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalServiceArea extends Model
{
    protected $fillable = [
        'professional_id', 'city', 'area', 'pincode',
        'latitude', 'longitude', 'radius_km', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
