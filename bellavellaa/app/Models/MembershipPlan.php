<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'price_paise',
        'duration_days', 'discount_percentage', 'coins_reward',
        'benefits', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price_paise' => 'integer',
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

    public function getPriceAttribute(): float { return $this->price_paise / 100; }
    public function getFormattedPriceAttribute(): string { return '₹' . number_format($this->price, 2); }

    public function memberships(): HasMany { return $this->hasMany(CustomerMembership::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}
