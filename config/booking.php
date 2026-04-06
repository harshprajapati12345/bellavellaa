<?php

/**
 * Booking State Machine Configuration
 * 
 * Defines the allowed status transitions for professional bookings.
 * This prevents invalid state jumps (e.g., accepted -> completed).
 */

return [
    'transitions' => [
        'pending'         => ['accepted', 'cancelled'],
        'assigned'        => ['accepted', 'on_the_way', 'cancelled', 'rejected'],
        'accepted'        => ['on_the_way', 'cancelled', 'rejected'],
        'on_the_way'      => ['arrived', 'cancelled'],
        'arrived'         => ['scan_kit', 'in_progress', 'cancelled'],
        'scan_kit'        => ['in_progress', 'cancelled'],
        'in_progress'     => ['payment_pending', 'completed'],
        'payment_pending' => ['completed'],
        'completed'       => [], // Final state
        'cancelled'       => [], // Final state
    ]
];
