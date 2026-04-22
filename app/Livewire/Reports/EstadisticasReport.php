<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Incidencia;
use App\Models\Department;
use App\Models\CodigoDeIncidencia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EstadisticasReport extends Component
{
    public $loading = true;
    public $selectedDepartment = null;
    public $selectedCode = null;
    public $selectedGender = null;
    public $fechaInicio;
    public $fechaFinal;

    public $departments = [];
    public $codigos = [];

    // Resultados
    public $statsByJornada = [];
    public $totalDays = 0;
    public $detailedData = [];

    public function mount()
    {
        // Por defecto: desde 1 de enero hasta hoy
        $this->fechaInicio = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->fechaFinal = Carbon::now()->format('Y-m-d');

        $user = auth()->user();
        if ($user->admin()) {
            $this->departments = Department::orderBy('code')->get();
        }
        else {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $this->departments = Department::whereIn('id', $departmentIds)->orderBy('code')->get();
        }

        $this->codigos = CodigoDeIncidencia::orderBy('code')->get();

        // Lo dejamos vacío y pedimos que le den al botón Generar indicando loading = true (esperando)
        $this->loading = true;
        $this->detailedData = null; // null means not generated yet
    }

    public function setDepartment($deptId)
    {
        $this->selectedDepartment = $deptId;
        $this->loading = true;
    }

    public function setCode($codeId)
    {
        $this->selectedCode = $codeId;
        $this->loading = true;
    }

    public function updated($propertyName)
    {
        $this->loading = true;
    }

    public function loadData()
    {
        $this->validate([
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date|after_or_equal:fechaInicio',
            'selectedCode' => 'required'
        ], [
            'selectedCode.required' => 'Por favor, selecciona un código de incidencia.',
            'fechaFinal.after_or_equal' => 'La fecha final no puede ser menor a la inicial.'
        ]);

        $this->loading = false;
    }

    #[\Livewire\Attributes\Computed]
    public function reportData()
    {
        if (!$this->selectedCode || !$this->fechaInicio || !$this->fechaFinal || $this->loading === true) {
            return null;
        }

        $user = auth()->user();

        $query = Incidencia::with(['employee.department', 'employee.puesto', 'employee.jornada', 'codigo', 'periodo'])
            ->where('codigodeincidencia_id', $this->selectedCode)
            ->whereBetween('fecha_inicio', [$this->fechaInicio, $this->fechaFinal]);

        if ($this->selectedDepartment) {
            $deptOption = $this->selectedDepartment;
            $query->whereHas('employee', function ($q) use ($deptOption) {
                $q->where('deparment_id', $deptOption);
            });
        } elseif (!$user->admin()) {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $query->whereHas('employee', function ($q) use ($departmentIds) {
                $q->whereIn('deparment_id', $departmentIds);
            });
        }

        if ($user->admin() && $this->selectedGender) {
            $genderChar = $this->selectedGender;
            $query->whereHas('employee', function ($q) use ($genderChar) {
                // En MySQL el índice es 1-based, así que 11 es el carácter correcto
                $q->whereRaw("SUBSTRING(curp, 11, 1) = ?", [$genderChar]);
            });
        }

        $incidencias = $query->orderBy('fecha_inicio', 'desc')->get();

        if ($incidencias->isNotEmpty()) {
            $numeros = $incidencias->pluck('employee.num_empleado')->unique()->filter()->toArray();
        }

        $statsByJornada = $incidencias->groupBy(function ($inc) {
            return $inc->employee->jornada->jornada ?? 'SIN JORNADA ASIGNADA';
        })->map(function ($group) {
            return $group->sum('total_dias');
        })->toArray();

        // Agrupamos por empleado para visualizar a los de mayor reincidencia primero
        $groupedByEmployee = $incidencias->groupBy('employee_id')->map(function ($employeeIncidencias) {
            $first = $employeeIncidencias->first();
            return [
                'employee' => $first->employee,
                'total_dias' => $employeeIncidencias->sum('total_dias'),
                'details' => $employeeIncidencias
            ];
        })->sortByDesc('total_dias');

        ksort($statsByJornada);

        return [
            'groupedByEmployee' => $groupedByEmployee,
            'totalDays' => $incidencias->sum('total_dias'),
            'statsByJornada' => $statsByJornada
        ];
    }

    public function render()
    {
        return view('livewire.reports.estadisticas-report');
    }
}