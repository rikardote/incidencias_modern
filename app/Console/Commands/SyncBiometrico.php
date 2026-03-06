<?php

namespace App\Console\Commands;

use App\Services\Biometrico\ChecadaService;
use Illuminate\Console\Command;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class SyncBiometrico extends Command
{
    protected $signature = 'biometrico:sync';
    protected $description = 'Descarga checadas de los dispositivos biométricos';

    public function handle(ChecadaService $service)
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
                        $this->info("Se descargaron " . count($checadas) . " registros del equipo. Procesando...");
                        $nuevos = $service->procesarRegistros($checadas, $dispositivo['location']);
                        $this->info("Se insertaron {$nuevos} registros nuevos de {$dispositivo['location']}.");
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
}
