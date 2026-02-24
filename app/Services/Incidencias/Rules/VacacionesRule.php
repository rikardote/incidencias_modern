<?php

namespace App\Services\Incidencias\Rules;

use App\Models\Incidencia;
use App\Models\Periodo;
use App\Models\Employe;
use App\Constants\Incidencias as Inc;

class VacacionesRule implements IncidenciaRuleInterface
{
    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal)
    {
        if (!Inc::esVacacional($codeReal)) {
            return;
        }

        if (empty($data['periodo_id'])) {
            throw new \DomainException('Debe seleccionar un periodo vacacional');
        }
        $incidencia->periodo_id = $data['periodo_id'];

        $total_dias_solicitados = $incidencia->total_dias;

        // 1. Verificar si se debe saltar la validación
        // Nota: $data viene como array desde el Service
        if (isset($data['saltar_validacion']) && ($data['saltar_validacion'] == 'true' || $data['saltar_validacion'] == 1)) {
            return;
        }

        // 2. Obtener lo que ya tiene capturado en la base de datos para ese periodo y código
        // Usamos tu método estático original de la clase Incidencia
        $vacaciones_en_bd = Incidencia::getTotalVacaciones(
            $incidencia->employee_id,
            $incidencia->periodo_id,
            $incidencia->codigodeincidencia_id
        );

        // 3. Buscar información del periodo para el mensaje de error
        $vacacion = Periodo::find($incidencia->periodo_id);
        $nombre_periodo = $vacacion ? $vacacion->periodo . '-' . $vacacion->year : 'N/A';

        // 4. La suma crítica: Lo que ya hay + lo que se pide en TODO el rango
        $total_proyectado = $vacaciones_en_bd + $total_dias_solicitados;

        // 5. Validaciones por grupo
        if (in_array($empleado->jornada_id, Inc::JORNADA_SYF_DYF) && $total_proyectado > 2) {
            throw new \DomainException("Ya tiene $vacaciones_en_bd de 2 dias gozados del periodo $nombre_periodo");
        }

        if (in_array($empleado->jornada_id, Inc::JORNADA_VAC_5_DIAS) && $total_proyectado > 5) {
            throw new \DomainException("Ya tiene $vacaciones_en_bd de 5 dias gozados del periodo $nombre_periodo");
        }

        if (in_array($empleado->jornada_id, Inc::JORNADA_VAC_6_DIAS) && $total_proyectado > 6) {
            throw new \DomainException("Ya tiene $vacaciones_en_bd de 6 dias gozados del periodo $nombre_periodo");
        }

        if (in_array($empleado->jornada_id, Inc::JORNADA_MAT_DESP) && $total_proyectado > 10) {
            throw new \DomainException("Ya tiene $vacaciones_en_bd de 10 dias gozados del periodo $nombre_periodo");
        }

        return;
    }
}
