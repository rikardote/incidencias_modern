<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmployeeImport extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $importing = false;
    public $message = '';
    public $status = ''; // success | error
    public $total = 0;
    public $updated = 0;
    public $skipped = 0;

    public function render()
    {
        return view('livewire.admin.employee-import')
            ->layout('layouts.app');
    }

    public function import()
    {
        if (!auth()->user()->admin()) {
            abort(403);
        }

        $this->validate([
            'csvFile' => 'required|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $this->importing = true;
        $this->message = 'Procesando archivo...';
        
        $path = $this->csvFile->getRealPath();
        $file = fopen($path, 'r');
        
        $this->total = 0;
        $this->updated = 0;
        $this->skipped = 0;

        DB::connection()->unsetEventDispatcher();
        DB::disableQueryLog();
        DB::beginTransaction();

        try {
            $isFirstRow = true;
            while (($line = fgetcsv($file)) !== FALSE) {
                // Saltar línea de encabezados (si existe)
                if ($isFirstRow && str_contains(strtolower($line[0] ?? ''), 'num_empleado')) {
                    $isFirstRow = false;
                    continue;
                }
                $isFirstRow = false;

                // Saltar líneas vacías o con pocos datos
                if (count($line) < 1 || empty(trim($line[0]))) {
                    continue;
                }

                $this->total++;
                
                // Formato esperado (10 columnas): 
                // [0]num_empleado, [1]father_lastname, [2]mother_lastname, [3]name, [4]rfc, [5]curp, [6]fecha_ingreso, [7]condicion, [8]num_seguro, [9]num_plaza
                $numEmpleadoRaw = trim($line[0]);
                
                // Limpiar BOM (Byte Order Mark) si existe
                $bom = pack('H*', 'EFBBBF');
                $numEmpleadoRaw = preg_replace("/^$bom/", '', $numEmpleadoRaw);
                // Limpiar cualquier otro caracter no imprimible al inicio
                $numEmpleadoRaw = preg_replace('/[[:^print:]]/', '', $numEmpleadoRaw);

                $numEmpleado = str_pad($numEmpleadoRaw, 6, '0', STR_PAD_LEFT);
                $fatherL = mb_strtoupper(trim($line[1] ?? ''));
                $motherL = mb_strtoupper(trim($line[2] ?? ''));
                $name = mb_strtoupper(trim($line[3] ?? ''));
                $rfc = mb_strtoupper(trim($line[4] ?? ''));
                $curp = mb_strtoupper(trim($line[5] ?? ''));
                $fechaIngresoRaw = trim($line[6] ?? '');
                $fechaIngreso = $fechaIngresoRaw;

                // Intentar convertir fecha de mm-dd-yy a Y-m-d
                if (!empty($fechaIngresoRaw)) {
                    try {
                        // El formato que reporta el usuario es mm-dd-yy (ej: 01-16-10)
                        $fechaIngreso = Carbon::createFromFormat('m-d-y', $fechaIngresoRaw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Si falla, intentamos otros separadores comunes o lo dejamos como está
                        Log::warning("No se pudo parsear la fecha: $fechaIngresoRaw. Se guardará original.");
                    }
                }

                $condicionStr = mb_strtoupper(trim($line[7] ?? ''));

                // Mapear condición (BASE=1, CONFIANZA=2)
                $condicionId = 2; // Default a Confianza si no se detecta
                if (str_contains($condicionStr, 'BASE')) {
                    $condicionId = 1;
                } elseif (str_contains($condicionStr, 'CONF')) {
                    $condicionId = 2;
                }
                
                // Sanitizar num_seguro y num_plaza para que sean números
                $numSeguro = trim($line[8] ?? '');
                $numSeguro = empty($numSeguro) || !is_numeric($numSeguro) ? 0 : $numSeguro;
                
                $numPlaza = trim($line[9] ?? '');
                $numPlaza = empty($numPlaza) || !is_numeric($numPlaza) ? 0 : $numPlaza;

                $employeeData = [
                    'father_lastname' => $fatherL,
                    'mother_lastname' => $motherL,
                    'name' => $name,
                    'rfc' => $rfc,
                    'curp' => $curp,
                    'fecha_ingreso' => $fechaIngreso,
                    'condicion_id' => $condicionId,
                    'num_seguro' => $numSeguro,
                    'num_plaza' => $numPlaza,
                    'updated_at' => now()
                ];

                // Buscar por número de empleado original limpio
                $exists = DB::table('employees')->where('num_empleado', $numEmpleadoRaw)->exists();

                if ($exists) {
                    DB::table('employees')
                        ->where('num_empleado', $numEmpleadoRaw)
                        ->update($employeeData);
                    $this->updated++;
                } else {
                    // Crear nuevo
                    $employeeData['num_empleado'] = $numEmpleadoRaw;
                    $employeeData['created_at'] = now();
                    $employeeData['active'] = 1;
                    
                    // Campos obligatorios requeridos por el modelo que no tienen default en DB
                    $employeeData['jornada_id'] = 0;
                    $employeeData['estancia'] = 0;
                    $employeeData['lactancia'] = 0;
                    $employeeData['comisionado'] = 0;
                    $employeeData['exento'] = 0;
                    
                    DB::table('employees')->insert($employeeData);
                    $this->updated++;
                }
            }

            DB::commit();
            fclose($file);

            $this->status = 'success';
            $this->message = "Proceso finalizado correctamente.";
            
            $this->dispatch('toast', [
                'icon' => 'success',
                'title' => 'Importación completada',
                'text' => "Se procesaron {$this->total} registros."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file)) fclose($file);
            
            Log::error("Error en importación de empleados: " . $e->getMessage());
            $this->status = 'error';
            $this->message = "Ocurrió un error: " . $e->getMessage();
            
            $this->dispatch('toast', [
                'icon' => 'error',
                'title' => 'Error en la importación',
                'text' => $e->getMessage()
            ]);
        }

        $this->importing = false;
        $this->csvFile = null;
    }
}
