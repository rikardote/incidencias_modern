<?php

namespace App\Services\Employees;

use App\Models\Employe;
use App\Models\Puesto;
use App\Models\Horario;
use App\Models\Jornada;
use App\Models\Condicion;
use App\Models\Incidencia;
use App\Services\Incidencias\IncidenciaHelpersService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\User;

class EmployeesService
{
    protected $helpers;

    public function __construct(IncidenciaHelpersService $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * Obtiene los datos necesarios para llenar los formularios de crear/editar
     */
    public function getFormData(User $user)
    {
        $deparments = $user->centros->pluck('deparment', 'id')->toArray();
        $puestos = Puesto::all()->pluck('puesto', 'id')->toArray();
        $jornadas = Jornada::all()->pluck('jornada', 'id')->toArray();
        $horarios = Horario::all()->pluck('horario', 'id')->toArray();
        $condiciones = Condicion::all()->pluck('condicion', 'id')->toArray();

        asort($deparments);
        asort($puestos);
        asort($horarios);
        asort($jornadas);

        return compact('deparments', 'puestos', 'jornadas', 'horarios', 'condiciones');
    }

    /**
     * Guarda o actualiza un empleado
     */
    public function saveEmployee(array $data, $num_empleado = null)
    {
        if ($num_empleado) {
            $employee = Employe::where('num_empleado', $num_empleado)->firstOrFail();
            $employee->fill($data);
        } else {
            $employee = new Employe($data);
        }

        // Formateo de fechas usando el service de helpers
        $employee->fecha_ingreso = $this->helpers->fechaYmd($data['fecha_ingreso'] ?? null);

        // Normalización de checkboxes
        $employee->estancia = !empty($data['estancia']) ? 1 : 0;
        $employee->lactancia = !empty($data['lactancia']) ? 1 : 0;
        $employee->comisionado = !empty($data['comisionado']) ? 1 : 0;

        if ($employee->lactancia) {
            $employee->lactancia_inicio = $this->helpers->fechaYmd($data['lactancia_inicio'] ?? null);
            $employee->lactancia_fin = $this->helpers->fechaYmd($data['lactancia_fin'] ?? null);
        } else {
            $employee->lactancia_inicio = null;
            $employee->lactancia_fin = null;
        }

        $employee->save();

        return $employee;
    }

    /**
     * Obtiene el estado de licencias médicas (exceso) por antigüedad
     */
    public function getExcesoLicenciasMedicas($num_empleado)
    {
        $empleado = Employe::where('num_empleado', $num_empleado)->firstOrFail();

        // Calcular períodos usando helpers
        $fechaInicio = $this->helpers->getdateActual($empleado->fecha_ingreso);
        $fechaFinal  = $this->helpers->getdatePosterior($fechaInicio);

        // Calcular antigüedad
        $fechaIngreso = new Carbon($empleado->fecha_ingreso);
        $hoy = Carbon::now();
        $antiguedad = $fechaIngreso->diffInYears($hoy);

        $dias_lic = Incidencia::getIncapacidadesEmpleado($empleado->num_empleado, $fechaInicio, $fechaFinal);

        // Lógica de límites según Ley o Reglamento
        $limites = [
            ['min' => 0,  'max' => 0,  'limite' => 15], // Menos de 1 año
            ['min' => 1,  'max' => 4,  'limite' => 30],
            ['min' => 5,  'max' => 9,  'limite' => 45],
            ['min' => 10, 'max' => 99, 'limite' => 60],
        ];

        foreach ($limites as $rango) {
            if ($antiguedad >= $rango['min'] && $antiguedad <= $rango['max']) {
                if ($dias_lic > $rango['limite']) {
                    return $dias_lic;
                }
                break;
            }
        }

        return 0;
    }

    /**
     * Lógica de búsqueda para autocompletado
     */
    public function search(string $term, User $user, $onlyDoctors = false)
    {
        $term = Str::upper($term);
        $dptos = $user->centros->pluck('id')->toArray();

        $query = Employe::query();

        if ($onlyDoctors) {
            $doctorPuestos = ['24','25','28','30','56','57','58','59','60','61','62','63','64','65','66','67','68','87','88','101','95','96','97','98'];
            $query->whereIn('puesto_id', $doctorPuestos);
        } else {
            $query->whereIn('deparment_id', $dptos);
        }

        $results = $query->where('father_lastname', 'LIKE', $term.'%')->get();

        return $results->map(function($v) use ($onlyDoctors) {
            return [
                'value' => $onlyDoctors ? $v->id : $v->num_empleado,
                'label' => "{$v->father_lastname} {$v->mother_lastname} {$v->name}"
            ];
        });
    }

    /**
     * Actualización rápida de campos específicos (jornada, horario, etc)
     */
    public function quickUpdate($num_empleado, array $fields)
    {
        $employee = Employe::where('num_empleado', $num_empleado)->firstOrFail();
        $employee->fill($fields);
        $employee->save();
        return $employee;
    }
}
