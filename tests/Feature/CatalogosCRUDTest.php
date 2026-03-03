<?php

namespace Tests\Feature;

use App\Models\Horario;
use App\Models\Jornada;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogosCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['type' => 'admin']);
    }

    /** @test */
    public function only_admins_can_access_catalogos()
    {
        $user = User::factory()->create(['type' => 'user']);

        $this->actingAs($user)->get(route('catalogos.periodos'))->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get(route('catalogos.horarios'))->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get(route('catalogos.jornadas'))->assertRedirect(route('dashboard'));

        $this->actingAs($this->admin)->get(route('catalogos.periodos'))->assertOk();
    }

    /** @test */
    public function can_create_periodo()
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Catalogos\Periodos::class)
            ->set('periodo', 1)
            ->set('year', 2025)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(Periodo::where('periodo', 1)->where('year', 2025)->exists());
    }

    /** @test */
    public function can_create_horario()
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Catalogos\Horarios::class)
            ->set('horario', '08:00 - 16:00')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(Horario::where('horario', '08:00 - 16:00')->exists());
    }

    /** @test */
    public function can_create_jornada()
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Catalogos\Jornadas::class)
            ->set('jornada', 'LUNES A VIERNES')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(Jornada::where('jornada', 'LUNES A VIERNES')->exists());
    }

    /** @test */
    public function can_delete_periodo()
    {
        $periodo = Periodo::create(['periodo' => 1, 'year' => 2024]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Catalogos\Periodos::class)
            ->call('delete', $periodo->id);

        $this->assertNull(Periodo::find($periodo->id));
    }
}
