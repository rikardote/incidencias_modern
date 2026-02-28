<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate LIVEWIRE method
app()->call(function () {
    $c = new \App\Livewire\Reports\EmployeeStatistics();

    // find an employee with incidencias
    $employeeId = \App\Models\Incidencia::select('employee_id')->groupBy('employee_id')->orderByRaw('count(*) desc')->first()->employee_id;
    $c->employeeId = $employeeId;
    $c->year = date('Y');

    $stats = $c->stats();
    print_r($stats['trendData']);
});