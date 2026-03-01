<?php

namespace App\Console\Commands;

use App\Models\Checada;
use Illuminate\Console\Command;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class SyncBiometrico extends Command
{
    protected $signature = 'biometrico:sync';
    protected $description = 'Descarga checadas de los dispositivos biométricos';

    public function handle()
    {
        $this->info('Iniciando sincronización biométrica...' . PHP_EOL);

        $dispositivos = [
            ['ip' => '192.160.141.37', 'location' => 'Delegación Principal'],
            ['ip' => '192.160.169.230', 'location' => 'Almacén'],
            ['ip' => '192.165.240.253', 'location' => 'San Felipe'],
            ['ip' => '192.165.232.253', 'location' => 'Los Algodones'],
            ['ip' => '192.165.171.253', 'location' => 'Tecate'],
            ['ip' => '192.161.192.253', 'location' => 'EBDI 60']
        ];

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
