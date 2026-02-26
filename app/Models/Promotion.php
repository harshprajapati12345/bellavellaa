<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'description', 'type', 'value',
        'max_discount_paise', 'min_order_paise',
        'usage_limit', 'per_user_limit', 'times_used',
        'target_type', 'target_id',
        'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'max_discount_paise' => 'integer',
        'min_order_paise' => 'integer',
        'usage_limit' => 'integer',
        'per_user_limit' => 'integer',
        'times_used' => 'integer',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany { return $this->hasMany(PromotionUsage::class); }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeActive($q)
    {
        return $q->where('is_active', true)
                  ->where('starts_at', '<=', now())
                  ->where('ends_at', '>=', now());
    }

    public function scopeForCode($q, string $code) { return $q->where('code', $code); }

    // ── Validation Helpers ─────────────────────────────────────────
    public function isValid(): bool
    {
        return $this->is_active
            && now()->between($this->starts_at, $this->ends_at)
            && ($this->usage_limit === null || $this->times_used < $this->usage_limit);
    }

    public function isValidForUser(int $userId): bool
    {
        if (!$this->isValid()) return false;
        $userUsages = $this->usages()->where('user_id', $userId)->count();
        return $userUsages < $this->per_user_limit;
    }

    public function calculateDiscount(int $orderTotalPaise): int
    {
        if ($orderTotalPaise < $this->min_order_paise) return 0;

        $discount = match ($this->type) {
            'percentage' => (int) ($orderTotalPaise * $this->value / 100),
            'flat' => $this->value,
            default => 0,
        };

        if ($this->max_discount_paise && $discount > $this->max_discount_paise) {
            $discount = $this->max_discount_paise;
        }

        return min($discount, $orderTotalPaise);
    }
}
