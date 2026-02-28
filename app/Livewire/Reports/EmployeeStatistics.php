<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\CodigoDeIncidencia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\Computed;

class EmployeeStatistics extends Component
{
    public $employeeId;
    public $year;

    public function mount($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->year = date('Y');
    }

    #[Computed]
    public function employee()
    {
        return Employe::with(['department', 'puesto', 'horario', 'jornada'])
            ->findOrFail($this->employeeId);
    }

    #[Computed]
    public function stats()
    {
        $employee = $this->employee();
        
        // Total history
        $totalIncidencias = Incidencia::where('employee_id', $this->employeeId)->count();
        $totalDays = Incidencia::where('employee_id', $this->employeeId)->sum('total_dias');
        
        // Distribution by Code (Top 10)
        $byCode = Incidencia::select('codigodeincidencia_id', DB::raw('count(*) as total'), DB::raw('sum(total_dias) as days'))
            ->where('employee_id', $this->employeeId)
            ->with('codigo')
            ->groupBy('codigodeincidencia_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Monthly Trend (Current Year)
        $monthlyTrend = Incidencia::select(
            DB::raw('MONTH(fecha_inicio) as month'),
            DB::raw('count(*) as total'),
            DB::raw('sum(total_dias) as days')
        )
        ->where('employee_id', $this->employeeId)
        ->whereYear('fecha_inicio', $this->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

        $trendData = [];
        for ($i = 1; $i <= 12; $i++) {
            $trendData[$i] = $monthlyTrend->get($i, (object)['total' => 0, 'days' => 0]);
        }

        // Recent Incidencias
        $recent = Incidencia::where('employee_id', $this->employeeId)
            ->with('codigo')
            ->orderByDesc('fecha_inicio')
            ->limit(5)
            ->get();

        return [
            'totalIncidencias' => $totalIncidencias,
            'totalDays' => $totalDays,
            'byCode' => $byCode,
            'trendData' => $trendData,
            'recent' => $recent
        ];
    }

    public function render()
    {
        return view('livewire.reports.employee-statistics')
            ->layout('layouts.app');
    }
}