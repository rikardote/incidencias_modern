<?php

namespace App\Livewire\Biometrico;

use App\Models\Checada;
use App\Models\Equipo;
use Livewire\Component;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class Monitor extends Component
{
    public $devices = [];
    public $recentChecadas = [];
    public $isRefreshingStatus = false;

    public function mount()
    {
        $this->loadDevices();
        $this->loadRecentChecadas();
    }

    public function loadDevices()
    {
        $this->devices = Equipo::all()->map(function ($device) {
            return [
                'id' => $device->id,
                'location' => $device->location,
                'ip' => $device->ip,
                'status' => 'checking', // success, error, checking
                'last_sync' => $device->updated_at->diffForHumans(),
            ];
        })->toArray();
    }

    public function loadRecentChecadas()
    {
        $this->recentChecadas = Checada::with('employee')
            ->where('fecha', '<=', now()->addDay())
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($checada) {
                return [
                    'id' => $checada->id,
                    'num_empleado' => $checada->num_empleado,
                    'nombre' => $checada->employee ? $checada->employee->full_name : 'No registrado',
                    'fecha' => $checada->fecha,
                    'hora' => date('H:i:s', strtotime($checada->fecha)),
                    'chip' => $checada->identificador,
                ];
            })->toArray();
    }

    public function refreshStatus()
    {
        $this->isRefreshingStatus = true;
        
        foreach ($this->devices as &$device) {
            try {
                $zk = new ZKTeco($device['ip']);
                // Timeout corto para no bloquear
                if ($zk->connect()) {
                    $device['status'] = 'success';
                    $zk->disconnect();
                } else {
                    $device['status'] = 'error';
                }
            } catch (\Exception $e) {
                $device['status'] = 'error';
            }
        }
        
        $this->isRefreshingStatus = false;
    }

    public function getListeners()
    {
        return [
            "echo:biometrico-monitor,.ChecadaCreated" => 'onChecadaCreated',
        ];
    }

    public function onChecadaCreated($event)
    {
        // El evento viene con 'checada' y 'location'
        $checada = $event['checada'];
        
        // Recargar el empleado para tener el nombre
        $checadaModel = Checada::with('employee')->find($checada['id']);
        
        $newChecada = [
            'id' => $checada['id'],
            'num_empleado' => $checada['num_empleado'],
            'nombre' => $checadaModel && $checadaModel->employee ? $checadaModel->employee->full_name : 'No registrado',
            'fecha' => $checada['fecha'],
            'hora' => date('H:i:s', strtotime($checada['fecha'])),
            'location' => $event['location'] ?? 'Desconocido',
        ];

        array_unshift($this->recentChecadas, $newChecada);
        
        // Mantener solo 20
        if (count($this->recentChecadas) > 20) {
            array_pop($this->recentChecadas);
        }
    }

    public function render()
    {
        return view('livewire.biometrico.monitor')->layout('layouts.app');
    }
}
