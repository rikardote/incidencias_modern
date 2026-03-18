<?php

namespace App\Events;

use App\Models\SystemNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemNotificationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SystemNotification $notification,
        /** IDs de usuarios que deben recibirla (null = todos) */
        public ?array $recipientIds = null
    ) {}

    /**
     * Si la notificación es global (target_user_id = null), se emite en
     * el canal privado de cada usuario activo. Si es individual, solo al
     * canal de ese usuario.
     *
     * @return array<PrivateChannel>
     */
    public function broadcastOn(): array
    {
        if ($this->recipientIds) {
            return array_map(
                fn ($id) => new PrivateChannel("notifications.{$id}"),
                $this->recipientIds
            );
        }

        return [new PrivateChannel('notifications.global')];
    }

    public function broadcastAs(): string
    {
        return 'SystemNotificationSent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->notification->id,
            'title'      => $this->notification->title,
            'body'       => $this->notification->body,
            'type'       => $this->notification->type,
            'created_at' => $this->notification->created_at->toISOString(),
        ];
    }
}
