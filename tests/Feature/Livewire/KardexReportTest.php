<?php

namespace Tests\Feature\Livewire;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\CodigoDeIncidencia;
use App\Models\Qna;
use App\Models\User;
use App\Livewire\Reports\KardexReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KardexReportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $employee;
    protected $codigo;
    protected $qna;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['type' => 'admin']);
        $this->qna = Qna::create([
            'qna' => 1,
            'year' => 2024,
            'active' => '1'
        ]);
        $this->codigo = CodigoDeIncidencia::create([
            'code' => '55',
            'description' => 'Test Code'
        ]);

        $this->employee = Employe::create([
            'num_empleado' => '012345',
            'name' => 'JUAN',
            'father_lastname' => 'PEREZ',
            'mother_lastname' => 'LOPEZ',
            'active' => 1
        ]);
    }

    /** @test */
    public function component_renders_successfully()
    {
        $this->actingAs($this->user);
        
        Livewire::test(KardexReport::class)
            ->assertStatus(200)
            ->assertSee('Kárdex');
    }

    /** @test */
    public function it_can_search_employee_by_number_with_padding()
    {
        $this->actingAs($this->user);

        Livewire::test(KardexReport::class)
            ->set('num_empleado', '12345') // Test padding
            ->call('cambiarEmpleadoByNum')
            ->assertSet('num_empleado', '012345')
            ->assertSet('employee.id', $this->employee->id);
    }

    /** @test */
    public function it_generates_results_automatically_when_employee_set()
    {
        $this->actingAs($this->user);

        // Create an incident
        Incidencia::create([
            'employee_id' => $this->employee->id,
            'codigodeincidencia_id' => $this->codigo->id,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_final' => now()->format('Y-m-d'),
            'total_dias' => 1,
            'qna_id' => $this->qna->id
        ]);

        Livewire::test(KardexReport::class)
            ->call('cambiarEmpleado', $this->employee->id)
            ->assertNotSet('results', null)
            ->assertCount('results', 1);
    }

    /** @test */
    public function it_can_set_date_range_to_all_history()
    {
        $this->actingAs($this->user);

        // Create an old incident
        $oldDate = '2010-01-01';
        Incidencia::create([
            'employee_id' => $this->employee->id,
            'codigodeincidencia_id' => $this->codigo->id,
            'fecha_inicio' => $oldDate,
            'fecha_final' => $oldDate,
            'total_dias' => 1,
            'qna_id' => $this->qna->id
        ]);

        Livewire::test(KardexReport::class)
            ->call('cambiarEmpleado', $this->employee->id)
            ->call('generateAll')
            ->assertSet('fecha_inicio', $oldDate)
            ->assertNotSet('results', null);
    }
}
