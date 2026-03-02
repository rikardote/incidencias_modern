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

        // --- REVISIÓN DE EXCESOS (SOLO PARA CÓDIGO 55 O SI ES UNA INCAPACIDAD) ---
        // Obtenemos los días totales que ya tiene y los que está capturando
        $start = $this->helpers->fechaYmd($data['datepicker_inicial']);
        $end = $this->helpers->fechaYmd($data['datepicker_final']);

        $fechaInicioPeriodo = $this->helpers->getdateActual($empleado->fecha_ingreso);
        $fechaFinalPeriodo = $this->helpers->getdatePosterior($fechaInicioPeriodo);

        $diasPrevios = Incidencia::getIncapacidadesEmpleado($empleado->num_empleado, $fechaInicioPeriodo, $fechaFinalPeriodo);

        $carbonInicio = \Carbon\Carbon::parse($start);
        $carbonFinal = \Carbon\Carbon::parse($end);
        $diasCaptura = $carbonInicio->diffInDays($carbonFinal) + 1;

        $totalDias = $diasPrevios + $diasCaptura;
        $antiguedad = $this->helpers->antiguedad($empleado->fecha_ingreso);

        if (getExcesodeIncapacidad($totalDias, $antiguedad)) {
            $msg = "AVISO DE EXCESO: El empleado excede el límite legal de incapacidades por antigüedad. ";
            $msg .= "Acumulado: {$totalDias} días. (Límite para su antigüedad: " . ($antiguedad < 1 ? '15' : ($antiguedad <= 4 ? '30' : ($antiguedad <= 9 ? '45' : '60'))) . " días). ";
            $msg .= "PROCEDA CON LOS TRÁMITES CORRESPONDIENTES.";

            session()->flash('incapacidad_warning', $msg);
        }

        $incidencia->medico_id = $data['medico_id'];
        $incidencia->fecha_expedida = $this->helpers->fechaYmd($data['datepicker_expedida']);
        $incidencia->diagnostico = $data['diagnostico'];
        $incidencia->num_licencia = $data['num_licencia'];
    }
}