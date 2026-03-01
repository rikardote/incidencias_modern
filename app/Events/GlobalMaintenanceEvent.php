<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GlobalMaintenanceEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $maintenance;
    public $sender_id;

    public function __construct($maintenance)
    {
        \Illuminate\Support\Facades\Log::info('GLOBAL_MAINTENANCE_EVENT_FIRED: ' . ($maintenance ? 'ON' : 'OFF'));
        $this->maintenance = $maintenance;
        $this->sender_id = auth()->id();
    }

    public function broadcastOn(): array
    {
        Log::info('BROADCASTING GLOBAL_MAINTENANCE_EVENT TO CHANNEL: chat');
        return [
            new PresenceChannel('chat')
        ];
    }

    public function broadcastAs(): string
    {
        return 'GlobalMaintenanceEvent';
    }
}