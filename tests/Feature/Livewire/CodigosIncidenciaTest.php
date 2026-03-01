<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\CodigoDeIncidencia;
use App\Livewire\CodigosIncidencia\Index;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CodigosIncidenciaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_codigos_index(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->assertStatus(200);
    }

    public function test_non_admin_redirected_from_codigos_index(): void
    {
        $user = User::factory()->create(['type' => 'user']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_search_codigos(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        CodigoDeIncidencia::create(['code' => '99', 'description' => 'Test Code']);
        CodigoDeIncidencia::create(['code' => '55', 'description' => 'Hidden Code']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('search', 'Test')
            ->assertSee('Test Code')
            ->assertDontSee('Hidden Code');
    }

    public function test_admin_can_create_codigo(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('create')
            ->set('code', '66')
            ->set('description', 'New Code Desc')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('codigos_de_incidencias', [
            'code' => '66',
            'description' => 'NEW CODE DESC',
        ]);
    }

    public function test_admin_can_update_codigo(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        $codigo = CodigoDeIncidencia::create(['code' => '77', 'description' => 'OLD CODE DESC']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('edit', $codigo->id)
            ->set('description', 'Updated Code Desc')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('codigos_de_incidencias', [
            'id' => $codigo->id,
            'description' => 'UPDATED CODE DESC',
        ]);
    }

    public function test_codigo_validation(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('create')
            ->set('code', '')
            ->set('description', '')
            ->call('save')
            ->assertHasErrors(['code' => 'required', 'description' => 'required']);
    }
}