<?php

namespace App\Events;

use App\Models\Professional;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfessionalStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Ensure the event is only broadcast after the database transaction is committed.
     * Prevents race conditions where the UI updates before the DB is ready.
     *
     * @var bool
     */
    public $afterCommit = true;

    public $professional;

    /**
     * Create a new event instance.
     */
    public function __construct(Professional $professional)
    {
        $this->professional = $professional;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('professionals.status'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ProfessionalStatusUpdated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->professional->id,
            'status' => $this->professional->status, // Accessor with offline/busy/online
            'name' => $this->professional->name,
            'is_online' => $this->professional->is_online,
            'active_request_id' => $this->professional->active_request_id,
        ];
    }
}
