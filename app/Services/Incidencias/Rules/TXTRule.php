<?php

namespace App\Services\Incidencias\Rules;


use App\Constants\Incidencias as Inc;
use DomainException;
use App\Models\Incidencia;
use App\Models\Employe;

class TXTRule implements IncidenciaRuleInterface
{
    protected $helpers;

    public function __construct(\App\Services\Incidencias\IncidenciaHelpersService $helpers = null)
    {
        $this->helpers = $helpers ?: app(\App\Services\Incidencias\IncidenciaHelpersService::class);
    }

    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal)
    {
        // No es TXT
        if ($codeReal != Inc::TXT) {
            return;
        }

        // Asignar campos específicos de TXT antes de validar y guardar
        if (isset($data['cobertura_txt'])) $incidencia->cobertura_txt = $data['cobertura_txt'];
        if (isset($data['autoriza_txt'])) $incidencia->autoriza_txt = $data['autoriza_txt'];

        // Campos obligatorios
        if (!$incidencia->cobertura_txt) {
            throw new DomainException('Debe especificar el sustituto');
        }

        if (!$incidencia->autoriza_txt) {
            throw new DomainException('Debe especificar quién autorizó el cambio de guardia');
        }

        // Total TXT del mes
        $usados = $this->helpers->txtUsadoPorMes(
            $empleado->num_empleado,
            $incidencia->fecha_inicio
        );

        $total = $usados + $incidencia->total_dias;

        // Topes por jornada
        if (
            in_array($empleado->jornada_id, Inc::JORNADA_MAT_DESP) &&
            $total > 5
        ) {
            throw new DomainException(
                'Trabajador no puede gozar más de 5 días de T.X.T'
            );
        }

        if (
            Inc::esSyfDyf($empleado->jornada_id) &&
            $total > 1
        ) {
            throw new DomainException(
                'Trabajador no puede gozar más de 1 día de T.X.T'
            );
        }

        if (
            Inc::esGuardia($empleado->jornada_id) &&
            $total > 2
        ) {
            throw new DomainException(
                'Trabajador no puede gozar más de 2 días de T.X.T'
            );
        }
    }
}
