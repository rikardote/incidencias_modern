<?php

namespace App\Services\Incidencias\Rules;

use App\Models\Incidencia;
use DomainException;
use DB;
use App\Models\Employe;
use App\Constants\Incidencias as Inc;

class PaseSalidaRule implements IncidenciaRuleInterface
{
    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal)
    {
        if (!Inc::esPaseSalida($codeReal)) {
            return;
        }
        // 1. Validar Condición de Base (Ya funcionando)
        if ((int)$empleado->condicion_id !== 1) {
            throw new DomainException(
                'Pases de salida solo válidos para personal de BASE'
            );
        }

        // 2. Validar que no exista otro pase en la misma QNA
        // Buscamos en la base de datos si el empleado ya tiene un registro activo
        // para esta quincena específica y con este mismo código.
        $yaExiste = DB::table('incidencias')
            ->where('employee_id', $empleado->id)
            ->where('qna_id', $incidencia->qna_id)
            ->where('codigodeincidencia_id', $incidencia->codigodeincidencia_id)
            ->whereNull('deleted_at') // Importante: no contar los eliminados
            ->exists();

        if ($yaExiste) {
            throw new DomainException(
                'El empleado ya gozo de un Pase de Salida en la quincena seleccionada. Solo se permite uno por quincena.'
            );
        }
    }
}
