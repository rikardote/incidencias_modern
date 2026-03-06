<?php

namespace App\Console\Commands;

use App\Models\Equipo;
use App\Services\Biometrico\ChecadaService;
use Illuminate\Console\Command;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class BiometricoMonitorDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'biometrico:monitor {device_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitorea en tiempo real los eventos de los dispositivos biométricos mediante sondeo rápido';

    private $pids = [];
    private ChecadaService $checadaService;

    /**
     * Execute the console command.
     */
    public function handle(ChecadaService $checadaService)
    {
        $this->checadaService = $checadaService;

        $deviceId = $this->argument('device_id');

        if ($deviceId) {
            $device = Equipo::find($deviceId);
            if (!$device) {
                $this->error("Equipo ID $deviceId no encontrado.");
                return;
            }
            $this->startMonitoring($device);
        } else {
            $this->info("Iniciando Monitor Maestro de Biométricos (Modo Híbrido)...");
            $this->runMaster();
        }
    }

    private function runMaster()
    {
        // Solo monitorear Delegación Principal por ahora
        $devices = Equipo::where('location', 'Delegación Principal')->get();

        if ($devices->isEmpty()) {
            $this->warn("No hay equipos registrados para monitorear.");
            return;
        }

        foreach ($devices as $device) {
            $this->forkDeviceMonitor($device);
        }

        // Proceso Maestro: vigila a los hijos
        while (true) {
            $status = 0;
            $pid = pcntl_wait($status);

            if ($pid > 0) {
                if (isset($this->pids[$pid])) {
                    $device = $this->pids[$pid];
                    $this->error("Monitor para {$device->location} (PID $pid) se detuvo. Reiniciando...");
                    unset($this->pids[$pid]);
                    
                    sleep(5);
                    $this->forkDeviceMonitor($device);
                }
            }
            
            usleep(500000);
        }
    }

    private function forkDeviceMonitor($device)
    {
        $pid = pcntl_fork();

        if ($pid == -1) {
            $this->error("No se pudo crear el proceso para {$device->location}");
        } elseif ($pid) {
            // Padre
            $this->pids[$pid] = $device;
        } else {
            // Hijo
            $this->startMonitoring($device);
            exit(0);
        }
    }

    private function startMonitoring($device)
    {
        $this->info("[{$device->location}] Iniciando monitor híbrido...");

        while (true) {
            try {
                $zk = new ZKTeco($device->ip);

                if ($zk->connect()) {
                    $this->info("[{$device->location}] ✅ CONECTADO. Monitoreando...");
                    
                    // Bucle de sondeo rápido
                    while (true) {
                        $this->info("[{$device->location}] Realizando sondeo...");
                        $this->pollAttendance($zk, $device);
                        sleep(10); // Revisar cada 10 segundos
                    }

                    $zk->disconnect();
                } else {
                    $this->error("[{$device->location}] ❌ Error de conexión.");
                }
            } catch (\Exception $e) {
                $this->error("[{$device->location}] ⚠️ Excepción: " . $e->getMessage());
            }

            $this->info("[{$device->location}] Reintentando en 30 segundos...");
            sleep(30);
        }
    }

    private function pollAttendance($zk, $device)
    {
        try {
            // Descargamos los registros
            $attendance = $zk->getAttendance();
            if (!is_array($attendance)) {
                $this->error("[{$device->location}] Error: getAttendance no devolvió un array.");
                return;
            }

            $count = count($attendance);
            $this->info("[{$device->location}] Se obtuvieron $count registros en total.");

            // Solo procesamos los últimos 50 para rapidez
            $recent = array_slice($attendance, -50);
            
            if ($count > 0) {
                $last = end($recent);
                $this->info("[{$device->location}] Último registro en el equipo: Empleado {$last['id']} a las {$last['timestamp']}");
            }

            $nuevos = 0;

            // Para el daemon, procesamos uno por uno para tener eventos en tiempo real
            foreach ($recent as $record) {
                $checada = $this->checadaService->procesarRegistroIndividual($record, $device->location);
                if ($checada) {
                    $this->info("[{$device->location}] 🕒 NUEVO REGISTRO EN VIVO: Empleado {$checada->num_empleado} a las {$checada->fecha}");
                    $nuevos++;
                }
            }

            if ($nuevos > 0) {
                $this->info("[{$device->location}] 🕒 Se detectaron $nuevos registros nuevos.");
            }

        } catch (\Exception $e) {
            $this->error("[{$device->location}] Error en sondeo: " . $e->getMessage());
            throw $e;
        }
    }
}
