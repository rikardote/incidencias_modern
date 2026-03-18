<?php

namespace App\Livewire;

use App\Models\SystemNotification;
use App\Models\SystemNotificationRead;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $isOpen = false;

    public function toggleOpen(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function markAsRead(int $notificationId): void
    {
        $userId = auth()->id();

        SystemNotificationRead::firstOrCreate(
            ['notification_id' => $notificationId, 'user_id' => $userId],
            ['read_at' => now()]
        );
    }

    public function markAllRead(): void
    {
        $userId = auth()->id();

        $unread = SystemNotification::unreadByUser($userId)
            ->latest()
            ->get();

        foreach ($unread as $n) {
            SystemNotificationRead::firstOrCreate(
                ['notification_id' => $n->id, 'user_id' => $userId],
                ['read_at' => now()]
            );
        }
    }

    /** Llamado por JS cuando llega un push de Reverb */
    public function newNotificationReceived(): void
    {
        // Solo refrescar el componente para actualizar contador y lista
    }

    public function render()
    {
        $userId = auth()->id();

        $notifications = SystemNotification::forUser($userId)
            ->with(['reads' => fn ($q) => $q->where('user_id', $userId)])
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($n) use ($userId) {
                $n->is_read = $n->reads->isNotEmpty();
                return $n;
            });

        $unreadCount = $notifications->where('is_read', false)->count();

        return view('livewire.notification-bell', compact('notifications', 'unreadCount'));
    }
}
