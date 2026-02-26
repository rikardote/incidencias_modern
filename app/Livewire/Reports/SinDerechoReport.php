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
        usleep(800000); // Artificial delay to show the spinner

        $dt = Carbon::create($this->year, $this->month, 1, 12, 0, 0);
        $fecha_inicio = $dt->copy()->startOfMonth()->format('Y-m-d');
        $fecha_final = $dt->copy()->endOfMonth()->format('Y-m-d');

        // Note: Code is string or numeric, handled by the relationship. 
        // We need to fetch the IDs from the database based on codes.

        $lic = ['40', '41', '46', '47', '53', '54', '55'];
        $inc = ['01', '02', '03', '04', '08', '09', '10', '18', '19', '25', '30', '31', '78', '86', '100'];

        // Get the actual IDs for these codes
        $licIds = DB::table('codigos_de_incidencias')->whereIn('code', $lic)->pluck('id')->toArray();
        $incIds = DB::table('codigos_de_incidencias')->whereIn('code', $inc)->pluck('id')->toArray();

        // 1. Incidencias without right
        // Legacy: where employees.condicion_id = 1 (meaning "Base")
        // We will query to Group by Employee to get their data

        $incidencias = [];

        if (!empty($incIds)) {
            $queryInc = DB::table('incidencias')
                ->select('employees.id', 'employees.num_empleado')
                ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
                ->whereNull('incidencias.deleted_at')
                ->where('employees.deparment_id', $this->departmentId)
                ->where('employees.condicion_id', 1)
                ->whereIn('incidencias.codigodeincidencia_id', $incIds)
                ->whereBetween('incidencias.fecha_inicio', [$fecha_inicio, $fecha_final])
                ->groupBy('employees.id', 'employees.num_empleado')
                ->get();

            foreach ($queryInc as $row) {
                $incidencias[$row->num_empleado] = $row->id;
            }
        }

        if (!empty($licIds)) {
            $queryLic = DB::table('incidencias')
                ->select('employees.id', 'employees.num_empleado', DB::raw('SUM(incidencias.total_dias) as count'))
                ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
                ->whereNull('incidencias.deleted_at')
                ->where('employees.deparment_id', $this->departmentId)
                ->where('employees.condicion_id', 1)
                ->whereIn('incidencias.codigodeincidencia_id', $licIds)
                ->whereBetween('incidencias.fecha_inicio', [$fecha_inicio, $fecha_final])
                ->groupBy('employees.id', 'employees.num_empleado')
                ->havingRaw('SUM(incidencias.total_dias) > 3')
                ->get();

            foreach ($queryLic as $row) {
                $incidencias[$row->num_empleado] = $row->id;
            }
        }

        // Now we get full employee models sorted by num_empleado
        $employeeIds = array_values($incidencias);

        $this->results = Employe::with(['puesto', 'horario', 'jornada'])
            ->whereIn('id', $employeeIds)
            ->orderBy('num_empleado')
            ->get();
    }

    public function showDetails($employeeId)
    {
        $dt = Carbon::create($this->year, $this->month, 1, 12, 0, 0);
        $fecha_inicio = $dt->copy()->startOfMonth()->format('Y-m-d');
        $fecha_final = $dt->copy()->endOfMonth()->format('Y-m-d');

        $lic = ['40', '41', '46', '47', '53', '54', '55'];
        $inc = ['01', '02', '03', '04', '08', '09', '10', '18', '19', '25', '30', '31', '78', '86', '100'];

        $employee = Employe::findOrFail($employeeId);
        $this->selectedEmployeeName = $employee->num_empleado . ' - ' . $employee->name . ' ' . $employee->father_lastname . ' ' . $employee->mother_lastname;

        // Obtain incidences causing the loss of rights
        // 1. the INC ones
        $incidenciasInc = Incidencia::with(['codigo', 'periodo'])
            ->where('employee_id', $employeeId)
            ->whereIn('codigodeincidencia_id', function ($q) use ($inc) {
            $q->select('id')->from('codigos_de_incidencias')->whereIn('code', $inc);
        })
            ->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_final])
            ->get();

        // 2. the LIC ones
        $incidenciasLic = Incidencia::with(['codigo', 'periodo'])
            ->where('employee_id', $employeeId)
            ->whereIn('codigodeincidencia_id', function ($q) use ($lic) {
            $q->select('id')->from('codigos_de_incidencias')->whereIn('code', $lic);
        })
            ->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_final])
            ->get();

        $totalLicDias = $incidenciasLic->sum('total_dias');

        $collected = collect();
        foreach ($incidenciasInc as $i) {
            $collected->push($i);
        }
        // Only show licencias if the sum > 3
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