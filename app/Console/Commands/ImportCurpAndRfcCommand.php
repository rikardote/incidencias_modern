<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCurpAndRfcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:curp-rfc {file : Ruta al archivo CSV (num_empleado,curp,rfc)}';

    protected $description = 'Importa masivamente CURP y RFC desde un archivo CSV';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("El archivo no existe: $file");
            return 1;
        }

        $handle = fopen($file, "r");
        $header = fgetcsv($handle, 1000, ","); // Leer cabecera

        $count = 0;
        $errors = 0;

        $this->info("Iniciando importación...");

        \Illuminate\Support\Facades\DB::connection()->unsetEventDispatcher();
        \Illuminate\Support\Facades\DB::disableQueryLog();

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Asegurarse de tener al menos las 3 columnas
            if (count($data) < 3) continue;

            $num_empleado = trim($data[0]);
            $curp = trim($data[1]);
            $rfc = trim($data[2]);

            if (empty($num_empleado)) continue;

            $employee = \App\Models\Employe::where('num_empleado', $num_empleado)->first();

            if ($employee) {
                $employee->update([
                    'curp' => $curp,
                    'rfc' => $rfc
                ]);
                $count++;
            } else {
                $this->warn("Empleado no encontrado: $num_empleado");
                $errors++;
            }
        }

        fclose($handle);

        $this->info("Importación terminada.");
        $this->info("Actualizados: $count");
        if ($errors > 0) {
            $this->error("No encontrados: $errors");
        }

        return 0;
    }
}
