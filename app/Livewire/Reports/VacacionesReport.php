<?php

namespace App\Livewire\Reports;

use App\Models\Employe;
use App\Models\Periodo;
use App\Models\Incidencia;
use App\Constants\Incidencias as Inc;
use Livewire\Component;

class VacacionesReport extends Component
{
    public $employeeId;
    public $employee;
    
    // Modal state
    public $showDetailsModal = false;
    public $selectedPeriod = null;
    public $selectedPeriodIncidencias = [];

    public function mount($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->employee = Employe::with(['department', 'puesto', 'jornada'])->findOrFail($employeeId);
    }

    public function showDetails($periodId, $periodNombre)
    {
        $this->selectedPeriod = $periodNombre;
        $this->selectedPeriodIncidencias = Incidencia::with(['codigo', 'qna'])
            ->where('employee_id', $this->employeeId)
            ->where('periodo_id', $periodId)
            ->whereIn('codigodeincidencia_id', [16, 25, 42])
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        
        $this->showDetailsModal = true;
    }

    public function closeDetails()
    {
        $this->showDetailsModal = false;
        $this->selectedPeriod = null;
        $this->selectedPeriodIncidencias = [];
    }

    public function render()
    {
        // Obtener todos los periodos activos o recientes
        $periodos = Periodo::orderBy('year', 'desc')
            ->orderBy('periodo', 'desc')
            ->get();

        $resumen = $periodos->map(function ($p) {
            // Días usados en este periodo (Códigos 60, 62, 63)
            $used = Incidencia::where('employee_id', $this->employeeId)
                ->where('periodo_id', $p->id)
                ->whereIn('codigodeincidencia_id', [16, 25, 42]) // IDs de 60, 62, 63
                ->sum('total_dias');

            return [
                'p_id' => $p->id,
                'nombre' => $p->periodo . '-' . $p->year,
                'usados' => $used,
            ];
        })->filter(function($item) {
            return $item['usados'] > 0;
        });

        return view('livewire.reports.vacaciones-report', [
            'resumen' => $resumen
        ]);
    }
}
