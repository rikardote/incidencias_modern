<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Incidencia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$employeeWithInc = Incidencia::select('employee_id')->groupBy('employee_id')->orderByRaw('count(*) desc')->first();
$employeeId = $employeeWithInc->employee_id;

$startDate = Carbon::now()->subMonths(11)->startOfMonth();

$monthlyBreakdown = Incidencia::select(
    DB::raw('YEAR(fecha_inicio) as year'),
    DB::raw('MONTH(fecha_inicio) as month'),
    'codigodeincidencia_id',
    DB::raw('sum(total_dias) as days')
)
    ->with('codigo')
    ->where('employee_id', $employeeId)
    ->where('fecha_inicio', '>=', $startDate)
    ->groupBy('year', 'month', 'codigodeincidencia_id')
    ->get();

$monthlyTrend = Incidencia::select(
    DB::raw('YEAR(fecha_inicio) as year'),
    DB::raw('MONTH(fecha_inicio) as month'),
    DB::raw('count(*) as total'),
    DB::raw('sum(total_dias) as days')
)
    ->where('employee_id', $employeeId)
    ->where('fecha_inicio', '>=', $startDate)
    ->groupBy('year', 'month')
    ->get();

$y = $monthlyTrend->first()->year ?? null;
$m = $monthlyTrend->first()->month ?? null;

echo "Y: $y, M: $m\n";

$breakdown = $monthlyBreakdown->filter(function ($item) use ($y, $m) {
    return $item->year == $y && $item->month == $m;
})->map(function ($item) {
    return (object)[
    'description' => $item->codigo ? $item->codigo->description : 'Desconocido',
    'days' => $item->days
    ];
})->values();

print_r($breakdown->toArray());