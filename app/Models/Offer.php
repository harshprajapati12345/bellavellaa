<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'discount_value' => 'float',
        'max_discount_paise' => 'integer',
        'min_order_paise' => 'integer',
        'usage_limit' => 'integer',
        'per_user_limit' => 'integer',
        'times_used' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(OfferUsage::class);
    }

    public function scopeActive($query)
    {
        return $query
            ->where('status', 'Active')
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', now());
            });
    }

    public function isValid(): bool
    {
        $startsOk = $this->valid_from === null || now()->startOfDay()->gte($this->valid_from->startOfDay());
        $endsOk = $this->valid_until === null || now()->startOfDay()->lte($this->valid_until->startOfDay());
        $usageOk = $this->usage_limit === null || $this->times_used < $this->usage_limit;

        return $this->status === 'Active' && $startsOk && $endsOk && $usageOk;
    }

    public function calculateDiscount(int $orderTotalPaise): int
    {
        if ($orderTotalPaise < ($this->min_order_paise ?? 0)) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? (int) round($orderTotalPaise * ((float) $this->discount_value / 100))
            : (int) round(((float) $this->discount_value) * 100);

        if ($this->max_discount_paise && $discount > $this->max_discount_paise) {
            $discount = $this->max_discount_paise;
        }

        return min($discount, $orderTotalPaise);
    }
}
