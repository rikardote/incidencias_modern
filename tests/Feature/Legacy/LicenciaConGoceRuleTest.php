<?php

namespace Tests\Feature\Legacy;

use App\Services\Incidencias\Rules\LicenciaConGoceRule;
use App\Models\Incidencia;
use App\Models\Employe;
use DomainException;
use App\Constants\Incidencias as Inc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenciaConGoceRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_ignora_codigo_que_no_es_licencia()
    {
        $rule = new LicenciaConGoceRule(new \App\Services\Incidencias\IncidenciaHelpersService());

        $this->assertNull($rule->aplicar(new Incidencia(), new Employe(), [], Inc::FALTA));
        $this->assertNull($rule->aplicar(new Incidencia(), new Employe(), [], Inc::VACACIONES[0]));
    }

    public function test_salta_validacion_compleja_si_esta_marcado_en_data()
    {
        $rule = new LicenciaConGoceRule(new \App\Services\Incidencias\IncidenciaHelpersService());

        $data = [
            'saltar_validacion_lic' => 1
        ];

        // 41 es Licencia con Goce. Si no saltara, intentaría conectarse a DB.
        $this->assertNull($rule->aplicar(new Incidencia(), new Employe(), $data, 41));
    }
}
