<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceTarget extends Model
{
    protected $fillable = [
        'name', 'metric', 'target_value', 'period',
        'reward_coins', 'reward_cash_paise', 'is_active',
    ];

    protected $casts = [
        'target_value' => 'integer',
        'reward_coins' => 'integer',
        'reward_cash_paise' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany { return $this->hasMany(ProfessionalTargetAssignment::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
