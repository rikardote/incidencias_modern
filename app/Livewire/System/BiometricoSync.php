<?php

namespace App\Livewire\System;

use App\Models\Checada;
use Livewire\Component;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class BiometricoSync extends Component
{
    public $isSyncing = false;
    public $message = '';
    public $progress = 0;
    public $results = [];

    public function sync()
    {
        $this->isSyncing = true;
        $this->message = 'Iniciando conexión con equipos...';
        $this->results = [];
        $this->progress = 0;

        $dispositivos = \App\Models\Equipo::all();

        $total = $dispositivos->count();

        if ($total === 0) {
            $this->isSyncing = false;
            $this->message = 'No hay equipos registrados.';
            return;
        }
        
        foreach ($dispositivos as $index => $dispositivo) {
            $this->message = "Sincronizando {$dispositivo['location']}...";
            
            try {
                $zk = new ZKTeco($dispositivo['ip']);
                
                if ($zk->connect()) {
                    $checadas = $zk->getAttendance();
                    
                    if (!empty($checadas)) {
                        $nuevos = $this->procesarChecadas($checadas, $dispositivo['location']);
                        $this->results[] = [
                            'location' => $dispositivo['location'],
                            'status' => 'success',
                            'message' => "Se descargaron {$nuevos} registros nuevos."
                        ];
                    } else {
                        $this->results[] = [
                            'location' => $dispositivo['location'],
                            'status' => 'warning',
                            'message' => "No se encontraron registros."
                        ];
                    }
                    
                    $zk->disconnect();
                } else {
                    $this->results[] = [
                        'location' => $dispositivo['location'],
                        'status' => 'error',
                        'message' => "No se pudo establecer conexión."
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Error sync biometrico ({$dispositivo['location']}): " . $e->getMessage());
                $this->results[] = [
                    'location' => $dispositivo['location'],
                    'status' => 'error',
                    'message' => "Error: " . $e->getMessage()
                ];
            }
            
            $this->progress = (($index + 1) / $total) * 100;
        }

        $this->isSyncing = false;
        $this->message = 'Sincronización finalizada.';
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Sincronización biométrica completada'
        ]);
        
        // Disparar evento para que otros componentes (como el reporte biométrico) se actualicen
        $this->dispatch('refreshBiometrico');
    }

    private function procesarChecadas($checadas, $location)
    {
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
            }
        }
        return $nuevos;
    }

    public function render()
    {
        return view('livewire.system.biometrico-sync');
    }
}
