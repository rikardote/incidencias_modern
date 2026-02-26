<?php

namespace Tests\Feature\Legacy;

use App\Services\Incidencias\Rules\IncapacidadRule;
use App\Models\Incidencia;
use App\Models\Employe;
use DomainException;
use App\Constants\Incidencias as Inc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncapacidadRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_valida_si_no_es_incapacidad()
    {
        $rule = new IncapacidadRule(new \App\Services\Incidencias\IncidenciaHelpersService());
        $incidencia = new Incidencia();
        $empleado = new Employe();

        $this->assertNull($rule->aplicar($incidencia, $empleado, [], Inc::VACACIONES[0]));
    }

    public function test_lanza_excepcion_si_falta_medico()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe seleccionar un médico');

        $rule = new IncapacidadRule(new \App\Services\Incidencias\IncidenciaHelpersService());
        $data = [
            'saltar_validacion_inca' => 0, 
            'datepicker_expedida' => '2023-10-01',
            'diagnostico' => 'Gripe',
            'num_licencia' => 'A123'
        ];

        $rule->aplicar(new Incidencia(), new Employe(), $data, 53); // 53 = Incapacidad
    }

    public function test_lanza_excepcion_si_falta_fecha_expedida()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe capturar la fecha expedida');

        $rule = new IncapacidadRule(new \App\Services\Incidencias\IncidenciaHelpersService());
        $data = [
            'saltar_validacion_inca' => 0,
            'medico_id' => 1,
            'diagnostico' => 'Gripe',
            'num_licencia' => 'A123'
        ];

        $rule->aplicar(new Incidencia(), new Employe(), $data, 53);
    }

    public function test_lanza_excepcion_si_falta_diagnostico()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe capturar el diagnóstico');

        $rule = new IncapacidadRule(new \App\Services\Incidencias\IncidenciaHelpersService());
        $data = [
            'saltar_validacion_inca' => 0,
            'medico_id' => 1,
            'datepicker_expedida' => '2023-10-01',
            'num_licencia' => 'A123'
        ];

        $rule->aplicar(new Incidencia(), new Employe(), $data, 53);
    }
}
