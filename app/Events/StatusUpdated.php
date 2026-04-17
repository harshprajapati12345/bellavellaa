<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $professional;

    public function __construct($professional)
    {
        $this->professional = $professional;
    }

    public function broadcastOn()
    {
        return new Channel('professional.' . $this->professional->id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->professional->id,
            'status' => $this->professional->status,
            'availability_status' => $this->professional->availability_status,
            'is_online' => (bool)$this->professional->is_online,
        ];
    }

    public function broadcastAs()
    {
        return 'StatusUpdated';
    }
}
