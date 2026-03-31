<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalOnlineSession extends Model
{
    protected $fillable = [
        'professional_id', 'went_online_at', 'went_offline_at',
        'duration_minutes', 'latitude', 'longitude',
    ];

    protected $casts = [
        'went_online_at' => 'datetime',
        'went_offline_at' => 'datetime',
    ];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }

    public function scopeActive($q) { return $q->whereNull('went_offline_at'); }
}
