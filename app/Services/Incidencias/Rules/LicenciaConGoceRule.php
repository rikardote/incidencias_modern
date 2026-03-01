<?php

namespace App\Services\Incidencias\Rules;

use DomainException;
use DB;
use App\Models\Incidencia;
use App\Models\Employe;
use App\Constants\Incidencias as Inc;

class LicenciaConGoceRule implements IncidenciaRuleInterface
{
    protected $helpers;

    public function __construct(\App\Services\Incidencias\IncidenciaHelpersService $helpers = null)
    {
        $this->helpers = $helpers ?: app(\App\Services\Incidencias\IncidenciaHelpersService::class);
    }

    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal)
    {
        if (!Inc::esLicencia($codeReal)) {
            return;
        }
        // 1. EL SALTO: Si en el controlador/service ya validamos que saltar_validacion_lic es "1"
        // Debemos acceder como array: $data['...']
        if (isset($data['saltar_validacion_lic']) && $data['saltar_validacion_lic'] == "1") {
            return; // Ignora el resto de la regla y deja pasar el guardado
        }

        if ($codeReal == 41) {
            // Solo personal de BASE (condicion_id = 1) puede tomar licencias con goce
            if ((int) $empleado->condicion_id !== 1) {
                throw new \DomainException(
                    'Las licencias con goce de sueldo (código 41) solo aplican para personal de BASE.'
                );
            }

            $antiguedad = $this->helpers->antiguedad($empleado->fecha_ingreso);

            $a = $this->helpers->excesoLicenciaConGoce(
                $incidencia->fecha_inicio,
                $antiguedad,
                $empleado->num_empleado,
                $incidencia->total_dias
            );

            if ($a != 0) {
                throw new \DomainException("Trabajador ya tomó $a días económicos");
            }
        }
    }
}
