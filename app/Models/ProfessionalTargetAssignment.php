<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalTargetAssignment extends Model
{
    protected $fillable = [
        'professional_id', 'performance_target_id',
        'current_value', 'is_completed', 'completed_at',
        'reward_claimed', 'period_start', 'period_end',
    ];

    protected $casts = [
        'current_value' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'reward_claimed' => 'boolean',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
    public function target(): BelongsTo { return $this->belongsTo(PerformanceTarget::class, 'performance_target_id'); }

    public function getProgressPercentage(): float
    {
        if ($this->target->target_value === 0) return 100;
        return min(100, round($this->current_value / $this->target->target_value * 100, 1));
    }

    public function scopeCompleted($q) { return $q->where('is_completed', true); }
    public function scopePending($q) { return $q->where('is_completed', false); }
    public function scopeUnclaimedRewards($q) { return $q->where('is_completed', true)->where('reward_claimed', false); }
}
