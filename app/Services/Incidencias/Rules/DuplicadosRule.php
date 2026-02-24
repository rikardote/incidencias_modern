<?php

namespace App\Services\Incidencias\Rules;

use App\Models\Incidencia;

use App\Constants\Incidencias as Inc;

class DuplicadosRule
{
    public function yaCapturado($employee_id, $fecha_inicio, $fecha_final, $codigo_nuevo)
    {
        // 1. Estos son los CÓDIGOS de texto que sí pueden convivir mapeados como string
        $codigos_compatibles = array_map('strval', Inc::DUPLICABLES);

        // 2. Consulta base con Join para traer el código de texto desde el catálogo
        $query = Incidencia::join('codigos_de_incidencias', 'incidencias.codigodeincidencia_id', '=', 'codigos_de_incidencias.id')
            ->where('incidencias.employee_id', $employee_id)
            ->where(function ($q) use ($fecha_inicio, $fecha_final) {
                $q->where('incidencias.fecha_inicio', '<=', $fecha_final)
                ->where('incidencias.fecha_final', '>=', $fecha_inicio);
            });

        // 3. Lógica de validación mejorada

        if (in_array((string)$codigo_nuevo, $codigos_compatibles)) {
            /**
             * SI EL NUEVO ES COMPATIBLE:
             * Buscamos si hay alguna incidencia cuyo CÓDIGO NO esté en la lista.
             */
            return $query->whereNotIn('codigos_de_incidencias.code', $codigos_compatibles)->exists();
        } else {
            /**
             * SI EL NUEVO NO ES COMPATIBLE:
             * Cualquier traslape bloquea el registro.
             */
            return $query->exists();
        }
    }
}
