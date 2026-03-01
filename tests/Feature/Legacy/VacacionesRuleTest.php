<?php

namespace Tests\Feature\Legacy;

use App\Services\Incidencias\Rules\VacacionesRule;
use App\Models\Incidencia;
use App\Models\Employe;
use DomainException;
use App\Constants\Incidencias as Inc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacacionesRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_valida_si_codigo_no_es_vacacional()
    {
        $rule = new VacacionesRule();

        // Código 10 (Falta) no debe activar la regla Vacacional
        $this->assertNull($rule->aplicar(new Incidencia(), new Employe(), [], Inc::FALTA));
    }

    public function test_lanza_excepcion_si_periodo_esta_vacio()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe seleccionar un periodo vacacional');

        $rule = new VacacionesRule();

        $data = [
            'saltar_validacion' => 0,
            // Falta periodo_id
        ];

        $rule->aplicar(new Incidencia(), new Employe(), $data, Inc::VACACIONES[0]);
    }

    public function test_salta_validacion_compleja_si_esta_marcado_en_data()
    {
        $rule = new VacacionesRule();

        $data = [
            'saltar_validacion' => 1,
            'periodo_id' => 5
        ];

        $incidencia = new Incidencia();
        $incidencia->total_dias = 4;
        $empleado = new Employe();
        $empleado->jornada_id = 14; // Matutino por ejemplo

        // Si no saltara, intentaría conectarse a DB para calcular los excesos
        $this->assertNull($rule->aplicar($incidencia, $empleado, $data, Inc::VACACIONES[0]));

        // Verifica que se le asignó el periodo a pesar de saltarse
        $this->assertEquals(5, $incidencia->periodo_id);
    }

    public function test_lanza_excepcion_si_no_tiene_jornada_asignada()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe asignar jornada laboral al trabajador');

        $rule = new VacacionesRule();

        $data = [
            'periodo_id' => 1
        ];

        $empleado = new Employe();
        $empleado->jornada_id = null; // No tiene jornada

        $rule->aplicar(new Incidencia(), $empleado, $data, Inc::VACACIONES[0]);
    }
}