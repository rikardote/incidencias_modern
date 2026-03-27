<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Employees\EmployeeApiService;

$service = app(EmployeeApiService::class);
$name = 'NOE NETZAHUALCOYOTL';
$data = $service->searchEmployees($name);

echo "Search results for {$name}:\n";
print_r($data);
