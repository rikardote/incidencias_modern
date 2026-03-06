<?php

namespace App\Livewire\Biometrico;

use App\Models\Checada;
use App\Services\Biometrico\ChecadaService;
use Livewire\Component;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class Manager extends Component
{
    public $dispositivos = [];
    public $selectedIds = [];
    public $isSyncing = false;
    public $currentDevice = '';
    public $results = [];
    public $progress = 0;
    public $deviceTimes = [];

    // Modal para nuevo/editar equipo
    public $isCreateModalOpen = false;
    public $editingEquipoId = null;
    public $newLocation = '';
    public $newIp = '';

    public function mount()
    {
        $this->loadEquipos();
    }

    public function loadEquipos()
    {
        $this->dispositivos = \App\Models\Equipo::all()->toArray();
        $this->selectedIds = array_column($this->dispositivos, 'id');
    }

    public function toggleAll()
    {
        if (count($this->selectedIds) === count($this->dispositivos)) {
            $this->selectedIds = [];
        } else {
            $this->selectedIds = array_column($this->dispositivos, 'id');
        }
    }

    public function openCreateModal()
    {
        $this->reset(['newLocation', 'newIp', 'editingEquipoId']);
        $this->isCreateModalOpen = true;
    }

    public function editEquipo($id)
    {
        $equipo = \App\Models\Equipo::findOrFail($id);
        $this->editingEquipoId = $equipo->id;
        $this->newLocation = $equipo->location;
        $this->newIp = $equipo->ip;
        $this->isCreateModalOpen = true;
    }

    public function saveEquipo()
    {
        $this->validate([
            'newLocation' => 'required|string|max:255',
            'newIp' => 'required|ip'
        ]);

        if ($this->editingEquipoId) {
            $equipo = \App\Models\Equipo::findOrFail($this->editingEquipoId);
            $equipo->update([
                'location' => mb_strtoupper($this->newLocation),
                'ip' => $this->newIp
            ]);
            $msg = 'Equipo actualizado correctamente';
        } else {
            \App\Models\Equipo::create([
                'location' => mb_strtoupper($this->newLocation),
                'ip' => $this->newIp
            ]);
            $msg = 'Equipo agregado correctamente';
        }

        $this->isCreateModalOpen = false;
        $this->loadEquipos();
        
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => $msg
        ]);
    }

    public function deleteEquipo($id)
    {
        $equipo = \App\Models\Equipo::findOrFail($id);
        $equipo->delete();

        $this->loadEquipos();
        
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Equipo eliminado correctamente'
        ]);
    }

    public function sync()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', [
                'icon' => 'warning',
                'title' => 'Seleccione al menos un equipo'
            ]);
            return;
        }

        $this->isSyncing = true;
        $this->results = [];
        $this->progress = 0;

        $service = app(ChecadaService::class);
        $devicesToSync = \App\Models\Equipo::whereIn('id', $this->selectedIds)->get();
        $total = $devicesToSync->count();
        $count = 0;

        foreach ($devicesToSync as $dispositivo) {
            $this->currentDevice = $dispositivo->location;
            
            try {
                $zk = new ZKTeco($dispositivo->ip);
                
                socket_set_option($zk->_zkclient, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 1, 'usec' => 500000]);
                
                if ($zk->connect()) {
                    $checadas = $zk->getAttendance();
                    
                    if (!empty($checadas)) {
                        $nuevos = $service->procesarRegistros($checadas, $dispositivo->location);
                        $this->results[$dispositivo->id] = [
                            'location' => $dispositivo->location,
                            'status' => 'success',
                            'message' => "Se descargaron {$nuevos} registros nuevos."
                        ];
                    } else {
                        $this->results[$dispositivo->id] = [
                            'location' => $dispositivo->location,
                            'status' => 'warning',
                            'message' => "No se encontraron registros."
                        ];
                    }
                    
                    $zk->disconnect();
                } else {
                    $this->results[$dispositivo->id] = [
                        'location' => $dispositivo->location,
                        'status' => 'error',
                        'message' => "No se pudo establecer conexión."
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Error sync biometrico ({$dispositivo->location}): " . $e->getMessage());
                $this->results[$dispositivo->id] = [
                    'location' => $dispositivo->location,
                    'status' => 'error',
                    'message' => "Error: " . $e->getMessage()
                ];
            }
            
            $count++;
            $this->progress = ($count / $total) * 100;
        }

        $this->isSyncing = false;
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Proceso finalizado'
        ]);
        
        $this->dispatch('refreshBiometrico');
    }

    /**
     * Consulta la hora actual de cada equipo biométrico vía UDP.
     * Timeout corto para no bloquear la interfaz.
     */
    public function fetchDeviceTimes()
    {
        $this->deviceTimes = [];

        foreach ($this->dispositivos as $disp) {
            if (!in_array($disp['id'], $this->selectedIds)) {
                continue;
            }

            try {
                $zk = new ZKTeco($disp['ip']);
                
                // Reducir el timeout de 60 segundos (por defecto) a 1.5 segundos
                // para evitar que la interfaz se quede colgada si el dispositivo está offline
                socket_set_option($zk->_zkclient, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 1, 'usec' => 500000]);

                if ($zk->connect()) {
                    $time = $zk->getTime();
                    $formattedTime = $time ? date('d/m/Y H:i:s', strtotime($time)) : null;
                    $this->deviceTimes[$disp['id']] = [
                        'time' => $formattedTime ?: 'Sin respuesta',
                        'status' => $formattedTime ? 'online' : 'error',
                    ];
                    $zk->disconnect();
                } else {
                    $this->deviceTimes[$disp['id']] = [
                        'time' => 'Sin conexión',
                        'status' => 'offline',
                    ];
                }
            } catch (\Exception $e) {
                $this->deviceTimes[$disp['id']] = [
                    'time' => 'Error',
                    'status' => 'offline',
                ];
            }
        }
    }

    /**
     * Sincroniza la hora de un equipo específico con la hora del huso horario del Pacífico (America/Tijuana)
     */
    public function syncDeviceTime($id)
    {
        $equipo = \App\Models\Equipo::findOrFail($id);
        
        try {
            $zk = new ZKTeco($equipo->ip);
            socket_set_option($zk->_zkclient, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 3, 'usec' => 0]);

            $connected = false;
            // Intentar conectar hasta 3 veces (UDP falla a veces)
            for ($i = 0; $i < 3; $i++) {
                if ($zk->connect()) {
                    $connected = true;
                    break;
                }
                usleep(500000); // medio segundo
            }

            if ($connected) {
                $now = now()->timezone('America/Tijuana')->format('Y-m-d H:i:s');
                $success = $zk->setTime($now);
                
                // setTime returns false on error, or potentially an empty string on success (which is falsey)
                if ($success !== false) {
                    $this->dispatch('toast', [
                        'icon' => 'success',
                        'title' => "Hora actualizada en {$equipo->location}"
                    ]);
                    
                    // Actualizar el valor en la UI (obteniendo de nuevo)
                    $time = $zk->getTime();
                    $formattedTime = $time ? date('d/m/Y H:i:s', strtotime($time)) : null;
                    $this->deviceTimes[$id] = [
                        'time' => $formattedTime ?: 'Sin respuesta',
                        'status' => $formattedTime ? 'online' : 'error',
                    ];
                } else {
                    $this->dispatch('toast', [
                        'icon' => 'error',
                        'title' => 'No se pudo actualizar la hora'
                    ]);
                }
                $zk->disconnect();
            } else {
                $this->dispatch('toast', [
                    'icon' => 'error',
                    'title' => 'No se pudo conectar al equipo'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'icon' => 'error',
                'title' => 'Error al conectar'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.biometrico.manager')->layout('layouts.app');
    }
}
