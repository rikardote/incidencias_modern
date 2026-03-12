<?php

namespace App\Services\Incidencias\Rules;

use App\Constants\Incidencias as Inc;
use App\Models\Incidencia;
use App\Models\Employe;
use DomainException;
use Carbon\Carbon;

class OnomasticoRule implements IncidenciaRuleInterface
{
    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal)
    {
        // No es Onomástico
        if ($codeReal != Inc::ONOMASTICO) {
            return;
        }

        // Solo personal de base y confianza tienen derecho a uno por año
        $permitidos = [Inc::CONDICION_BASE, Inc::CONDICION_CONFIANZA];
        if (!in_array($empleado->condicion_id, $permitidos)) {
            // Si el usuario no especificó restricción para otros, podrías dejarlo pasar o bloquearlo.
            // Según la solicitud, la regla aplica a base y confianza.
            return;
        }

        $year = Carbon::parse($incidencia->fecha_inicio)->year;

        $yaUsado = Incidencia::where('employee_id', $empleado->id)
            ->join('codigos_de_incidencias', 'incidencias.codigodeincidencia_id', '=', 'codigos_de_incidencias.id')
            ->where('codigos_de_incidencias.code', '14')
            ->whereYear('incidencias.fecha_inicio', $year)
            ->whereNull('incidencias.deleted_at')
            ->exists();

        if ($yaUsado) {
            throw new DomainException("El empleado ya cuenta con un código 14 (Onomástico) registrado en el año {$year}.");
        }
    }
}
