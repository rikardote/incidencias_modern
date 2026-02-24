<?php

namespace App\Services\Incidencias\Rules;

use App\Models\Incidencia;
use App\Models\Employe;

interface IncidenciaRuleInterface
{
    /**
     * Evalúa y aplica la regla de negocio sobre una incidencia.
     * En caso de no superar la validación, lanzará una DomainException.
     * También permite a la regla asignar sus propios campos específicos a la incidencia si requiere.
     *
     * @param Incidencia $incidencia
     * @param Employe $empleado
     * @param array $data
     * @param int $codeReal
     * @return void
     * @throws \DomainException
     */
    public function aplicar(Incidencia $incidencia, Employe $empleado, array $data, int $codeReal);
}
