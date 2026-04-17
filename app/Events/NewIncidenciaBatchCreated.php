<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewIncidenciaBatchCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
    //
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('chat'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewIncidenciaBatchCreated';
    }
}