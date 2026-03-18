<?php

namespace Tests\Feature;

use App\Events\SystemNotificationSent;
use App\Livewire\Admin\Notifications as AdminNotifications;
use App\Livewire\NotificationBell;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class SystemNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_global_notification()
    {
        Event::fake();

        $admin = User::factory()->create(['type' => 'admin', 'active' => true]);
        $user1 = User::factory()->create(['type' => 'member', 'active' => true]);
        $user2 = User::factory()->create(['type' => 'member', 'active' => true]);

        Livewire::actingAs($admin)
            ->test(AdminNotifications::class)
            ->call('openModal')
            ->set('title', 'Global Mantenimiento')
            ->set('body', 'Test de broadcast global')
            ->set('type', 'info')
            ->set('target', 'all')
            ->call('send')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertDatabaseHas('system_notifications', [
            'sender_id' => $admin->id,
            'title' => 'Global Mantenimiento',
            'target_user_id' => null,
        ]);

        Event::assertDispatched(SystemNotificationSent::class, function ($event) use ($user1, $user2) {
            // Admin should not receive their own notification
            return in_array($user1->id, $event->recipientIds) &&
                   in_array($user2->id, $event->recipientIds);
        });
    }

    public function test_admin_can_send_direct_user_notification()
    {
        Event::fake();

        $admin = User::factory()->create(['type' => 'admin', 'active' => true]);
        $user1 = User::factory()->create(['type' => 'member', 'active' => true]);

        Livewire::actingAs($admin)
            ->test(AdminNotifications::class)
            ->call('openModal')
            ->set('title', 'Mensaje Privado')
            ->set('type', 'warning')
            ->set('target', 'user')
            ->set('targetUserId', $user1->id)
            ->call('send')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('system_notifications', [
            'sender_id' => $admin->id,
            'title' => 'Mensaje Privado',
            'target_user_id' => $user1->id,
        ]);

        Event::assertDispatched(SystemNotificationSent::class, function ($event) use ($user1) {
            return $event->recipientIds === [$user1->id];
        });
    }

    public function test_non_admin_cannot_send_notifications()
    {
        $user = User::factory()->create(['type' => 'member']);

        Livewire::actingAs($user)
            ->test(AdminNotifications::class)
            ->call('send')
            ->assertForbidden();
    }

    public function test_user_can_see_notifications_in_bell_and_mark_as_read()
    {
        $admin = User::factory()->create(['type' => 'admin', 'active' => true]);
        $user = User::factory()->create(['type' => 'member', 'active' => true]);

        // Crear una global y una individual
        $global = SystemNotification::create([
            'sender_id' => $admin->id,
            'target_user_id' => null,
            'title' => 'Aviso general',
            'type' => 'info',
        ]);

        $direct = SystemNotification::create([
            'sender_id' => $admin->id,
            'target_user_id' => $user->id,
            'title' => 'Aviso directo',
            'type' => 'warning',
        ]);

        // Asegurarnos que la campana muestra 2 no leídas
        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->assertSee($global->title)
            ->assertSee($direct->title)
            ->assertViewHas('unreadCount', 2)
            ->call('markAsRead', $global->id)
            ->assertViewHas('unreadCount', 1);

        $this->assertDatabaseHas('system_notification_reads', [
            'notification_id' => $global->id,
            'user_id' => $user->id,
        ]);

        // Marcar todo como leído
        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('markAllRead')
            ->assertViewHas('unreadCount', 0);

        $this->assertDatabaseHas('system_notification_reads', [
            'notification_id' => $direct->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_global_scope_only_shows_relevant_notifications()
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $user1 = User::factory()->create(['type' => 'member']);
        $user2 = User::factory()->create(['type' => 'member']);

        SystemNotification::create([
            'sender_id' => $admin->id,
            'target_user_id' => $user1->id,
            'title' => 'Para User 1',
        ]);

        SystemNotification::create([
            'sender_id' => $admin->id,
            'target_user_id' => null,
            'title' => 'Para Todos',
        ]);

        // User 2 shouldn't see "Para User 1"
        Livewire::actingAs($user2)
            ->test(NotificationBell::class)
            ->assertSee('Para Todos')
            ->assertDontSee('Para User 1');
    }
}
