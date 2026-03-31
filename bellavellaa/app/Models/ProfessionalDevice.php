<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalDevice extends Model
{
    protected $fillable = [
        'professional_id', 'device_type', 'device_model',
        'fcm_token', 'app_version', 'last_active_at',
    ];

    protected $casts = ['last_active_at' => 'datetime'];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
}
