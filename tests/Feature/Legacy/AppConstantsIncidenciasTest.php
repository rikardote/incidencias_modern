<?php

namespace Tests\Feature\Legacy;

use App\Constants\Incidencias as Inc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppConstantsIncidenciasTest extends TestCase
{
    use RefreshDatabase;

    public function test_valida_es_guardia()
    {
        // El id 2 es guardia según el array
        $this->assertTrue(Inc::esGuardia(2));
        $this->assertTrue(Inc::esGuardia(34));

        // El 1 o 15 son Syf/Dyf, no guardia
        $this->assertFalse(Inc::esGuardia(1));
        $this->assertFalse(Inc::esGuardia(15));
    }

    public function test_valida_es_syf_y_dyf()
    {
        $this->assertTrue(Inc::esSyfDyf(1));
        $this->assertTrue(Inc::esSyfDyf(15));

        $this->assertFalse(Inc::esSyfDyf(2));
    }

    public function test_valida_asignacion_de_dias_por_jornada()
    {
        // 2 (guardia) -> 2 días
        $this->assertEquals(2, Inc::diasPorJornada(2));

        // 1 (Syf/Dyf) -> 4 días
        $this->assertEquals(4, Inc::diasPorJornada(1));

        // Jornada base MatDesp o no especial -> null
        $this->assertNull(Inc::diasPorJornada(14));
    }

    public function test_valida_tipos_de_codigos()
    {
        // Incapacidad (53, 54, 55)
        $this->assertTrue(Inc::esIncapacidad(53));
        $this->assertFalse(Inc::esIncapacidad(40));

        // Licencia (40, 41, 47, 48, 49)
        $this->assertTrue(Inc::esLicencia(48));
        $this->assertFalse(Inc::esLicencia(53));

        // Vacacional (60, 62, 63)
        $this->assertTrue(Inc::esVacacional(60));
        $this->assertFalse(Inc::esVacacional(40));

        // Pase de salida (905)
        $this->assertTrue(Inc::esPaseSalida(905));
        $this->assertFalse(Inc::esPaseSalida(900));
    }
}
