<?php

namespace App\Support;

class BookingLifecycle
{
    public const NON_TRACKABLE_STATUSES = [
        'completed',
        'cancelled',
        'rejected',
    ];

    public const NON_RESCHEDULABLE_STATUSES = [
        'completed',
        'cancelled',
        'rejected',
        'on_the_way',
        'arrived',
        'scan_kit',
        'in_progress',
        'payment_pending',
    ];

    public static function statusTimestampColumn(string $status): ?string
    {
        return match ($status) {
            'assigned' => 'assigned_at',
            'accepted' => 'accepted_at',
            'on_the_way' => 'on_the_way_at',
            'arrived', 'scan_kit' => 'arrived_at',
            'in_progress' => 'service_started_at',
            'completed' => 'completed_at',
            'cancelled' => 'cancelled_at',
            default => null,
        };
    }

    public static function stepForStatus(string $status): ?string
    {
        return match ($status) {
            'assigned', 'accepted' => 'accepted',
            'on_the_way' => 'journey',
            'arrived', 'scan_kit' => 'arrival',
            'in_progress' => 'service',
            'payment_pending' => 'payment',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => null,
        };
    }
}
