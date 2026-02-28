<?php

namespace Tests\Feature\Incidencias;

use App\Models\Employe;
use App\Models\User;
use App\Models\Department;
use App\Models\CodigoDeIncidencia;
use App\Models\Qna;
use App\Livewire\Incidencias\Manager;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncapacidadExcesoAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_captura_de_incapacidad_excedida_dispara_swal_warning()
    {
        Carbon::setTestNow('2026-02-27');

        $admin = User::factory()->create(['type' => 'admin']);
        $dept = Department::create(['description' => 'TEST DEPT', 'code' => '001']);
        
        // Empleado con antigüedad menor a 1 año (límite 15 días)
        $employee = Employe::create([
            'num_empleado' => '10101',
            'name' => 'Carlos',
            'father_lastname' => 'Test',
            'mother_lastname' => 'User',
            'deparment_id' => $dept->id,
            'fecha_ingreso' => '2025-08-27',
        ]);

        $codigo55 = CodigoDeIncidencia::create(['code' => '55', 'description' => 'EG']);
        DB::statement('PRAGMA foreign_keys = OFF;');
        DB::table('puestos')->insert(['id' => 24, 'puesto' => 'MEDICO']);

        $doctor = Employe::create([
            'num_empleado' => 'DOC01',
            'name' => 'Dr. House',
            'father_lastname' => 'Gregory',
            'mother_lastname' => 'Macleod',
            'puesto_id' => '24',
            'deparment_id' => $dept->id,
        ]);

        Qna::create(['qna' => 3, 'year' => 2026, 'active' => '1']);
        Qna::create(['qna' => 4, 'year' => 2026, 'active' => '1']);

        Livewire::actingAs($admin)
            ->test(Manager::class, ['employeeId' => $employee->id])
            ->set('codigo', $codigo55->id)
            ->set('fechas_seleccionadas', '2026-02-01 to 2026-02-20') // 20 días capturados (> 15 días)
            ->set('medico_id', $doctor->id)
            ->set('num_licencia', 'LIC-123')
            ->set('diagnostico', 'GRIPE')
            ->set('fecha_expedida', '2026-02-01')
            ->call('store')
            ->assertDispatched('toast')
            ->assertDispatched('swal'); // Se debe de lanzar el aviso de SweetAlert
    }

    public function test_no_dispara_swal_si_esta_dentro_de_los_limites()
    {
        Carbon::setTestNow('2026-02-27');

        $admin = User::factory()->create(['type' => 'admin']);
        $dept = Department::create(['description' => 'TEST DEPT', 'code' => '001']);
        
        // Empleado con 15 años de antigüedad (límite 60 días)
        $employee = Employe::create([
            'num_empleado' => '20202',
            'name' => 'Maria',
            'father_lastname' => 'Antiguedad',
            'mother_lastname' => 'Senior',
            'deparment_id' => $dept->id,
            'fecha_ingreso' => '2011-02-27',
        ]);

        $codigo55 = CodigoDeIncidencia::create(['code' => '55', 'description' => 'EG']);
        DB::statement('PRAGMA foreign_keys = OFF;');
        DB::table('puestos')->insert(['id' => 24, 'puesto' => 'MEDICO']);

        $doctor = Employe::create([
            'num_empleado' => 'DOC02',
            'name' => 'Dra. Quinn',
            'father_lastname' => 'Medicine',
            'mother_lastname' => 'Woman',
            'puesto_id' => '24',
            'deparment_id' => $dept->id,
        ]);

        Qna::create(['qna' => 3, 'year' => 2026, 'active' => '1']);
        Qna::create(['qna' => 4, 'year' => 2026, 'active' => '1']);

        // Captura de 10 días (Inferior a los 60 permitidos para 10+ años)
        Livewire::actingAs($admin)
            ->test(Manager::class, ['employeeId' => $employee->id])
            ->set('codigo', $codigo55->id)
            ->set('fechas_seleccionadas', '2026-02-01 to 2026-02-10') // 10 días
            ->set('medico_id', $doctor->id)
            ->set('num_licencia', 'LIC-456')
            ->set('diagnostico', 'RECUPERACION')
            ->set('fecha_expedida', '2026-02-01')
            ->call('store')
            ->assertDispatched('toast') // Siempre hay un toast de éxito
            ->assertNotDispatched('swal'); // NO debe de lanzarse la alerta
    }
}