<?php

namespace Tests\Feature;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\Qna;
use App\Models\CodigoDeIncidencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidenciaTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_total_vacaciones(): void
    {
        $employee = Employe::create([
            'num_empleado' => '12345',
            'name' => 'Test',
            'father_lastname' => 'User',
            'mother_lastname' => 'Test',
        ]);

        $codigo = CodigoDeIncidencia::create(['code' => 'VAC', 'description' => 'Vacaciones']);
        $periodo = \App\Models\Periodo::create(['name' => '2024-1']);
        $qna = Qna::create(['qna' => 1, 'year' => 2024]);

        Incidencia::create([
            'employee_id' => $employee->id,
            'codigodeincidencia_id' => $codigo->id,
            'qna_id' => $qna->id,
            'fecha_inicio' => '2024-01-01',
            'fecha_final' => '2024-01-05',
            'total_dias' => 5,
            'periodo_id' => $periodo->id,
        ]);

        Incidencia::create([
            'employee_id' => $employee->id,
            'codigodeincidencia_id' => $codigo->id,
            'qna_id' => $qna->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_final' => '2024-02-03',
            'total_dias' => 3,
            'periodo_id' => $periodo->id,
        ]);

        $this->assertEquals(8, Incidencia::getTotalVacaciones($employee->id, $periodo->id, $codigo->id));
    }

    public function test_get_total_licencias_code_41(): void
    {
        $employee = Employe::create([
            'num_empleado' => '12345',
            'name' => 'Test',
            'father_lastname' => 'User',
            'mother_lastname' => 'Test',
        ]);

        // Code 41 must have ID 3 according to Incidencia model
        $codigo = new CodigoDeIncidencia();
        $codigo->id = 3;
        $codigo->code = '41';
        $codigo->description = 'Licencia';
        $codigo->save();

        $qna = Qna::create(['qna' => 1, 'year' => 2024]);

        Incidencia::create([
            'employee_id' => $employee->id,
            'codigodeincidencia_id' => 3,
            'qna_id' => $qna->id,
            'fecha_inicio' => '2024-01-01',
            'fecha_final' => '2024-01-05',
            'total_dias' => 5,
        ]);

        $this->assertEquals(5, Incidencia::getTotalLicencias('12345', '2024-01-01', '2024-12-31'));
    }

    public function test_get_txt_por_mes_code_900(): void
    {
        $employee = Employe::create([
            'num_empleado' => '12345',
            'name' => 'Test',
            'father_lastname' => 'User',
            'mother_lastname' => 'Test',
        ]);

        $codigo = CodigoDeIncidencia::create(['code' => '900', 'description' => 'TXT']);
        $qna = Qna::create(['qna' => 1, 'year' => 2024]);

        Incidencia::create([
            'employee_id' => $employee->id,
            'codigodeincidencia_id' => $codigo->id,
            'qna_id' => $qna->id,
            'fecha_inicio' => '2024-01-01',
            'fecha_final' => '2024-01-02',
            'total_dias' => 2,
        ]);

        $this->assertEquals(2, Incidencia::Gettxtpormes('2024-01-01', '2024-12-31', '12345'));
    }
}
