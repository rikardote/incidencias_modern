<?php

namespace Tests\Feature\Legacy;

use App\Services\Incidencias\Rules\PaseSalidaRule;
use App\Models\Incidencia;
use App\Models\Employe;
use App\Models\Department;
use App\Models\Condicion;
use App\Models\Puesto;
use App\Models\Horario;
use App\Models\Jornada;
use App\Models\Qna;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaseSalidaRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_permite_pase_de_salida_a_personal_no_base()
    {
        $rule = new PaseSalidaRule();

        $dept = Department::create(['description' => 'Dept']);
        Condicion::create(['name' => 'Base']); // ID 1
        $cond = Condicion::create(['name' => 'No Base']); // ID 2
        $puesto = Puesto::create(['puesto' => 'Puesto']);
        $horario = Horario::create(['name' => 'Horario']);
        $jornada = Jornada::create(['name' => 'Jornada']);
        
        \DB::table('codigos_de_incidencias')->insert(['id' => 905, 'code' => '905', 'description' => 'PASE', 'created_at' => now(), 'updated_at' => now()]);
        \DB::table('qnas')->insert(['id' => 1, 'qna' => 1, 'year' => 2024, 'active' => '1', 'created_at' => now(), 'updated_at' => now()]);

        $empleado = Employe::create([
            'num_empleado' => 'TEST' . rand(1000, 9999),
            'name' => 'TEST USER',
            'father_lastname' => 'LASTNAME',
            'mother_lastname' => 'MOTHER',
            'deparment_id' => $dept->id,
            'condicion_id' => $cond->id, // ID 2
            'puesto_id' => $puesto->id,
            'horario_id' => $horario->id,
            'jornada_id' => $jornada->id
        ]);

        $incidencia = new Incidencia([
            'codigodeincidencia_id' => 905,
            'qna_id' => 1,
        ]);

        $this->expectException(DomainException::class);

        $rule->aplicar($incidencia, $empleado, [], 905);
    }

    public function test_no_permite_mas_de_un_pase_por_quincena()
    {
        $dept = Department::create(['description' => 'Dept']);
        $condBase = Condicion::create(['name' => 'Base']); // Assuming ID 1 is base or whatever is first
        $puesto = Puesto::create(['puesto' => 'Puesto']);
        $horario = Horario::create(['name' => 'Horario']);
        $jornada = Jornada::create(['name' => 'Jornada']);
        
        \DB::table('codigos_de_incidencias')->insert(['id' => 905, 'code' => '905', 'description' => 'PASE', 'created_at' => now(), 'updated_at' => now()]);
        \DB::table('qnas')->insert(['id' => 1, 'qna' => 1, 'year' => 2024, 'active' => '1', 'created_at' => now(), 'updated_at' => now()]);

        // If PaseSalidaRule expects id 1 for base, we should ensure it.
        // Let's look at PaseSalidaRule.php
        
        $empleado = Employe::create([
            'num_empleado' => 'TEST' . rand(1000, 9999),
            'name' => 'TEST USER',
            'father_lastname' => 'LASTNAME',
            'mother_lastname' => 'MOTHER',
            'deparment_id' => $dept->id,
            'condicion_id' => 1, // BASE
            'puesto_id' => $puesto->id,
            'horario_id' => $horario->id,
            'jornada_id' => $jornada->id
        ]);

        // Pase ya existente
        Incidencia::create([
            'employee_id' => $empleado->id,
            'qna_id' => 1,
            'codigodeincidencia_id' => 905,
            'fecha_inicio' => '2024-01-01',
            'fecha_final' => '2024-01-01',
        ]);

        $incidencia = new Incidencia([
            'employee_id' => $empleado->id,
            'qna_id' => 1,
            'codigodeincidencia_id' => 905,
        ]);

        $rule = new PaseSalidaRule();

        $this->expectException(DomainException::class);

        $rule->aplicar($incidencia, $empleado, [], 905);
    }
}
