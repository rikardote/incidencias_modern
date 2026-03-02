<?php

namespace Tests\Feature\Legacy;

use App\Services\Incidencias\Rules\TXTRule;
use App\Models\Incidencia;
use App\Models\Employe;
use DomainException;
use App\Constants\Incidencias as Inc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TXTRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_valida_si_el_codigo_no_es_txt()
    {
        $rule = new TXTRule(new \App\Services\Incidencias\IncidenciaHelpersService());
        $incidencia = new Incidencia();
        $empleado = new Employe();

        // Simular un código que NO es TXT (ej. vacaciones)
        // La regla debería hacer un return temprano y no lanzar excepción.
        $this->assertNull($rule->aplicar($incidencia, $empleado, [], Inc::VACACIONES[0]));
    }

    public function test_lanza_excepcion_si_falta_cobertura_txt()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe especificar el sustituto');

        $rule = new TXTRule(new \App\Services\Incidencias\IncidenciaHelpersService());
        $incidencia = new Incidencia();
        $empleado = new Employe();

        // El array $data no trae cobertura_txt
        $data = [
            'autoriza_txt' => 'Dr. Simi'
        ];

        $rule->aplicar($incidencia, $empleado, $data, Inc::TXT);
    }

    public function test_lanza_excepcion_si_falta_autoriza_txt()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe especificar quién autorizó el cambio de guardia');

        $rule = new TXTRule(new \App\Services\Incidencias\IncidenciaHelpersService());
        $incidencia = new Incidencia();
        $empleado = new Employe();

        // El array $data SÍ trae cobertura, pero NO autoriza_txt
        $data = [
            'cobertura_txt' => 'Juan Perez'
        ];

        $rule->aplicar($incidencia, $empleado, $data, Inc::TXT);
    }
}
