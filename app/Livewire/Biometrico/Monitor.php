<?php

namespace App\Livewire\Biometrico;

use App\Models\Checada;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Monitor extends Component
{
    public $recentChecadas = [];
    public $equipoNames = [];

    public function mount()
    {
        $this->loadEquipoNames();
        $this->loadRecentChecadas();
    }

    public function loadEquipoNames()
    {
        $this->equipoNames = \App\Models\Equipo::pluck('location', 'serial_number')->toArray();
    }



    public function loadRecentChecadas()
    {
        $user = auth()->user();
        $query = Checada::with('employee')
            ->where('fecha', '<=', now()->addDay())
            ->orderBy('fecha', 'desc')
            ->limit(50);

        if (!$user->admin()) {
            $departmentIds = $user->departments->pluck('id')->toArray();
            $query->whereHas('employee', function ($q) use ($departmentIds) {
                $q->whereIn('deparment_id', $departmentIds);
            });
        }

        $this->recentChecadas = $query->get()
            ->map(function ($checada) {
                // El identificador tiene formato: PIN_YYYYMMDDHHII_LOCATION
                $parts = explode('_', $checada->identificador);
                $location = count($parts) >= 3 ? implode('_', array_slice($parts, 2)) : 'Desconocido';

                // Si la ubicación parece un Serial Number (empieza con CL... o similar) 
                // o si tenemos un mapeo para este valor, lo usamos
                // Quitamos el prefijo ADMS_ si viene en el identificador para buscarlo en la tabla
                $sn = str_replace('ADMS_', '', $location);
                $refinedLocation = $this->equipoNames[$sn] ?? $location;

                return [
                    'id' => $checada->id,
                    'num_empleado' => $checada->num_empleado,
                    'nombre' => $checada->employee ? $checada->employee->full_name : 'No registrado',
                    'fecha' => $checada->fecha,
                    'hora' => date('H:i:s', strtotime($checada->fecha)),
                    'chip' => $checada->identificador,
                    'location' => str_starts_with($refinedLocation, 'ADMS_') ? $refinedLocation : 'ADMS_' . $refinedLocation,
                ];
            })->toArray();
    }



    public function getListeners()
    {
        return [
            "echo:biometrico-monitor,.ChecadaCreated" => 'onChecadaCreated',
        ];
    }

    public function onChecadaCreated($event)
    {
        $user = auth()->user();
        // El evento viene con 'checada' y 'location'
        $checada = $event['checada'];
        
        // Recargar el empleado para tener el nombre y la validación de departamento
        $checadaModel = Checada::with('employee')->find($checada['id']);
        
        // Si el usuario no es admin, validar que tenga acceso al departamento del empleado
        if (!$user->admin()) {
            $departmentIds = $user->departments->pluck('id')->toArray();
            $employeeDepartmentId = $checadaModel->employee->deparment_id ?? null;
            
            if (!$employeeDepartmentId || !in_array($employeeDepartmentId, $departmentIds)) {
                return; // No tiene acceso, ignorar evento
            }
        }

        $rawLocation = $event['location'] ?? 'Desconocido';
        $sn = str_replace('ADMS_', '', $rawLocation);
        $refinedLocation = $this->equipoNames[$sn] ?? $rawLocation;

        $newChecada = [
            'id' => $checada['id'],
            'num_empleado' => $checada['num_empleado'],
            'nombre' => $checadaModel && $checadaModel->employee ? $checadaModel->employee->full_name : 'No registrado',
            'fecha' => $checada['fecha'],
            'hora' => date('H:i:s', strtotime($checada['fecha'])),
            'location' => str_starts_with($refinedLocation, 'ADMS_') 
                ? $refinedLocation 
                : 'ADMS_' . $refinedLocation,
            'chip' => $checadaModel ? $checadaModel->identificador : 'Desconocido',
        ];

        array_unshift($this->recentChecadas, $newChecada);
        
        // Mantener solo 50
        if (count($this->recentChecadas) > 50) {
            array_pop($this->recentChecadas);
        }
    }

    public function render()
    {
        return view('livewire.biometrico.monitor')->layout('layouts.app');
    }
}
