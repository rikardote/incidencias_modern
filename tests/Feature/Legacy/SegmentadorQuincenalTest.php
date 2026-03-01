<?php

namespace Tests\Feature\Legacy;

use App\Services\Incidencias\SegmentadorQuincenal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SegmentadorQuincenalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que las fechas dentro de la misma quincena retornan un solo segmento.
     */
    public function test_periodo_dentro_de_misma_quincena()
    {
        $inicio = '2023-10-01';
        $fin = '2023-10-05';

        // Mockeamos la dependencia de BD devolviendo un ID falso estático.
        $segmentos = SegmentadorQuincenal::calcularSegmentos($inicio, $fin, function($fecha) { return 99; });

        $this->assertCount(1, $segmentos, 'Debe generar solo un segmento si las fechas no cruzan de quincena.');

        $this->assertEquals('2023-10-01', $segmentos[0]['fecha_inicio']);
        $this->assertEquals('2023-10-05', $segmentos[0]['fecha_final']);
        $this->assertEquals(5, $segmentos[0]['total_dias']);
        $this->assertEquals(99, $segmentos[0]['qna_id']);
    }

    /**
     * Prueba que cruzar el día 15 genera dos segmentos en la misma mes.
     */
    public function test_periodo_cruza_dia_quince()
    {
        $inicio = '2023-10-14';
        $fin = '2023-10-18';

        $segmentos = SegmentadorQuincenal::calcularSegmentos($inicio, $fin, function($fecha) { return 99; });

        $this->assertCount(2, $segmentos, 'Debe generar dos segmentos al cruzar el día 15.');

        // Segmento 1 (hasta el 15)
        $this->assertEquals('2023-10-14', $segmentos[0]['fecha_inicio']);
        $this->assertEquals('2023-10-15', $segmentos[0]['fecha_final']);
        $this->assertEquals(2, $segmentos[0]['total_dias']);

        // Segmento 2 (desde el 16)
        $this->assertEquals('2023-10-16', $segmentos[1]['fecha_inicio']);
        $this->assertEquals('2023-10-18', $segmentos[1]['fecha_final']);
        $this->assertEquals(3, $segmentos[1]['total_dias']);
    }

    /**
     * Prueba salto a un nuevo mes.
     */
    public function test_periodo_cruza_fin_de_mes()
    {
        $inicio = '2023-10-29';
        $fin = '2023-11-02';

        $segmentos = SegmentadorQuincenal::calcularSegmentos($inicio, $fin, function($fecha) { return 99; });

        $this->assertCount(2, $segmentos, 'Debe generar dos segmentos al cruzar fin de mes.');

        // Segmento 1 (hasta el 31)
        $this->assertEquals('2023-10-29', $segmentos[0]['fecha_inicio']);
        $this->assertEquals('2023-10-31', $segmentos[0]['fecha_final']);
        $this->assertEquals(3, $segmentos[0]['total_dias']);

        // Segmento 2 (desde el día 1 del mes nuevo)
        $this->assertEquals('2023-11-01', $segmentos[1]['fecha_inicio']);
        $this->assertEquals('2023-11-02', $segmentos[1]['fecha_final']);
        $this->assertEquals(2, $segmentos[1]['total_dias']);
    }
}
