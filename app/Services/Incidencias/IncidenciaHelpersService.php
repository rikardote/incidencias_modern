<?php

namespace App\Services\Incidencias;

class IncidenciaHelpersService
{
    public function sistemaEnMantenimiento()
    {
        return check_manto();
    }

    public function fechaYmd($fecha)
    {
        return fecha_ymd($fecha);
    }

    public function qnaDesdeFecha($fecha)
    {
        return qna_year($fecha);
    }

    public function inicioQna($qnaId)
    {
        return getFechaInicioPorQna($qnaId);
    }

    public function finQnaDesdeInicio($fechaInicio)
    {
        return getFechaFinalPorQna($fechaInicio);
    }

    public function generarToken()
    {
        return genToken();
    }

    public function capturadoPor($userId)
    {
        return capturado_por($userId);
    }

    public function txtUsadoPorMes($numEmpleado, $fecha)
    {
        return getTxtPorMes($numEmpleado, $fecha);
    }

    public function antiguedad($fechaIngreso)
    {
        return getAntiguedad($fechaIngreso);
    }

    public function excesoLicenciaConGoce($start, $antiguedad, $numEmpleado, $dias)
    {
        return getExcesodeLicenciasConGoce(
            $start,
            $antiguedad,
            $numEmpleado,
            $dias
        );
    }

    public function getdateActual($fechaIngreso)
    {
        return getdateActual($fechaIngreso);
    }

    public function getdatePosterior($fecha)
    {
        return getdatePosterior($fecha);
    }
}
