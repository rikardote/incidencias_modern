<?php

namespace App\Services\Incidencias;

use Carbon\Carbon;

class SegmentadorQuincenal
{
    /**
     * Divide un rango de fechas en segmentos quincenales exactos.
     * Retorna un arreglo de segmentos conteniendo fecha de inicio, fecha final, total de días y qna_id.
     *
     * @param string $fechaInicial Fecha inicial en algún formato admitido por fecha_ymd()
     * @param string $fechaFinal Fecha final en algún formato admitido por fecha_ymd()
     * @return array
     */
    public static function calcularSegmentos($fechaInicial, $fechaFinal, callable $qnaResolver = null)
    {
        $inicio = Carbon::parse($fechaInicial);
        $fin    = Carbon::parse($fechaFinal);
        $segmentos = [];
        $actual = $inicio->copy();

        while ($actual->lte($fin)) {
            $qnaId = $qnaResolver ? $qnaResolver($actual->format('Y-m-d')) : qna_year($actual->format('Y-m-d'));
            
            if (!$qnaId) {
                throw new \DomainException("La incidencia toca fechas cuya Quincena correspondiente aún no ha sido aperturada por el administrador del sistema (" . $actual->format('m/Y') . ").");
            }

            $finQna = ($actual->day <= 15) ? $actual->copy()->day(15) : $actual->copy()->day($actual->daysInMonth);
            $finSegmento = $finQna->lt($fin) ? $finQna : $fin->copy();

            $segmentos[] = [
                'fecha_inicio' => $actual->format('Y-m-d'),
                'fecha_final'  => $finSegmento->format('Y-m-d'),
                'qna_id'       => $qnaId,
                'total_dias'   => $actual->diffInDays($finSegmento) + 1
            ];

            $actual = $finSegmento->addDay();
        }

        return $segmentos;
    }
}
