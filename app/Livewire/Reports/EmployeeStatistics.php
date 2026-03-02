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
        
        $baseQuery = Incidencia::where('employee_id', $this->employeeId)
            ->whereHas('codigo', function ($q) {
                $q->where('code', '!=', '77');
            });
            
        // Total history
        $totalIncidencias = (clone $baseQuery)->count();
        $totalDays = (clone $baseQuery)->sum('total_dias');
        
        // Distribution by Code (Top 10)
        $byCode = (clone $baseQuery)->select('codigodeincidencia_id', DB::raw('count(*) as total'), DB::raw('sum(total_dias) as days'))
            ->with('codigo')
            ->groupBy('codigodeincidencia_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Monthly Trend (Current vs Previous Year)
        $currentYear = intval($this->year);
        $previousYear = $currentYear - 1;
        $startDate = Carbon::create($previousYear, 1, 1)->startOfDay();
        
        $monthlyTrend = (clone $baseQuery)->select(
            DB::raw('YEAR(fecha_inicio) as year'),
            DB::raw('MONTH(fecha_inicio) as month'),
            DB::raw('count(*) as total'),
            DB::raw('sum(total_dias) as days')
        )
        ->where('fecha_inicio', '>=', $startDate)
        ->groupBy('year', 'month')
        ->get();

        $monthlyBreakdown = (clone $baseQuery)->select(
            DB::raw('YEAR(fecha_inicio) as year'),
            DB::raw('MONTH(fecha_inicio) as month'),
            'codigodeincidencia_id',
            DB::raw('sum(total_dias) as days')
        )
        ->with('codigo')
        ->where('fecha_inicio', '>=', $startDate)
        ->groupBy('year', 'month', 'codigodeincidencia_id')
        ->get();

        $trendData = [];
        $monthNames = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        
        for ($m = 1; $m <= 12; $m++) {
            
            // Previous year data
            $statPrev = $monthlyTrend->first(fn($item) => $item->year == $previousYear && $item->month == $m);
            $breakdownPrev = $monthlyBreakdown->filter(fn($item) => $item->year == $previousYear && $item->month == $m)
                ->map(fn($item) => (object) [
                    'description' => $item->codigo ? $item->codigo->description : 'Desconocido',
                    'days' => $item->days
                ])->toArray();

            // Current year data
            $statCurr = $monthlyTrend->first(fn($item) => $item->year == $currentYear && $item->month == $m);
            $breakdownCurr = $monthlyBreakdown->filter(fn($item) => $item->year == $currentYear && $item->month == $m)
                ->map(fn($item) => (object) [
                    'description' => $item->codigo ? $item->codigo->description : 'Desconocido',
                    'days' => $item->days
                ])->toArray();

            $trendData[] = (object)[
                'monthName' => $monthNames[$m - 1],
                'previous' => (object)[
                    'year' => $previousYear,
                    'total' => $statPrev ? $statPrev->total : 0,
                    'days' => $statPrev ? $statPrev->days : 0,
                    'breakdown' => $breakdownPrev
                ],
                'current' => (object)[
                    'year' => $currentYear,
                    'total' => $statCurr ? $statCurr->total : 0,
                    'days' => $statCurr ? $statCurr->days : 0,
                    'breakdown' => $breakdownCurr
                ]
            ];
        }

        // Recent Incidencias
        $recent = (clone $baseQuery)
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