<?php

namespace App\Livewire\Reports;

use App\Models\Employe;
use App\Models\Incidencia;
use Carbon\Carbon;
use Livewire\Component;

class KardexReport extends Component
{
    public $num_empleado = '';
    public $fecha_inicio = '';
    public $fecha_final = '';

    public $employee = null;
    public $results = null;

    public function mount()
    {
        $this->fecha_inicio = date('Y') . '-01-01';
        $this->fecha_final = date('Y-m-d');
    }

    public function cambiarEmpleado($employeeId)
    {
        $this->results = null;
        $this->employee = Employe::with(['department', 'puesto', 'horario', 'jornada'])->find($employeeId);
        if ($this->employee) {
            $this->num_empleado = $this->employee->num_empleado;
        // No generamos nada automáticamente, dejamos que el usuario elija
        }
        else {
            $this->num_empleado = '';
        }
    }

    public function updatedFechaInicio()
    {
        $this->results = null;
    }

    public function updatedFechaFinal()
    {
        $this->results = null;
    }

    public function generateAll()
    {
        if (!$this->employee) {
            $this->addError('num_empleado', 'Empleado no encontrado. Verifique el número de empleado.');
            return;
        }

        $this->fecha_inicio = '1970-01-01';
        $this->fecha_final = date('Y-12-31', strtotime('+100 years'));

        $this->generate();
    }

    public function generate()
    {
        usleep(800000); // Artificial delay to show the spinner
        if (!$this->employee) {
            $this->addError('num_empleado', 'Empleado no encontrado. Verifique el número de empleado.');
            return;
        }

        $incidenciasDB = Incidencia::with(['codigo', 'periodo'])
            ->where('employee_id', $this->employee->id)
            ->whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_final])
            ->get();

        $grouped = $incidenciasDB->groupBy(function ($inc) {
            return empty($inc->token) ? 'id_' . $inc->id : $inc->token;
        })->map(function ($group) {
            $first = $group->first();
            return (object)[
            'codigo' => $first->codigo,
            'fecha_inicio' => $group->min('fecha_inicio'),
            'fecha_final' => $group->max('fecha_final'),
            'total_dias' => $group->sum('total_dias'),
            'periodo' => $first->periodo,
            'otorgado' => $first->otorgado,
            'horas_otorgadas' => $first->horas_otorgadas,
            'diagnostico' => $first->diagnostico,
            'num_licencia' => $first->num_licencia,
            'cobertura_txt' => $first->cobertura_txt,
            ];
        })->sortBy(function ($item) {
            return $item->codigo->code . $item->fecha_inicio;
        })->values();

        $this->results = collect($grouped);
    }

    public function render()
    {
        return view('livewire.reports.kardex-report')->layout('layouts.app');
    }
}