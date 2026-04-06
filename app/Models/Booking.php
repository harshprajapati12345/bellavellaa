<?php

namespace App\Models;

use App\Support\BookingLifecycle;
use App\Events\BookingStatusChanged;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon|null $date
 * @property \Carbon\Carbon|null $assigned_at
 * @property \Carbon\Carbon|null $accepted_at
 * @property \Carbon\Carbon|null $on_the_way_at
 * @property \Carbon\Carbon|null $arrived_at
 * @property \Carbon\Carbon|null $service_started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon|null $cancelled_at
 */
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

    public function canTransitionTo(string $next): bool
    {
        $map = config('booking.transitions');
        return in_array($next, $map[$this->status] ?? []);
    }

    public function applyStatusTransition(string $status, array $attributes = []): void
    {
        // 🚀 ELITE IDEMPOTENCY: If already in desired status, return early (safe re-entry).
        if ($this->status === $status) {
            return;
        }

        // 🛡️ STATE MACHINE ENFORCEMENT
        if (!$this->canTransitionTo($status)) {
            throw new \Exception("Invalid status transition from '{$this->status}' to '{$status}'.");
        }

        // 🛡️ TIMESTAMP INTEGRITY GUARD: First-write-wins for critical metrics.
        if ($status === 'in_progress' && $this->service_started_at) {
            return; 
        }

        $fromStatus = $this->status;
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

        // 🚀 DECOUPLED SIDE EFFECTS: Move non-core logic (notifications, analytics) to listeners.
        event(new BookingStatusChanged($this, $fromStatus, $status));

        // 📊 AUDIT LOGGING: Full observability.
        \Log::info('Booking status updated (Elite)', [
            'booking_id' => $this->id,
            'from' => $fromStatus,
            'to' => $status,
            'user_id' => auth()->id() ?? 'system/webhook',
            'request_ip' => request()->ip()
        ]);
    }

    public function scheduledAt(): ?Carbon
    {
        if (!$this->date || blank($this->slot)) {
            return null;
        }

        try {
            /** @var Carbon $date */
            $date = $this->date;
            return Carbon::parse($date->format('Y-m-d') . ' ' . trim((string) $this->slot));
        } catch (\Throwable) {
            return null;
        }
    }

    public function trackingWindowStartsAt(): ?Carbon
    {
        /** @var Carbon $date */
        $date = $this->date;
        return $date ? $date->copy()->startOfDay() : null;
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

    protected static function booted()
    {
        static::deleting(function ($booking) {
            if ($booking->professional_id) {
                $pro = $booking->professional;
                if ($pro && (int)$pro->active_request_id === (int)$booking->id) {
                    $pro->update(['active_request_id' => null]);
                    $pro->refresh();
                    broadcast(new \App\Events\ProfessionalStatusUpdated($pro))->toOthers();
                }
            }
        });
    }
}
