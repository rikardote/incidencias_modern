<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Incidencia;
use App\Models\Department;
use App\Models\CodigoDeIncidencia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class AusentismoReport extends Component
{
    public $loading = true;
    public $selectedDepartment = null;
    public $fechaInicio;
    public $fechaFinal;
    public $chartType = 'bar';

    public $departments = [];
    
    public $ausentismoCodes = [
        '01', '02', '03', '10', '14', '17', '40', '41', '42', '46', '47', '48', '49', '51', '53', '54', '55', '60', '62', '63', '94', '100', '907'
    ];

    public function mount()
    {
        $this->fechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->fechaFinal = Carbon::now()->format('Y-m-d');

        try {
            $user = auth()->user();
            if ($user->admin()) {
                $this->departments = Department::orderBy('code')->get();
            } else {
                $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
                $this->departments = Department::whereIn('id', $departmentIds)->orderBy('code')->get();
            }
        } catch (\Exception $e) {
            $this->departments = [];
        }

        $this->loading = false;
    }

    public function setDepartment($deptId)
    {
        $this->selectedDepartment = $deptId;
    }

    public function loadData()
    {
        $this->validate([
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date|after_or_equal:fechaInicio',
        ]);
        $this->loading = false;
    }

    #[Computed]
    public function selectedDeptLabel()
    {
        if (empty($this->departments)) return 'Error cargando departamentos';
        $dept = collect($this->departments)->firstWhere('id', $this->selectedDepartment);
        return $dept ? '[' . $dept->code . '] ' . $dept->description : 'Todos los departamentos';
    }

    #[Computed]
    public function reportData()
    {
        if (!$this->fechaInicio || !$this->fechaFinal || $this->loading) {
            return null;
        }

        try {
            $user = auth()->user();
            
            // Map codes to IDs for faster SQL query
            $codigoMapping = CodigoDeIncidencia::whereIn('code', $this->ausentismoCodes)
                ->pluck('id', 'code')
                ->toArray();
            
            $codigosList = CodigoDeIncidencia::whereIn('code', $this->ausentismoCodes)
                ->get()
                ->keyBy('id');

            // Build base query
            $query = Incidencia::select('codigodeincidencia_id', DB::raw('SUM(total_dias) as total_sum'))
                ->whereIn('codigodeincidencia_id', array_values($codigoMapping))
                ->whereBetween('fecha_inicio', [$this->fechaInicio, $this->fechaFinal])
                ->groupBy('codigodeincidencia_id');

            // Apply filters (department, user scope)
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

            // Results from DB already aggregated
            $results = $query->get();

            $classicStats = [];
            $rawChartData = [];
            $totalDays = 0;
            
            // Map results back to the requested codes for the grid
            foreach ($this->ausentismoCodes as $code) {
                $id = $codigoMapping[$code] ?? -1;
                $found = $results->firstWhere('codigodeincidencia_id', $id);
                $sum = $found ? (float)$found->total_sum : 0;
                
                $classicStats[$code] = $sum;
                $totalDays += $sum;
                
                $codeObj = $codigosList[$id] ?? null;
                $rawChartData[] = [
                    'code' => $code,
                    'description' => $codeObj ? $codeObj->description : 'Desconocido',
                    'value' => $sum
                ];
            }

            // Max value for bar scaling
            $maxVal = collect($rawChartData)->max('value') ?: 1;
            $chartData = array_map(function($item) use ($maxVal) {
                $item['percentage'] = $maxVal > 0 ? ($item['value'] / $maxVal) * 100 : 0;
                return $item;
            }, $rawChartData);

            return [
                'chartData' => $chartData,
                'classicStats' => $classicStats,
                'totalClassicStats' => $totalDays,
                'totalDays' => (int)$totalDays
            ];
            
        } catch (\Exception $e) {
            return [
                'chartData' => [],
                'classicStats' => array_fill_keys($this->ausentismoCodes, 0),
                'totalClassicStats' => 0,
                'totalDays' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function render()
    {
        return view('livewire.reports.ausentismo-report');
    }
}