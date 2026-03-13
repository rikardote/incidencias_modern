<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$csvFile = 'QNA04.csv';

if (!file_exists($csvFile)) {
    echo "❌ Error: No se encontró el archivo $csvFile en el directorio raíz.\n";
    exit(1);
}

echo "📂 Iniciando importación de CURP/RFC desde $csvFile...\n";

$file = fopen($csvFile, 'r');
$total = 0;
$updated = 0;
$skipped = 0;

DB::beginTransaction();

try {
    while (($line = fgetcsv($file)) !== FALSE) {
        $total++;
        
        // Formato esperado: num_empleado, curp, rfc
        if (count($line) < 3) {
            $skipped++;
            continue;
        }

        $numEmpleado = str_pad(trim($line[0]), 6, '0', STR_PAD_LEFT);
        $curp = trim($line[1]);
        $rfc = trim($line[2]);

        $affected = DB::table('employees')
            ->where('num_empleado', $numEmpleado)
            ->update([
                'curp' => $curp,
                'rfc' => $rfc,
                'updated_at' => now()
            ]);

        if ($affected) {
            $updated++;
        } else {
            // Quizás no existe el empleado en la DB
            $skipped++;
        }

        if ($total % 500 == 0) {
            echo "  Procesando... $total registros...\n";
        }
    }

    DB::commit();
    fclose($file);

    echo "\n✨ Importación Finalizada:\n";
    echo "   - Total procesados: $total\n";
    echo "   - Actualizados con éxito: $updated\n";
    echo "   - Omitidos (no encontrados o sin cambios): $skipped\n";

} catch (\Exception $e) {
    DB::rollBack();
    fclose($file);
    echo "❌ Error durante la importación: " . $e->getMessage() . "\n";
    exit(1);
}
