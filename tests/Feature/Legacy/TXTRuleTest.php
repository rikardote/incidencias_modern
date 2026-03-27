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
        $empleado->condicion_id = Inc::CONDICION_BASE;

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
        $empleado->condicion_id = Inc::CONDICION_BASE;

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
        $empleado->condicion_id = Inc::CONDICION_BASE;

        // El array $data SÍ trae cobertura, pero NO autoriza_txt
        $data = [
            'cobertura_txt' => 'Juan Perez'
        ];

        $rule->aplicar($incidencia, $empleado, $data, Inc::TXT);
    }

    public function test_lanza_excepcion_si_excede_limite_mensual_matutino()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Trabajador no puede gozar más de 5 días de T.X.T');

        $helpers = $this->createMock(\App\Services\Incidencias\IncidenciaHelpersService::class);
        $helpers->method('txtUsadoPorMes')->willReturn(5); // Ya usó 5

        $rule = new TXTRule($helpers);
        $incidencia = new Incidencia(['total_dias' => 1, 'fecha_inicio' => '2024-01-01']);
        $empleado = new Employe(['condicion_id' => Inc::CONDICION_BASE, 'num_empleado' => '123', 'jornada_id' => Inc::JORNADA_MAT_DESP[0]]);

        $data = ['cobertura_txt' => 'Sustituto', 'autoriza_txt' => 'Jefe'];

        $rule->aplicar($incidencia, $empleado, $data, Inc::TXT);
    }

    public function test_permite_exceder_limite_si_configuracion_activa()
    {
        // Activar el bypass en la base de datos
        \App\Models\Configuration::set('unlock_txt_limits', true);

        $helpers = $this->createMock(\App\Services\Incidencias\IncidenciaHelpersService::class);
        $helpers->method('txtUsadoPorMes')->willReturn(5); // Ya usó 5

        $rule = new TXTRule($helpers);
        $incidencia = new Incidencia(['total_dias' => 1, 'fecha_inicio' => '2024-01-01']);
        $empleado = new Employe(['condicion_id' => Inc::CONDICION_BASE, 'num_empleado' => '123', 'jornada_id' => Inc::JORNADA_MAT_DESP[0]]);

        $data = ['cobertura_txt' => 'Sustituto', 'autoriza_txt' => 'Jefe'];

        // No debería lanzar excepción porque está desbloqueado
        $rule->aplicar($incidencia, $empleado, $data, Inc::TXT);
        
        $this->assertTrue(true);
    }
}
