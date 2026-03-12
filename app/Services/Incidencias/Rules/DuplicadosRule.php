<?php

namespace App\Services\Incidencias\Rules;

use App\Models\Incidencia;
use App\Models\Employe;
use App\Constants\Incidencias as Inc;

class DuplicadosRule
{
    public function yaCapturado(Employe $empleado, $fecha_inicio, $fecha_final, $codigo_nuevo)
    {
        // 1. Estos son los CÓDIGOS de texto que sí pueden convivir mapeados como string
        $codigos_compatibles = array_map('strval', Inc::DUPLICABLES);
        $codigo_nuevo_str = (string) $codigo_nuevo;

        // 2. Consulta base con Join para traer el código de texto desde el catálogo
        $query = Incidencia::join('codigos_de_incidencias', 'incidencias.codigodeincidencia_id', '=', 'codigos_de_incidencias.id')
            ->where('incidencias.employee_id', $empleado->id)
            ->where(function ($q) use ($fecha_inicio, $fecha_final) {
                $q->where('incidencias.fecha_inicio', '<=', $fecha_final)
                ->where('incidencias.fecha_final', '>=', $fecha_inicio);
            })
            ->whereNull('incidencias.deleted_at');

        // 3. Regla Excepción 905 (Pase de Salida)
        // Al personal distinto a matutino o vespertino (NO 14, NO 17),
        // se puede capturar 905 el mismo día que los siguientes códigos:
        $jornadaId = (int) $empleado->jornada_id;
        $esMatVesp = in_array($jornadaId, Inc::JORNADA_MAT_DESP);

        if (!$esMatVesp) {
            $excepcion_905_codigos = ['60', '62', '900', '17', '61', '901', '55', '53', '40', '41', '42', '47', '48', '49'];

            if ($codigo_nuevo_str === '905') {
                // Si estamos capturando 905, ignoramos todos los que estén en la lista de compatibles extendida
                $codigos_compatibles = array_unique(array_merge($codigos_compatibles, $excepcion_905_codigos));
            } else if (in_array($codigo_nuevo_str, $excepcion_905_codigos)) {
                // Si capturamos uno de esos códigos excepcionales, e internamente ya hay un 905 registrado,
                // no debemos considerarlo como traslape.
                $query->where('codigos_de_incidencias.code', '!=', '905');
            }
        }

        // 4. Lógica de validación mejorada
        if (in_array($codigo_nuevo_str, $codigos_compatibles)) {
            /**
             * SI EL NUEVO ES COMPATIBLE:
             * Buscamos si hay alguna incidencia cuyo CÓDIGO NO esté en la lista.
             */
            return $query->whereNotIn('codigos_de_incidencias.code', $codigos_compatibles)->first(['codigos_de_incidencias.code']);
        } else {
            /**
             * SI EL NUEVO NO ES COMPATIBLE:
             * Cualquier traslape bloquea el registro.
             */
            return $query->first(['codigos_de_incidencias.code']);
        }
    }
}
