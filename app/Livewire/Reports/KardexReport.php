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

    public function mount($employeeId = null)
    {
        $this->fecha_inicio = date('Y') . '-01-01';
        $this->fecha_final = date('Y-m-d');

        if ($employeeId) {
            $this->cambiarEmpleado($employeeId);
        }
    }

    public function cambiarEmpleado($employeeId)
    {
        $this->results = null;
        $user = auth()->user();
        $query = Employe::with(['department', 'puesto', 'horario', 'jornada']);

        // Seguridad: Solo ver empleados de departamentos permitidos
        if (!$user->admin()) {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $query->whereIn('deparment_id', $departmentIds);
        }

        $this->employee = $query->find($employeeId);

        if ($this->employee) {
            $this->num_empleado = $this->employee->num_empleado;
            // Generar automáticamente al entrar o cambiar
            $this->generate();
        }
        else {
            $this->num_empleado = '';
            if ($employeeId && !$user->admin()) {
                session()->flash('error', 'No tiene permiso para ver este empleado.');
            }
        }
    }

    public function cambiarEmpleadoByNum()
    {
        $num = str_pad($this->num_empleado, 6, '0', STR_PAD_LEFT);
        $emp = Employe::where('num_empleado', $num)->value('id');
        if ($emp) {
            $this->cambiarEmpleado($emp);
        } else {
            $this->addError('num_empleado', 'Empleado no encontrado. Intente con 6 dígitos.');
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

        // Buscar la fecha de la primera incidencia capturada
        $minDate = Incidencia::where('employee_id', $this->employee->id)->min('fecha_inicio');

        $this->fecha_inicio = $minDate ?: date('Y') . '-01-01';
        $this->fecha_final = date('Y-m-d');

        $this->generate();
    }

    public function generate()
    {
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
            $code = $item->codigo->code ?? 'ZZ';
            return $code . $item->fecha_inicio;
        })->values();

        $this->results = $grouped->toArray();
    }

    public function render()
    {
        return view('livewire.reports.kardex-report')->layout('layouts.app');
    }
}