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

        foreach ($empleados as $empleado) {
            $fechaInicio = getdateActual($empleado->fecha_ingreso);
            $fechaFinal = getdatePosterior($fechaInicio);

            // Código 55 es incapacidad (según legacy logic)
            $incapacidades = Incidencia::with(['codigo'])
                ->where('employee_id', $empleado->id)
                ->whereHas('codigo', function ($q) {
                $q->where('code', '55');
            })
                ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFinal])
                ->whereNull('deleted_at')
                ->orderBy('fecha_inicio')
                ->get();

            if ($incapacidades->isEmpty()) {
                continue;
            }

            $totalDias = $incapacidades->sum('total_dias');
            $antiguedad = getAntiguedad($empleado->fecha_ingreso);

            $incapacidadReciente = $incapacidades->contains(function ($inc) {
                return Carbon::parse($inc->fecha_inicio)->addDays(30)->isAfter(Carbon::now());
            });

            if (getExcesodeIncapacidad($totalDias, $antiguedad)) {
                $this->data[$empleado->num_empleado] = [
                    'empleado' => $empleado,
                    'incapacidades' => $incapacidades,
                    'total_dias' => $totalDias,
                    'antiguedad' => $antiguedad,
                    'periodo_inicio' => $fechaInicio,
                    'periodo_final' => $fechaFinal,
                    'incapacidad_reciente' => $incapacidadReciente,
                ];
            }
        }

        $this->loading = false;

        $this->dispatch('island-notif', [
            'message' => 'Reporte de Incapacidades Listo',
            'type' => 'success'
        ]);
    }


    public function render()
    {
        return view('livewire.reports.exceso-incapacidades-report');
    }
}