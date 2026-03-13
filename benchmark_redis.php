3<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;

function benchmark($storeName, $iterations = 500) {
    echo "Benchmarking store: [$storeName]... ";
    $start = microtime(true);
    
    $store = Cache::store($storeName);
    
    for ($i = 0; $i < $iterations; $i++) {
        $key = "bench_{$i}";
        $store->put($key, "value_{$i}", 60);
        $value = $store->get($key);
    }
    
    // Cleanup
    for ($i = 0; $i < $iterations; $i++) {
        $store->forget("bench_{$i}");
    }

    $end = microtime(true);
    $total = ($end - $start) * 1000; // ms
    echo sprintf("\n  Total time for %d ops: %.2f ms", $iterations * 2, $total);
    echo sprintf("\n  Avg latency per op: %.4f ms\n", $total / ($iterations * 2));
    return $total;
}

echo "--- COMPARATIVA DE RENDIMIENTO (CACHE) ---\n";
echo "Simulando 1000 operaciones (500 escrituras + 500 lecturas)\n\n";

try {
    $dbTime = benchmark('database');
    echo "\n";
    $redisTime = benchmark('redis');

    echo "\n--- RESULTADO FINAL ---\n";
    if ($redisTime < $dbTime) {
        $gain = (($dbTime - $redisTime) / $dbTime) * 100;
        echo sprintf("Redis es un %.1f%% más rápido que la Base de Datos.\n", $gain);
        echo sprintf("Ahorro de tiempo: %.2f ms por cada 1000 operaciones.\n", $dbTime - $redisTime);
    } else {
        echo "Los resultados son muy cercanos o la red local influye en la prueba.\n";
    }
} catch (\Exception $e) {
    echo "Error durante la prueba: " . $e->getMessage() . "\n";
}
