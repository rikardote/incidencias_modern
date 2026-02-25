<?php

namespace App\Livewire\System;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class MaintenanceToggle extends Component
{
    public $isMaintenanceMode = false;

    public function mount()
    {
        if (!auth()->user()->admin()) {
            abort(403);
        }
        $this->isMaintenanceMode = Cache::get('capture_maintenance', false);
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

        $this->dispatch('notify', [
            'message' => $this->isMaintenanceMode ? 'Modo de mantenimiento activado (Capturas bloqueadas)' : 'Modo de mantenimiento desactivado (Capturas liberadas)',
            'type' => $this->isMaintenanceMode ? 'error' : 'success'
        ]);
        
        $this->dispatch('maintenance-updated', mode: $this->isMaintenanceMode);
    }

    public function render()
    {
        return view('livewire.system.maintenance-toggle');
    }
}
