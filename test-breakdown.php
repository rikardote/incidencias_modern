<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Incidencia;
use Carbon\Carbon;

$employeeWithInc = Incidencia::select('employee_id')->groupBy('employee_id')->havingRaw('count(*) > 0')->first();
if (!$employeeWithInc) {
    echo "No incidencias.\n";
    exit;
}
$employeeId = $employeeWithInc->employee_id;
echo "Employee ID: " . $employeeId . "\n";

$startDate = Carbon::now()->subMonths(11)->startOfMonth();

$monthlyBreakdown = Incidencia::select(
    \DB::raw('YEAR(fecha_inicio) as year'),
    \DB::raw('MONTH(fecha_inicio) as month'),
    'codigodeincidencia_id',
    \DB::raw('sum(total_dias) as days')
)
    ->with('codigo')
    ->where('employee_id', $employeeId)
    ->where('fecha_inicio', '>=', $startDate)
    ->groupBy('year', 'month', 'codigodeincidencia_id')
    ->get();

print_r($monthlyBreakdown->toArray());