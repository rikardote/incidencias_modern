<?php

namespace App\Livewire\Biometrico;

use App\Models\Checada;
use Livewire\Component;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;
use App\Events\ChecadaCreated;

class Manager extends Component
{
    public $dispositivos = [];
    public $selectedIds = [];
    public $isSyncing = false;
    public $currentDevice = '';
    public $results = [];
    public $progress = 0;

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

        $devicesToSync = \App\Models\Equipo::whereIn('id', $this->selectedIds)->get();
        $total = $devicesToSync->count();
        $count = 0;

        foreach ($devicesToSync as $dispositivo) {
            $this->currentDevice = $dispositivo->location;
            
            try {
                $zk = new ZKTeco($dispositivo->ip);
                
                if ($zk->connect()) {
                    $checadas = $zk->getAttendance();
                    
                    if (!empty($checadas)) {
                        $nuevos = $this->procesarChecadas($checadas, $dispositivo->location);
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

    private function procesarChecadas($checadas, $location)
    {
        $nuevos = 0;
        foreach (array_chunk($checadas, 200) as $chunk) {
            foreach ($chunk as $checada) {
                $timestamp = strtotime($checada['timestamp']);
                $fecha = date("Y-m-d H:i:s", $timestamp);
                $id = $checada['id'];
                
                if ($timestamp > strtotime('+1 day')) {
                    continue;
                }
                
                $identificador = "{$id}_" . date("YmdHi", $timestamp) . "_" . str_replace(' ', '', $location);

                if (!Checada::where('identificador', $identificador)->exists()) {
                    $newChecada = Checada::create([
                        'num_empleado' => $id,
                        'fecha' => $fecha,
                        'identificador' => $identificador
                    ]);
                    
                    event(new ChecadaCreated($newChecada, $location));
                    
                    $nuevos++;
                }
            }
        }
        return $nuevos;
    }

    public function render()
    {
        return view('livewire.biometrico.manager')->layout('layouts.app');
    }
}
