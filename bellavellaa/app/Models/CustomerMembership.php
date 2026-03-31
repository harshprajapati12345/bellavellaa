<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMembership extends Model
{
    protected $fillable = [
        'customer_id', 'membership_plan_id',
        'starts_at', 'expires_at', 'status', 'payment_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function plan(): BelongsTo { return $this->belongsTo(MembershipPlan::class, 'membership_plan_id'); }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }

    public function isActive(): bool { return $this->status === 'active' && now()->isBefore($this->expires_at); }
    public function scopeActive($q) { return $q->where('status', 'active')->where('expires_at', '>', now()); }
}
