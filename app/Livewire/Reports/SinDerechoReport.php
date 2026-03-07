<?php

namespace App\Livewire\Reports;

use App\Models\Department;
use App\Models\Incidencia;
use App\Models\Employe;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SinDerechoReport extends Component
{
    public $year;
    public $month;
    public $departmentId;
    public $results = null;

    public $isModalOpen = false;
    public $selectedEmployeeName = '';
    public $selectedEmployeeIncidencias = [];

    protected $rules = [
        'year' => 'required|numeric|min:2010|max:2100',
        'month' => 'required|numeric|min:1|max:12',
        'departmentId' => 'required|exists:deparments,id',
    ];

    public function mount()
    {
        $this->year = date('Y');
        // By default, previous month
        $this->month = Carbon::now()->subMonth()->month;
    }

    public function updatedYear()
    {
        $this->results = null;
    }

    public function updatedMonth()
    {
        $this->results = null;
    }

    public function updatedDepartmentId()
    {
        $this->results = null;
    }

    public function generate()
    {
        $this->validate();

        $dt = Carbon::create($this->year, $this->month, 1, 12, 0, 0);
        $fecha_inicio = $dt->copy()->startOfMonth()->format('Y-m-d');
        $fecha_final = $dt->copy()->endOfMonth()->format('Y-m-d');

        // Note: Code is string or numeric, handled by the relationship. 
        $lic = ['40', '41', '46', '47', '53', '54', '55'];
        $inc = ['01', '02', '03', '04', '08', '09', '10', '18', '19', '25', '30', '31', '78', '86', '100'];

        $licIds = DB::table('codigos_de_incidencias')->whereIn('code', $lic)->pluck('id')->toArray();
        $incIds = DB::table('codigos_de_incidencias')->whereIn('code', $inc)->pluck('id')->toArray();

        // OPTIMIZACIÓN CRÍTICA: Primero obtenemos los IDs de los empleados del departamento.
        // Esto reduce el universo de búsqueda para la tabla 'incidencias' de miles de registros a solo unos pocos.
        $targetEmployeeIds = DB::table('employees')
            ->where('deparment_id', $this->departmentId)
            ->where('condicion_id', 1)
            ->pluck('id')
            ->toArray();

        if (empty($targetEmployeeIds)) {
            $this->results = collect();
            return;
        }

        // OBTENCIÓN DE DATOS EN UNA SOLA PASADA
        // Traemos todas las incidencias de interés para esos empleados en ese mes.
        // Esto evita escanear la tabla 'incidencias' varias veces.
        $allIncidencias = DB::table('incidencias')
            ->select('employee_id', 'codigodeincidencia_id', 'total_dias')
            ->whereNull('deleted_at')
            ->whereIn('employee_id', $targetEmployeeIds)
            ->whereIn('codigodeincidencia_id', array_merge($incIds, $licIds))
            ->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_final])
            ->get();

        // Procesamos la lógica en memoria (PHP es mucho más rápido para esto que una BD sin índices)
        $incidenciasPorEmpleado = $allIncidencias->groupBy('employee_id');
        $employeeIds = [];

        foreach ($incidenciasPorEmpleado as $empId => $incidencias) {
            $hasCriticalInc = false;
            $sumLicMedicas = 0;

            foreach ($incidencias as $inc) {
                // Si tiene alguna falta (INC), pierde el derecho automáticamente
                if (in_array($inc->codigodeincidencia_id, $incIds)) {
                    $hasCriticalInc = true;
                    break; 
                }
                // Si es licencia médica, las acumulamos
                if (in_array($inc->codigodeincidencia_id, $licIds)) {
                    $sumLicMedicas += $inc->total_dias;
                }
            }

            if ($hasCriticalInc || $sumLicMedicas > 3) {
                $employeeIds[] = $empId;
            }
        }

        $this->results = Employe::with(['puesto', 'horario', 'jornada'])
            ->whereIn('id', $employeeIds)
            ->orderBy('num_empleado')
            ->get();

        $this->dispatch('toast', icon: 'success', title: 'Reporte Generado');
    }

    public function showDetails($employeeId)
    {
        $employee = collect($this->results)->firstWhere('id', $employeeId);
        if (!$employee) {
            return;
        }

        $this->selectedEmployeeName = $employee->num_empleado . ' - ' . $employee->name . ' ' . $employee->father_lastname . ' ' . $employee->mother_lastname;

        // Necesitamos acotar estrictamente las fechas al mes/año que el usuario seleccionó en la interfaz
        $dt = Carbon::create($this->year, $this->month, 1, 12, 0, 0);
        $fecha_inicio = $dt->copy()->startOfMonth()->format('Y-m-d');
        $fecha_final = $dt->copy()->endOfMonth()->format('Y-m-d');

        $lic = ['40', '41', '46', '47', '53', '54', '55'];
        $inc = ['01', '02', '03', '04', '08', '09', '10', '18', '19', '25', '30', '31', '78', '86', '100'];

        // Hacemos una única consulta pequeña que tarda <1ms en ejecutarse para traer solo ese mes
        $incidencias = Incidencia::with(['codigo'])
            ->where('employee_id', $employeeId)
            ->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_final])
            ->whereHas('codigo', function ($q) use ($inc, $lic) {
                $q->whereIn('code', array_merge($inc, $lic));
            })
            ->get();

        $incidenciasInc = collect();
        $incidenciasLic = collect();

        foreach ($incidencias as $incidencia) {
            $codeStr = (string) $incidencia->codigo->code;
            if (in_array($codeStr, $inc)) {
                $incidenciasInc->push($incidencia);
            } elseif (in_array($codeStr, $lic)) {
                $incidenciasLic->push($incidencia);
            }
        }

        $totalLicDias = $incidenciasLic->sum('total_dias');

        $collected = collect();
        foreach ($incidenciasInc as $i) {
            $collected->push($i);
        }
        
        // Solo agregamos las licencias a la vista detalle si suman más de 3 días en total
        if ($totalLicDias > 3) {
            foreach ($incidenciasLic as $i) {
                $collected->push($i);
            }
        }

        $this->selectedEmployeeIncidencias = $collected->sortBy('fecha_inicio')->values()->all();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedEmployeeIncidencias = [];
    }


    public function render()
    {
        $user = auth()->user();
        if ($user->admin()) {
            $departments = Department::orderBy('code')->get();
        }
        else {
            $departments = $user->departments()->orderBy('code')->get();
        }

        $years = [];
        for ($i = date('Y'); $i >= 2017; $i--) {
            $years[$i] = $i;
        }

        $months = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];

        return view('livewire.reports.sin-derecho-report', [
            'years' => $years,
            'months' => $months,
            'departments' => $departments,
        ])->layout('layouts.app');
    }
}