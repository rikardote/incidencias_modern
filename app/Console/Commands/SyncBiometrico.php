<?php

namespace App\Console\Commands;

use App\Models\Checada;
use Illuminate\Console\Command;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class SyncBiometrico extends Command
{
    protected $signature = 'biometrico:sync';
    protected $description = 'Descarga checadas de los dispositivos biométricos';

    public function handle()
    {
        $this->info('Iniciando sincronización biométrica...' . PHP_EOL);

        $dispositivos = \App\Models\Equipo::all();

        if ($dispositivos->isEmpty()) {
            $this->warn('No hay equipos registrados en la base de datos.');
            return;
        }

        foreach ($dispositivos as $dispositivo) {
            $this->info("Conectando a {$dispositivo['location']} ({$dispositivo['ip']})...");
            
            try {
                $zk = new ZKTeco($dispositivo['ip']);
                
                if ($zk->connect()) {
                    $this->info("Conectado. Descargando registros...");
                    $checadas = $zk->getAttendance();
                    
                    if (!empty($checadas)) {
                        $this->procesarChecadas($checadas, $dispositivo['location']);
                    } else {
                        $this->warn("No se encontraron registros.");
                    }
                    
                    $zk->disconnect();
                } else {
                    $this->error("No se pudo establecer conexión.");
                }
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
                Log::error("Error sync biometrico ({$dispositivo['location']}): " . $e->getMessage());
            }
            
            $this->line('');
        }

        $this->info('Sincronización finalizada.');
    }

    private function procesarChecadas($checadas, $location)
    {
        $bar = $this->output->createProgressBar(count($checadas));
        $bar->start();

        $nuevos = 0;
        foreach (array_chunk($checadas, 200) as $chunk) {
            foreach ($chunk as $checada) {
                $fecha = date("Y-m-d H:i:s", strtotime($checada['timestamp']));
                $id = $checada['id'];
                
                // Generar identificador único para evitar duplicados
                $identificador = "{$id}_" . date("YmdHi", strtotime($checada['timestamp'])) . "_" . str_replace(' ', '', $location);

                if (!Checada::where('identificador', $identificador)->exists()) {
                    Checada::create([
                        'num_empleado' => $id,
                        'fecha' => $fecha,
                        'identificador' => $identificador
                    ]);
                    $nuevos++;
                }
                $bar->advance();
            }
        }

        $bar->finish();
        $this->info(PHP_EOL . "Se insertaron {$nuevos} registros nuevos de {$location}.");
    }
}
