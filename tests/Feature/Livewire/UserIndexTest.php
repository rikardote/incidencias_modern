<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Qna;
use App\Models\CaptureException;
use App\Livewire\Users\Index as UserIndex;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_user_index(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->assertStatus(200);
    }

    public function test_non_admin_redirected_from_user_index(): void
    {
        $user = User::factory()->create(['type' => 'user']);

        Livewire::actingAs($user)
            ->test(UserIndex::class)
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_search_users(): void
    {
        $admin = User::factory()->create(['type' => 'admin', 'name' => 'Admin User']);
        $otherUser = User::factory()->create(['name' => 'Searchable User']);

        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->set('search', 'Searchable')
            ->assertSee('Searchable User')
            ->assertDontSee('Admin User');
    }

    public function test_admin_can_grant_capture_exception(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $user = User::factory()->create(['type' => 'user']);
        
        $lastClosed = Qna::create([
            'qna' => 1,
            'year' => 2024,
            'active' => '0', // Closed
        ]);

        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('grantException', $user->id)
            ->assertSet('exceptionUserId', $user->id)
            ->assertSet('exceptionQnaId', $lastClosed->id)
            ->set('exceptionDuration', 20)
            ->set('exceptionReason', 'Test Reason')
            ->call('saveException')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('capture_exceptions', [
            'user_id' => $user->id,
            'qna_id' => $lastClosed->id,
            'reason' => 'Test Reason',
        ]);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('toggleActive', $admin->id)
            ->assertDispatched('notify');

        $this->assertTrue($admin->fresh()->active);
    }
}
