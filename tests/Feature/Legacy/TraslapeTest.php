<?php

namespace Tests\Feature\Legacy;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\Department;
use App\Models\Condicion;
use App\Models\Puesto;
use App\Models\Horario;
use App\Models\Jornada;
use App\Models\Qna;
use App\Services\Incidencias\Rules\DuplicadosRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraslapeTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_permite_incapacidad_si_ya_hay_falta_en_el_rango()
    {
        $dept = Department::create(['description' => 'Test Dept']);
        $cond = Condicion::create(['name' => 'Test Cond']);
        $puesto = Puesto::create(['puesto' => 'Test Puesto']);
        $horario = Horario::create(['name' => 'Test Horario']);
        $jornada = Jornada::create(['name' => 'Test Jornada']);
        $qna = Qna::create(['qna' => 1, 'year' => 2024, 'active' => '1']);

        \App\Models\CodigoDeIncidencia::create(['id' => 1, 'code' => '1', 'description' => 'FALTA']);
        \App\Models\CodigoDeIncidencia::create(['id' => 55, 'code' => '55', 'description' => 'INCAP']);

        $empleado = Employe::create([
            'num_empleado' => 'TEST' . rand(1000, 9999),
            'name' => 'TEST USER',
            'father_lastname' => 'LASTNAME',
            'mother_lastname' => 'MOTHER',
            'deparment_id' => $dept->id,
            'condicion_id' => $cond->id,
            'puesto_id' => $puesto->id,
            'horario_id' => $horario->id,
            'jornada_id' => $jornada->id
        ]);

        // Código 1 el 6 de febrero
        Incidencia::create([
            'employee_id' => $empleado->id,
            'codigodeincidencia_id' => 1,
            'qna_id' => $qna->id, // Required in modern app
            'fecha_inicio' => '2024-02-06',
            'fecha_final' => '2024-02-06',
        ]);

        $rule = new DuplicadosRule();

        $resultado = $rule->yaCapturado(
            $empleado->id,
            '2024-02-05',
            '2024-02-07',
            55 // incapacidad
        );

        $this->assertTrue($resultado);
    }
}
