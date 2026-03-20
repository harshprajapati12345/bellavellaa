<?php

namespace App\Models;

use App\Support\BookingLifecycle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'date' => 'date',
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'on_the_way_at' => 'datetime',
        'arrived_at' => 'datetime',
        'service_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function variant()
    {
        return $this->belongsTo(ServiceVariant::class, 'service_variant_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getSellableItemAttribute()
    {
        if ($this->sellable_type === 'package') {
            return $this->package;
        }

        if ($this->service_variant_id) {
            return $this->variant;
        }

        if ($this->service_id) {
            return $this->service;
        }

        return $this->package;
    }

    public function getSellableNameAttribute(): ?string
    {
        if ($this->sellable_type === 'package') {
            return data_get($this->meta, 'package_snapshot.title')
                ?? $this->package_name
                ?? $this->package?->name;
        }

        return $this->variant?->name
            ?? $this->service_name
            ?? $this->service?->name
            ?? $this->package_name
            ?? $this->package?->name;
    }

    public function getCustomerDisplayNameAttribute(): string
    {
        return $this->customer?->name
            ?? $this->customer_name
            ?? $this->customer?->mobile
            ?? $this->customer_phone
            ?? 'Guest';
    }

    public function markLifecycle(string $column): bool
    {
        if (blank($column) || $this->{$column}) {
            return false;
        }

        $this->{$column} = now();

        return true;
    }

    public function applyStatusTransition(string $status, array $attributes = []): void
    {
        $attributes['status'] = $status;

        $step = BookingLifecycle::stepForStatus($status);
        if ($step !== null && !array_key_exists('current_step', $attributes)) {
            $attributes['current_step'] = $step;
        }

        $timestampColumn = BookingLifecycle::statusTimestampColumn($status);
        if ($timestampColumn !== null && !$this->{$timestampColumn} && !array_key_exists($timestampColumn, $attributes)) {
            $attributes[$timestampColumn] = now();
        }

        $this->fill($attributes);
        $this->save();
    }

    public function scheduledAt(): ?Carbon
    {
        if (!$this->date || blank($this->slot)) {
            return null;
        }

        try {
            return Carbon::parse($this->date->format('Y-m-d') . ' ' . trim((string) $this->slot));
        } catch (\Throwable) {
            return null;
        }
    }

    public function trackingWindowStartsAt(): ?Carbon
    {
        return $this->date ? $this->date->copy()->startOfDay() : null;
    }

    public function rescheduleCutoffAt(): ?Carbon
    {
        return $this->scheduledAt()?->copy()->subHours(3);
    }

    public function rescheduleWindowEndsAt(): Carbon
    {
        return now()->addDays(7)->endOfDay();
    }

    public function rescheduleHistory(): array
    {
        $history = data_get($this->meta, 'reschedule_history', []);

        return is_array($history) ? $history : [];
    }

    public function hasRemainingRescheduleAttempt(): bool
    {
        return count($this->rescheduleHistory()) < 1;
    }

    public function canTrackProfessional(): bool
    {
        if (!$this->professional_id) {
            return false;
        }

        if (in_array($this->status, BookingLifecycle::NON_TRACKABLE_STATUSES, true)) {
            return false;
        }

        $serviceDayStart = $this->trackingWindowStartsAt();
        if (!$serviceDayStart) {
            return false;
        }

        return now()->greaterThanOrEqualTo($serviceDayStart);
    }

    public function canReschedule(): bool
    {
        if (in_array($this->status, BookingLifecycle::NON_RESCHEDULABLE_STATUSES, true)) {
            return false;
        }

        if (!$this->hasRemainingRescheduleAttempt()) {
            return false;
        }

        $cutoff = $this->rescheduleCutoffAt();
        if (!$cutoff || !now()->lt($cutoff)) {
            return false;
        }

        return $this->date
            ? $this->date->copy()->startOfDay()->lessThanOrEqualTo($this->rescheduleWindowEndsAt())
            : false;
    }

    public function canCancel(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled'], true)
            && $this->canReschedule();
    }
}
