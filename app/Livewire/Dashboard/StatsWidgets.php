<?php

namespace App\Livewire\Dashboard;

use App\Models\Employe;
use App\Models\Incidencia;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatsWidgets extends Component
{
    public $activeEmployeesCount = 0;
    public $todayIncidenciasCount = 0;
    public $systemStatus = [];

    public function mount()
    {
        $this->refreshStats();
    }

    public function refreshStats()
    {
        // 1. Empleados Activos
        $query = Employe::active();
        if (auth()->check() && !auth()->user()->admin()) {
            $query->whereIn('deparment_id', auth()->user()->departments->pluck('id'));
        }
        $this->activeEmployeesCount = $query->count();

        // 2. Incidencias del día
        $incidenciaQuery = Incidencia::whereDate('created_at', today());
        if (auth()->check() && !auth()->user()->admin()) {
            $deptIds = auth()->user()->departments->pluck('id');
            $incidenciaQuery->whereHas('employee', function($q) use ($deptIds) {
                $q->whereIn('deparment_id', $deptIds);
            });
        }
        $this->todayIncidenciasCount = $incidenciaQuery->count();

        // 3. Estatus Técnico
        $this->systemStatus = $this->checkTechnicalStatus();
    }

    private function checkTechnicalStatus()
    {
        $status = [
            'db_main' => false,
            'db_biometrico' => false,
            'reverb' => false,
            'api_empleados' => false,
            'maintenance' => Cache::get('capture_maintenance', false),
        ];

        // DB Main
        try {
            DB::connection()->getPdo();
            $status['db_main'] = true;
        } catch (\Exception $e) {}

        // DB Biometrico
        try {
            DB::connection(app()->environment('testing') ? config('database.default') : 'biometrico')->getPdo();
            $status['db_biometrico'] = true;
        } catch (\Exception $e) {}

        // Employees API Status
        try {
            $apiUrl = config('services.employees.api_url');
            if ($apiUrl) {
                // Hacemos una petición rápida al root de la API
                $response = \Illuminate\Support\Facades\Http::timeout(1)->get($apiUrl);
                $status['api_empleados'] = $response->successful() || $response->status() === 404; // 404 significa que el servidor respondió (está vivo)
            }
        } catch (\Exception $e) {}

        // Reverb Status
        $reverbHost = config('reverb.servers.reverb.hostname') ?? 'localhost';
        $reverbPort = config('reverb.servers.reverb.port', 8080);
        
        $connection = @fsockopen($reverbHost, $reverbPort, $errno, $errstr, 1);
        if (is_resource($connection)) {
            $status['reverb'] = true;
            fclose($connection);
        }

        return $status;
    }

    public function render()
    {
        return view('livewire.dashboard.stats-widgets');
    }
}
