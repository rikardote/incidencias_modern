<?php

namespace App\Livewire\Incidencias;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\CodigoDeIncidencia;
use App\Models\Qna;
use App\Models\Periodo;
use App\Services\Incidencias\IncidenciasService;
use App\Constants\Incidencias as IncConstants;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Rapida extends Component
{
    // Formulario de Selección de Empleado
    public $employee_input; // E.j. '332618'
    public $employeeId;
    public $selectedEmployee;
    
    // Renglón de Captura Actual
    public $codigo_input; // E.j. '55', '1', '10'
    public $codigo_id;
    public $selectedCodigo;
    public $fecha_inicio;
    public $fecha_final;
    public $periodo_id;
    
    // Campos Extra (Incapacidad/Otros)
    public $medico_id, $diagnostico, $num_licencia, $fecha_expedida;
    public $cobertura_txt, $autoriza_txt, $motivo_comision, $otorgado_txt;
    
    public $is_incapacidad = false;
    public $is_vacaciones = false;
    public $is_txt = false;
    public $is_comision = false;
    public $is_otorgado = false;

    // Historial de sesión
    public $recentCaptures = [];

    public function mount()
    {
        $this->recentCaptures = session()->get('recent_captures_v2', []);
    }

    public function updatedEmployeeInput($value)
    {
        $this->employeeId = null;
        $this->selectedEmployee = null;

        if ($value && strlen($value) >= 3) {
            $employee = Employe::with(['department'])->where('num_empleado', $value)->first();
            
            if ($employee) {
                $this->employeeId = $employee->id;
                $this->selectedEmployee = $employee;
                // Foco al siguiente campo (Código)
                $this->dispatch('focus-next', ['field' => 'codigo']);
            }
        }
    }

    public function changeEmployee()
    {
        $this->employee_input = '';
        $this->employeeId = null;
        $this->selectedEmployee = null;
        $this->resetRow();
    }

    public function updatedCodigoInput($value)
    {
        $this->codigo_id = null;
        $this->selectedCodigo = null;
        $this->fecha_inicio = null;
        $this->fecha_final = null;
        $this->resetFlags();
        $this->resetErrorBag();

        if ($value) {
            $codigo = CodigoDeIncidencia::where('code', $value)->first();
            
            if ($codigo) {
                $this->codigo_id = $codigo->id;
                $this->selectedCodigo = $codigo;
                $codeInt = (int) $codigo->code;
                
                $this->is_incapacidad = IncConstants::esIncapacidad($codeInt);
                $this->is_vacaciones = IncConstants::esVacacional($codeInt);
                $this->is_txt = ($codeInt === IncConstants::TXT);
                $this->is_comision = IncConstants::esComisionOficial($codeInt);
                $this->is_otorgado = ($codeInt === 901);

                // Validar elegibilidad de inmediato
                $this->validateRealTime();

                // SOLO si NO hay errores, permitimos saltar a la fecha
                if (!$this->getErrorBag()->has('general') && !$this->is_incapacidad && !$this->is_vacaciones && !$this->is_txt && !$this->is_comision && !$this->is_otorgado) {
                    $this->dispatch('focus-next', ['field' => 'fecha_inicio']);
                }
            }
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['employee_input', 'codigo_input', 'fecha_inicio', 'fecha_final', 'medico_id', 'diagnostico', 'num_licencia', 'fecha_expedida', 'cobertura_txt', 'autoriza_txt', 'motivo_comision'])) {
            $this->validateRealTime();
        }
    }

    protected function validateRealTime()
    {
        $this->resetErrorBag();
        
        if (!$this->selectedEmployee) {
            return;
        }

        try {
            $service = app(IncidenciasService::class);
            $service->validarCaptura([
                'empleado_id' => $this->selectedEmployee->id,
                'codigo' => $this->selectedCodigo?->id, // Puede ser null aún
                'datepicker_inicial' => $this->fecha_inicio,
                'datepicker_final' => $this->fecha_final,
                'medico_id' => $this->medico_id,
                'diagnostico' => $this->diagnostico,
                'num_licencia' => $this->num_licencia,
                'datepicker_expedida' => $this->fecha_expedida,
                'cobertura_txt' => $this->cobertura_txt,
                'autoriza_txt' => $this->autoriza_txt,
                'motivo_comision' => $this->motivo_comision,
                'periodo_id' => $this->periodo_id,
            ]);
        } catch (\DomainException $e) {
            $this->addError('general', $e->getMessage());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
        }
    }

    public function store(IncidenciasService $service)
    {
        $this->validate([
            'employeeId' => 'required',
            'codigo_id' => 'required',
            'fecha_inicio' => 'required',
        ]);

        try {
            $parseDate = function($val) {
                if (!$val) return null;
                $val = str_replace('-', '/', $val);
                $parts = explode('/', $val);
                if (count($parts) == 3) {
                    $y = $parts[2];
                    if (strlen($y) == 2) $parts[2] = '20' . $y;
                    return Carbon::createFromFormat('d/m/Y', implode('/', $parts))->format('Y-m-d');
                }
                return null;
            };

            $f_inicio = $parseDate($this->fecha_inicio);
            $f_final = $this->fecha_final ? $parseDate($this->fecha_final) : $f_inicio;

            if (!$f_inicio) throw new \Exception("Formato de fecha de inicio inválido");

            $token = sha1(time() . '_' . $this->employeeId);
            $service->crearIncidencias([
                'empleado_id' => $this->employeeId,
                'codigo' => $this->codigo_id,
                'datepicker_inicial' => $f_inicio,
                'datepicker_final' => $f_final,
                'periodo_id' => $this->periodo_id,
                'token' => $token,
                // Médicos
                'medico_id' => $this->medico_id,
                'diagnostico' => $this->diagnostico,
                'num_licencia' => $this->num_licencia,
                'datepicker_expedida' => $this->fecha_expedida ? $parseDate($this->fecha_expedida) : null,
                // Especiales
                'cobertura_txt' => $this->cobertura_txt,
                'autoriza_txt' => $this->autoriza_txt,
                'motivo_comision' => $this->motivo_comision,
                'otorgado' => $this->otorgado_txt,
            ]);

            // Agregar al historial
            $this->recentCaptures[] = [
                'token' => $token,
                'time' => now()
            ];
            session()->put('recent_captures_v2', $this->recentCaptures);

            $this->resetRow();
            $this->dispatch('focus-next', ['field' => 'employee']);
            
        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        }
    }

    public function delete($token)
    {
        Incidencia::where('token', $token)->delete();
        $this->recentCaptures = collect($this->recentCaptures)->filter(fn($c) => $c['token'] !== $token)->toArray();
        session()->put('recent_captures_v2', $this->recentCaptures);
    }

    private function resetRow()
    {
        $this->codigo_input = '';
        $this->codigo_id = null;
        $this->selectedCodigo = null;
        $this->fecha_inicio = '';
        $this->fecha_final = '';
        $this->periodo_id = null;
        
        $this->medico_id = null;
        $this->diagnostico = '';
        $this->num_licencia = '';
        $this->fecha_expedida = '';
        
        $this->resetFlags();
        $this->dispatch('focus-next', ['field' => 'codigo']);
    }

    private function resetFlags()
    {
        $this->is_incapacidad = false;
        $this->is_vacaciones = false;
        $this->is_txt = false;
        $this->is_comision = false;
        $this->is_otorgado = false;
        
        $this->cobertura_txt = '';
        $this->autoriza_txt = '';
        $this->motivo_comision = '';
        $this->otorgado_txt = '';
    }

    public function render()
    {
        $medicos = [];
        if ($this->is_incapacidad) {
            $medicos = Cache::remember('medicos_list_array_v2', 3600, function() {
                $doctorPuestos = ['24','25','28','30','56','57','58','59','60','61','62','63','64','65','66','67','68','87','88','101','95','96','97','98'];
                return Employe::whereIn('puesto_id', $doctorPuestos)->orderBy('name')->get(['id', 'name', 'father_lastname', 'mother_lastname', 'num_empleado']);
            });
        }

        $periodos = [];
        if ($this->is_vacaciones) {
            $periodos = Cache::remember('catalogo_periodos_5yrs', 3600, function() {
                return Periodo::where('year', '>=', (int)date('Y') - 5)->orderBy('year', 'desc')->orderBy('periodo', 'desc')->get();
            });
        }

        // Obtener datos reales de las capturas recientes para validar si existen
        $sessionTokens = collect($this->recentCaptures)->pluck('token')->toArray();
        $realCaptures = Incidencia::with(['employee', 'codigo', 'qna', 'periodo'])
            ->whereIn('token', $sessionTokens)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('token');

        $displayCaptures = [];
        foreach ($this->recentCaptures as $sessCap) {
            if ($realCaptures->has($sessCap['token'])) {
                $group = $realCaptures->get($sessCap['token']);
                $first = $group->first();
                $last = $group->last();
                
                $totalDias = $group->sum('total_dias');
                $qnaLabel = $first->qna ? "Q{$first->qna->qna}/" . substr($first->qna->year, -2) : '--';

                $displayCaptures[] = [
                    'token' => $sessCap['token'],
                    'qna' => $qnaLabel,
                    'codigo' => $first->codigo->code,
                    'employee' => $first->employee->num_empleado . ' - ' . $first->employee->fullname,
                    'f_inicio' => Carbon::parse($first->fecha_inicio)->format('d/m/y'),
                    'f_final' => Carbon::parse($last->fecha_final)->format('d/m/y'),
                    'total_dias' => $totalDias,
                    'periodo' => $first->periodo ? $first->periodo->periodo . '/' . $first->periodo->year : '--',
                    'quien' => $first->capturado_por ?? '--',
                    'time' => Carbon::parse($first->created_at)->diffForHumans(),
                ];
            }
        }

        return view('livewire.incidencias.rapida', [
            'medicos' => $medicos,
            'periodos' => $periodos,
            'displayCaptures' => $displayCaptures
        ])->layout('layouts.app');
    }
}
