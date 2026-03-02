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
    public $año;
    public $checadas = [];

    public function mount($employeeId)
    {
        $this->employee = Employe::with(['department', 'puesto', 'horario'])->findOrFail($employeeId);
        $this->año = (int)date('Y');

        // Calcular quincena actual
        $day = (int)date('d');
        $month = (int)date('n');
        $this->quincena = ($day <= 15) ? ($month * 2 - 1) : ($month * 2);

        $this->loadChecadas();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['quincena', 'año'])) {
            $this->loadChecadas();
        }
    }

    public function loadChecadas()
    {
        $mes = ceil($this->quincena / 2);
        $es_primera = ($this->quincena % 2) != 0;

        $inicio = $es_primera
            ? "{$this->año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01"
            : "{$this->año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-16";

        $fin = $es_primera
            ? "{$this->año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-15"
            : "{$this->año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("{$this->año}-{$mes}-01"));

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
        $años = range(2024, (int)date('Y'));

        return view('livewire.biometrico.employee-attendance', [
            'años' => $años,
            'quincenas' => $this->quincenasOptions
        ])->layout('layouts.app');
    }
}