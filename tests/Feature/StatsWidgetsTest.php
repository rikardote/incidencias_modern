<?php

namespace Tests\Feature;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\User;
use App\Models\Department;
use App\Models\CodigoDeIncidencia;
use App\Models\Qna;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class StatsWidgetsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['type' => 'admin']);
    }

    /** @test */
    public function dashboard_widgets_show_correct_counts()
    {
        $dept = Department::create(['description' => 'TEST DEPT', 'code' => '999']);
        
        // Empleados activos
        Employe::create([
            'num_empleado' => '1', 'name' => 'A', 'father_lastname' => 'B', 'mother_lastname' => 'C',
            'deparment_id' => $dept->id, 'active' => '1'
        ]);
        Employe::create([
            'num_empleado' => '2', 'name' => 'D', 'father_lastname' => 'E', 'mother_lastname' => 'F',
            'deparment_id' => $dept->id, 'active' => '1'
        ]);
        // Empleado inactivo
        Employe::create([
            'num_empleado' => '3', 'name' => 'G', 'father_lastname' => 'H', 'mother_lastname' => 'I',
            'deparment_id' => $dept->id, 'active' => '0'
        ]);

        $qna = Qna::create(['qna' => 1, 'year' => 2026, 'active' => '1']);
        $codigo = CodigoDeIncidencia::create(['code' => '01', 'description' => 'TEST']);

        // Incidencia hoy
        Incidencia::create([
            'qna_id' => $qna->id, 'employee_id' => 1, 'codigodeincidencia_id' => $codigo->id,
            'fecha_inicio' => now(), 'fecha_final' => now(), 'created_at' => now()
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Dashboard\StatsWidgets::class)
            ->assertSet('activeEmployeesCount', 2)
            ->assertSet('todayIncidenciasCount', 1);
    }

    /** @test */
    public function dashboard_widgets_detect_maintenance_mode()
    {
        Cache::put('capture_maintenance', true);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Dashboard\StatsWidgets::class)
            ->assertSee('Mantenimiento');

        Cache::put('capture_maintenance', false);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Dashboard\StatsWidgets::class)
            ->assertSee('Habilitado');
    }

    /** @test */
    public function dashboard_widgets_detect_system_status()
    {
        // El test de DB principal debería pasar siempre en sqlite :memory:
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Dashboard\StatsWidgets::class)
            ->assertSet('systemStatus.db_main', true)
            ->assertSet('systemStatus.db_biometrico', true);
    }
}
