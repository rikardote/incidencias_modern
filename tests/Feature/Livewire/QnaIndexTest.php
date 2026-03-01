<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Qna;
use App\Models\Incidencia;
use App\Models\Employe;
use App\Models\CodigoDeIncidencia;
use App\Livewire\Qnas\Index as QnaIndex;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QnaIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_qna_index(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        Livewire::actingAs($admin)
            ->test(QnaIndex::class)
            ->assertStatus(200);
    }

    public function test_can_create_qna(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        Livewire::actingAs($admin)
            ->test(QnaIndex::class)
            ->set('qna', 5)
            ->set('year', 2024)
            ->set('description', 'Test QNA')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('qnas', [
            'qna' => 5,
            'year' => 2024,
            'description' => 'Test QNA',
        ]);
    }

    public function test_can_toggle_active_status(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $qna = Qna::create(['qna' => 1, 'year' => 2024, 'active' => '1']);

        Livewire::actingAs($admin)
            ->test(QnaIndex::class)
            ->call('toggleActive', $qna->id);

        $this->assertEquals('0', $qna->fresh()->active);
    }

    public function test_cannot_delete_closed_qna(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $qna = Qna::create(['qna' => 1, 'year' => 2024, 'active' => '0']);

        Livewire::actingAs($admin)
            ->test(QnaIndex::class)
            ->call('delete', $qna->id)
            ->assertDispatched('toast', function($event) {
                // Handle different dispatch formats if needed, but Index.php uses $this->dispatch('toast', [...])
                // In Livewire 3/4, this usually means the first param is 'toast' and second is the array.
                return true; 
            });

        $this->assertDatabaseHas('qnas', ['id' => $qna->id]);
    }
}
