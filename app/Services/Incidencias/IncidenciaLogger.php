<?php

namespace App\Services;

use Log;

class IncidenciaLogger
{
    public static function rechazo($regla, $empleado, $incidencia, $mensaje)
    {
        Log::info('INCIDENCIA RECHAZADA', [
            'regla'        => $regla,
            'empleado_id'  => $empleado->id,
            'num_empleado' => $empleado->num_empleado,
            'codigo'       => $incidencia->codigodeincidencia_id,
            'fecha_inicio' => $incidencia->fecha_inicio,
            'fecha_final'  => $incidencia->fecha_final,
            'mensaje'      => $mensaje,
            'usuario'      => auth()->user()->id ?? null,
        ]);
    }
}
