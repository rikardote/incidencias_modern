<?php

namespace App\Livewire\Biometrico;

use App\Models\Employe;
use App\Models\Checada;
use Livewire\Component;
use Carbon\Carbon;

class EmployeeAttendance extends Component
{
    public $employee;
    public $quincena;
    public $quincenaFin;
    public $año;
    public $checadas = [];
    public $isPortal = false;

    public function mount($numEmpleado, $isPortal = false)
    {
        $this->isPortal = $isPortal;
        
        // Priorizar guard 'web' (Administradores/Jefes de Depto)
        $user = auth()->guard('web')->user();
        $guard = 'web';

        if (!$user && auth()->guard('employee')->check()) {
            $user = auth()->guard('employee')->user();
            $guard = 'employee';
        }

        if (!$user) {
            abort(403, 'No ha iniciado sesión.');
        }

        $query = Employe::with(['department', 'puesto', 'horario']);

        if ($guard === 'employee') {
            // Un empleado logueado solo puede verse a sí mismo
            if ($user->num_empleado != $numEmpleado) {
                abort(403, 'No tiene permiso para ver esta información.');
            }
            $this->employee = $user;
        } else {
            // Lógica para usuarios administrativos (guard 'web')
            if (!$user->admin()) {
                $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
                $query->whereIn('deparment_id', $departmentIds);
            }
            $this->employee = $query->where('num_empleado', $numEmpleado)->firstOrFail();
        }
        
        $this->año = (int)date('Y');

        // Calcular quincena actual
        $day = (int)date('d');
        $month = (int)date('n');
        $this->quincena = ($day <= 15) ? ($month * 2 - 1) : ($month * 2);
        $this->quincenaFin = $this->quincena;

        $this->loadChecadas();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['quincena', 'quincenaFin', 'año'])) {
            $this->loadChecadas();
        }
    }

    public function loadChecadas()
    {
        if ($this->isPortal) {
            $fin = now()->format('Y-m-d');
            $inicio = now()->subMonth()->format('Y-m-d');
            $checadaModel = new Checada();
            
            $registros = $checadaModel->obtenerRegistrosPorEmpleado($this->employee->id, $inicio, $fin);
            
            $this->checadas = $registros->sortByDesc('fecha')->values();
            return;
        }

        $qStart = (int)min($this->quincena, $this->quincenaFin);
        $qEnd = (int)max($this->quincena, $this->quincenaFin);

        $mesStart = ceil($qStart / 2);
        $es_primera_start = ($qStart % 2) != 0;

        $inicio = $es_primera_start
            ? "{$this->año}-" . str_pad($mesStart, 2, '0', STR_PAD_LEFT) . "-01"
            : "{$this->año}-" . str_pad($mesStart, 2, '0', STR_PAD_LEFT) . "-16";

        $mesEnd = ceil($qEnd / 2);
        $es_primera_end = ($qEnd % 2) != 0;

        $fin = $es_primera_end
            ? "{$this->año}-" . str_pad($mesEnd, 2, '0', STR_PAD_LEFT) . "-15"
            : "{$this->año}-" . str_pad($mesEnd, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("{$this->año}-{$mesEnd}-01"));

        $checadaModel = new Checada();
        $this->checadas = $checadaModel->obtenerRegistrosPorEmpleado($this->employee->id, $inicio, $fin);
    }

    public function getQuincenasOptionsProperty()
    {
        $mesesEspanol = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $options = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $q1 = $mes * 2 - 1;
            $q2 = $mes * 2;

            $options[] = [
                'value' => $q1,
                'label' => "QNA " . str_pad($q1, 2, '0', STR_PAD_LEFT) . " (1RA " . mb_strtoupper($mesesEspanol[$mes]) . ")"
            ];
            $options[] = [
                'value' => $q2,
                'label' => "QNA " . str_pad($q2, 2, '0', STR_PAD_LEFT) . " (2DA " . mb_strtoupper($mesesEspanol[$mes]) . ")"
            ];
        }
        return $options;
    }

    public function render()
    {
        if ($this->isPortal) {
            return view('livewire.biometrico.portal-attendance');
        }

        $años = range(2024, (int)date('Y'));

        return view('livewire.biometrico.employee-attendance', [
            'años' => $años,
            'quincenas' => $this->quincenasOptions
        ])->layout('layouts.app');
    }
}