<?php

namespace App\Livewire\System;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class MaintenanceToggle extends Component
{
    public $isMaintenanceMode = false;
    public $islandStyle = 'classic';

    public function mount(\App\Services\System\IslandWidgetService $service)
    {
        if (!auth()->user()->admin()) {
            abort(403);
        }
        $this->isMaintenanceMode = Cache::get('capture_maintenance', false);
        $this->islandStyle = $service->getCurrentStyle();
    }

    public function updatedIslandStyle($value, \App\Services\System\IslandWidgetService $service)
    {
        $this->islandStyle = $service->setStyle($value);
        
        // Sincronizar con la navegación enviando el estilo
        $this->dispatch('island-style-updated', style: $this->islandStyle);
        
        // Disparar una notificación de prueba usando el bridge de toast que ya funciona
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Cambiado a ' . ucfirst($this->islandStyle)
        ]);
    }

    public function toggle()
    {
        if (!auth()->user()->admin()) return;

        $this->isMaintenanceMode = !$this->isMaintenanceMode;
        
        if ($this->isMaintenanceMode) {
            Cache::forever('capture_maintenance', true);
        } else {
            Cache::forget('capture_maintenance');
        }

        $this->dispatch('toast', [
            'icon' => $this->isMaintenanceMode ? 'error' : 'success',
            'title' => $this->isMaintenanceMode ? 'Mantenimiento Activado' : 'Mantenimiento Desactivado'
        ]);
        
        $this->dispatch('maintenance-updated', mode: $this->isMaintenanceMode);
    }

    public function render()
    {
        return view('livewire.system.maintenance-toggle');
    }
}
