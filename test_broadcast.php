<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payload = [
    'employee_name' => 'PRUEBA REAL-TIME',
    'type' => 'TEST',
    'user_name' => 'DEBUGGER',
    'details' => [
        'fecha_inicio' => date('Y-m-d'),
        'fecha_final' => date('Y-m-d'),
        'total_dias' => 777,
        'qnas' => 'Q_DEBUG',
        'periodo' => 'P_DEBUG'
    ],
    'created_at' => now()->toDateTimeString()
];

echo "Broadcasting NewIncidenciaBatchCreated...\n";
broadcast(new \App\Events\NewIncidenciaBatchCreated($payload));
echo "Broadcast sent!\n";