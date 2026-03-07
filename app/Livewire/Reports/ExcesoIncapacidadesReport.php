<?php

namespace App\Livewire\Reports;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\Department;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExcesoIncapacidadesReport extends Component
{
    public $data = [];
    public $loading = true;
    public $search = '';
    public $selectedDepartment = null;
    public $departments = [];

    public function mount()
    {
        $user = auth()->user();
        if ($user->admin()) {
            $this->departments = Department::orderBy('code')->get();
        }
        else {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $this->departments = Department::whereIn('id', $departmentIds)->orderBy('code')->get();
        }
    }

    public function setDepartment($deptId)
    {
        $this->selectedDepartment = $deptId;
    }

    public function loadData()
    {
        $user = auth()->user();

        $query = Employe::query()->where('active', '1');

        if ($this->selectedDepartment) {
            $query->where('deparment_id', $this->selectedDepartment);
        }
        elseif (!$user->admin()) {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $query->whereIn('deparment_id', $departmentIds);
        }

        $empleados = $query->get();
        $this->data = [];

        if ($empleados->isEmpty()) {
            $this->loading = false;
            return;
        }

        $empleadoIds = $empleados->pluck('id')->toArray();

        // Código 55 - Incapacidad. Obtenemos el ID del código.
        $codigoIncapacidad = DB::table('codigos_de_incidencias')->where('code', '55')->first();
        if (!$codigoIncapacidad) {
            $this->loading = false;
            return;
        }

        // Traemos TODAS las incapacidades de estos empleados en un rango amplio que cubra 
        // cualquier "año laboral" actual (13 meses atrás hasta hoy para estar seguros).
        $todasIncapacidades = Incidencia::with(['codigo'])
            ->whereIn('employee_id', $empleadoIds)
            ->where('codigodeincidencia_id', $codigoIncapacidad->id)
            ->where('fecha_inicio', '>=', Carbon::now()->subMonths(13)->format('Y-m-d'))
            ->whereNull('deleted_at')
            ->orderBy('fecha_inicio')
            ->get()
            ->groupBy('employee_id');

        foreach ($empleados as $empleado) {
            // El periodo de incapacidades se calcula según su fecha de ingreso (aniversario)
            $fechaInicio = getdateActual($empleado->fecha_ingreso);
            $fechaFinal = getdatePosterior($fechaInicio);

            // Filtramos las incapacidades que ya tenemos en memoria
            $incidenciasEmpleado = $todasIncapacidades->get($empleado->id, collect());
            
            $incapacidadesMes = $incidenciasEmpleado->filter(function($inc) use ($fechaInicio, $fechaFinal) {
                return $inc->fecha_inicio >= $fechaInicio && $inc->fecha_inicio <= $fechaFinal;
            });

            if ($incapacidadesMes->isEmpty()) {
                continue;
            }

            $totalDias = $incapacidadesMes->sum('total_dias');
            $antiguedad = getAntiguedad($empleado->fecha_ingreso);

            $incapacidadReciente = $incapacidadesMes->contains(function ($inc) {
                return Carbon::parse($inc->fecha_inicio)->addDays(30)->isAfter(Carbon::now());
            });

            if (getExcesodeIncapacidad($totalDias, $antiguedad)) {
                $this->data[$empleado->num_empleado] = [
                    'empleado' => $empleado,
                    'incapacidades' => $incapacidadesMes,
                    'total_dias' => $totalDias,
                    'antiguedad' => $antiguedad,
                    'periodo_inicio' => $fechaInicio,
                    'periodo_final' => $fechaFinal,
                    'incapacidad_reciente' => $incapacidadReciente,
                ];
            }
        }

        $this->loading = false;
        $this->dispatch('toast', icon: 'success', title: 'Reporte de Incapacidades Listo');
    }


    public function render()
    {
        return view('livewire.reports.exceso-incapacidades-report');
    }
}