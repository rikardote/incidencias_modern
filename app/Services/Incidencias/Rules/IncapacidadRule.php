<?php

namespace App\Services\Incidencias\Rules;

use App\Models\Incidencia;
use App\Models\Employe;
use App\Constants\Incidencias as Inc;

class IncapacidadRule implements IncidenciaRuleInterface
{
    protected $helpers;

    public function __construct(\App\Services\Incidencias\IncidenciaHelpersService $helpers = null)
    {
        $this->helpers = $helpers ?: app(\App\Services\Incidencias\IncidenciaHelpersService::class);
    }

    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal)
    {
        if (!Inc::esIncapacidad($codeReal)) {
            return;
        }

        $saltarInca = (isset($data['saltar_validacion_inca']) && $data['saltar_validacion_inca'] == "1");

        if ($saltarInca) {
            return;
        }

        if (!isset($data['medico_id']) || !is_numeric($data['medico_id']) || $data['medico_id'] <= 0) {
            throw new \DomainException('Debe seleccionar un médico');
        }

        if (empty($data['datepicker_expedida'])) {
            throw new \DomainException('Debe capturar la fecha expedida');
        }

        if (empty($data['diagnostico'])) {
            throw new \DomainException('Debe capturar el diagnóstico');
        }

        if (empty($data['num_licencia'])) {
            throw new \DomainException('Debe capturar el número de licencia');
        }

        $incidencia->medico_id = $data['medico_id'];
        $incidencia->fecha_expedida = $this->helpers->fechaYmd($data['datepicker_expedida']);
        $incidencia->diagnostico = $data['diagnostico'];
        $incidencia->num_licencia = $data['num_licencia'];
    }
}
