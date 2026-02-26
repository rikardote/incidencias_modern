<?php

namespace App\Livewire\Biometrico;

use App\Models\Checada;
use App\Models\Department;
use App\Models\Employe;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $centro_seleccionado;
    public $año_seleccionado;
    public $quincena_seleccionada;
    
    public $fecha_inicio;
    public $fecha_fin;

    public function mount()
    {
        $this->año_seleccionado = date('Y');
        $this->quincena_seleccionada = (date('d') <= 15) ? ((date('n') * 2) - 1) : (date('n') * 2);
        
        // Cargar primer centro disponible si no hay uno seleccionado
        $user = Auth::user();
        if (!$user->admin()) {
            $this->centro_seleccionado = $user->departments()->first()?->deparment_id;
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['año_seleccionado', 'quincena_seleccionada', 'centro_seleccionado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        // Centros autorizados
        $centrosQuery = Department::query();
        if (!$user->admin()) {
            $centrosQuery->whereIn('id', $user->departments()->pluck('deparment_id'));
        }
        $centros = $centrosQuery->orderBy('code')->get();

        // Opciones de quincenas
        $quincenas = $this->getQuincenasOptions();
        $años = range(2024, (int)date('Y'));

        // Calcular fechas
        $this->calcularFechas();

        $empleados = collect();
        if ($this->centro_seleccionado) {
            $checadaModel = new Checada();
            $registros = $checadaModel->obtenerRegistros($this->centro_seleccionado, $this->fecha_inicio, $this->fecha_fin);
            
            $empleados = $registros->groupBy('num_empleado')->sortBy(function($grupo) {
                return intval($grupo->first()->num_empleado);
            });
        }

        return view('livewire.biometrico.index', [
            'centros' => $centros,
            'quincenas' => $quincenas,
            'años' => $años,
            'empleados' => $empleados,
            'incidenciasSinColor' => ['7','17','40','41','42','46','49','51','53','54','55','60','61','62','63','77','94','901']
        ]);
    }

    private function getQuincenasOptions()
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

    private function calcularFechas()
    {
        $mes = ceil($this->quincena_seleccionada / 2);
        $es_primera_quincena = ($this->quincena_seleccionada % 2) != 0;

        $this->fecha_inicio = $es_primera_quincena
            ? "{$this->año_seleccionado}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01"
            : "{$this->año_seleccionado}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-16";

        $this->fecha_fin = $es_primera_quincena
            ? "{$this->año_seleccionado}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-15"
            : "{$this->año_seleccionado}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("{$this->año_seleccionado}-{$mes}-01"));
    }

    public function exportPdf()
    {
        if (!$this->centro_seleccionado) return;
        
        return redirect()->route('biometrico.exportar', [
            'centro' => $this->centro_seleccionado,
            'año' => $this->año_seleccionado,
            'quincena' => $this->quincena_seleccionada
        ]);
    }
}
