<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'professional_id',
        'address',
        'city',
        'zip',
        'latitude',
        'longitude',
        'scheduled_date',
        'scheduled_slot',
        'subtotal_paise',
        'discount_paise',
        'tax_paise',
        'total_paise',
        'coins_used',
        'status',
        'payment_status',
        'payment_method',
        'promotion_id',
        'coupon_code',
        'customer_notes',
        'admin_notes',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'subtotal_paise' => 'integer',
        'discount_paise' => 'integer',
        'tax_paise' => 'integer',
        'total_paise' => 'integer',
        'coins_used' => 'integer',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function getSubtotalAttribute(): float
    {
        return $this->subtotal_paise / 100;
    }
    public function getDiscountAttribute(): float
    {
        return $this->discount_paise / 100;
    }
    public function getTaxAttribute(): float
    {
        return $this->tax_paise / 100;
    }
    public function getTotalAttribute(): float
    {
        return $this->total_paise / 100;
    }
    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total, 2);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    public function assignments(): HasMany
    {
        return $this->hasMany(OrderAssignment::class);
    }
    public function otps(): HasMany
    {
        return $this->hasMany(OrderOtp::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }
    public function scopeActive($q)
    {
        return $q->whereNotIn('status', ['completed', 'cancelled']);
    }
    public function scopeCompleted($q)
    {
        return $q->where('status', 'completed');
    }
    public function scopeCancelled($q)
    {
        return $q->where('status', 'cancelled');
    }

    public static function generateOrderNumber(): string
    {
        return 'BV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
