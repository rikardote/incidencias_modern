<?php

namespace App\Services\Incidencias\Rules;

use App\Models\Incidencia;
use App\Models\Employe;
use DomainException;
use Illuminate\Support\Facades\DB;

class Limite0809Rule implements IncidenciaRuleInterface
{
    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal)
    {
        // 1. Aplica solo para códigos 08 y 09
        if (!in_array($codeReal, [8, 9])) {
            return;
        }

        // 2. Contar cuántas incidencias de códigos 08 y 09 tiene en esta quincena
        $count = DB::table('incidencias')
            ->join('codigos_de_incidencias', 'incidencias.codigodeincidencia_id', '=', 'codigos_de_incidencias.id')
            ->where('incidencias.employee_id', $empleado->id)
            ->where('incidencias.qna_id', $incidencia->qna_id)
            ->whereIn('codigos_de_incidencias.code', ['08', '09', '8', '9'])
            ->whereNull('incidencias.deleted_at')
            ->count();

        // 3. Evaluar si con la nueva incidencia se supera el límite de 2
        // Nota: La incidencia actual apenas se va a guardar, por lo que el conteo en BD no la incluye.
        if ($count >= 2) {
            throw new DomainException(
                'No se permite registrar más de 2 incidencias con clave 08 o 09 (combinadas) en la misma quincena.'
            );
        }
    }
}
