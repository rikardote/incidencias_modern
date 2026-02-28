<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$employeeId = 2; // Let's try 2
$startDate = \Carbon\Carbon::now()->subMonths(240)->startOfMonth();

use App\Models\Incidencia;
use Illuminate\Support\Facades\DB;

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
print_r($monthlyBreakdown->toArray());
