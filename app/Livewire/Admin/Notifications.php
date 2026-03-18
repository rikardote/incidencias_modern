<?php

namespace App\Livewire\Admin;

use App\Events\SystemNotificationSent;
use App\Models\SystemNotification;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Notifications extends Component
{
    public string $title   = '';
    public string $body    = '';
    public string $type    = 'info';
    public string $target  = 'all'; // 'all' | specific user id
    public ?int   $targetUserId = null;

    public bool $isModalOpen = false;
    public ?int $deleteId    = null;

    protected function rules(): array
    {
        return [
            'title'        => 'required|string|max:120',
            'body'         => 'nullable|string|max:1000',
            'type'         => 'required|in:info,warning,success,danger',
            'target'       => 'required|in:all,user',
            'targetUserId' => 'required_if:target,user|nullable|exists:users,id',
        ];
    }

    public function openModal(): void
    {
        $this->reset(['title', 'body', 'type', 'target', 'targetUserId', 'deleteId']);
        $this->type = 'info';
        $this->target = 'all';
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    public function send(): void
    {
        if (!auth()->user()->admin()) abort(403);

        $this->validate();

        $targetUserId = $this->target === 'user' ? $this->targetUserId : null;

        $notification = SystemNotification::create([
            'sender_id'      => auth()->id(),
            'target_user_id' => $targetUserId,
            'title'          => $this->title,
            'body'           => $this->body ?: null,
            'type'           => $this->type,
        ]);

        // Determinar a quién emitir por Reverb
        if ($targetUserId) {
            $recipientIds = [$targetUserId];
        } else {
            // Todos los usuarios activos excepto el admin que la envía
            $recipientIds = User::where('active', true)
                ->where('id', '!=', auth()->id())
                ->pluck('id')
                ->toArray();
        }

        broadcast(new SystemNotificationSent($notification, $recipientIds));

        $this->dispatch('toast', [
            'icon'  => 'success',
            'title' => 'Notificación enviada correctamente.',
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function destroy(): void
    {
        if (!auth()->user()->admin()) abort(403);
        if ($this->deleteId) {
            SystemNotification::destroy($this->deleteId);
            $this->deleteId = null;
            $this->dispatch('toast', [
                'icon'  => 'success',
                'title' => 'Notificación eliminada.',
            ]);
        }
    }

    #[Computed]
    public function notifications()
    {
        return SystemNotification::with('sender', 'targetUser')
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function users()
    {
        return User::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);
    }

    public function render()
    {
        return view('livewire.admin.notifications')
            ->layout('layouts.app');
    }
}
