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
    public $medico_search = '';
    public $medico_selected_name = '';
    public $cobertura_txt, $autoriza_txt, $motivo_comision, $otorgado_txt;
    
    public $is_incapacidad = false;
    public $is_vacaciones = false;
    public $is_txt = false;
    public $is_comision = false;
    public $is_otorgado = false;
    public $periodo_search = '';
    public $periodo_selected_name = '';

    // Historial de sesión
    public $recentCaptures = [];
    public $lastAddedToken = null;

    public function mount()
    {
        $this->recentCaptures = session()->get('recent_captures_v2', []);
    }

    public function loadEmployeeData($id)
    {
        $this->selectedEmployee = Employe::find($id);
        if ($this->selectedEmployee) {
            $this->employee_input = $this->selectedEmployee->num_empleado;
            
            // Cargar incidencias reales de TODAS las quincenas activas
            $activeQnaIds = Qna::where('active', '1')->pluck('id');
            
            if ($activeQnaIds->isNotEmpty()) {
                $dbIncidencias = Incidencia::with(['medico', 'codigo', 'qna', 'periodo'])
                    ->where('employee_id', $this->selectedEmployee->id)
                    ->whereIn('qna_id', $activeQnaIds)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $this->recentCaptures = $dbIncidencias->map(function($inc) {
                    return [
                        'token' => 'db_' . $inc->id, // Prefijo para saber que es de DB
                        'id' => $inc->id,
                        'codigo' => $inc->codigo->code ?? '??',
                        'description' => $inc->codigo->description ?? '??',
                        'f_inicio' => Carbon::parse($inc->fecha_inicio)->format('d/m/y'),
                        'f_final' => Carbon::parse($inc->fecha_final)->format('d/m/y'),
                        'total_dias' => $inc->total_dias,
                        'qna' => ($inc->qna->qna ?? '??') . '/' . substr($inc->qna->year ?? '??', -2),
                        'periodo' => $inc->periodo ? $inc->periodo->periodo . '/' . $inc->periodo->year : '--',
                        'medico_info' => $inc->medico_expeditor ?? ($inc->medico ? $inc->medico->name . ' ' . $inc->medico->father_lastname . ' ' . $inc->medico->mother_lastname : null),
                        'folio' => $inc->num_licencia ?? null,
                        'has_diagnostico' => !empty($inc->diagnostico),
                        'diagnostico_text' => $inc->diagnostico,
                        'has_comision' => !empty($inc->motivo_comision),
                        'comision_text' => $inc->motivo_comision,
                        'has_otorgado' => !empty($inc->otorgado),
                        'otorgado_text' => $inc->otorgado,
                        'has_fecha_expedida' => !empty($inc->fecha_expedida),
                        'fecha_expedida_text' => $inc->fecha_expedida ? Carbon::parse($inc->fecha_expedida)->format('d/m/Y') : null,
                        'has_cobertura' => !empty($inc->cobertura_txt),
                        'cobertura_text' => $inc->cobertura_txt,
                        'has_autoriza' => !empty($inc->autoriza_txt),
                        'autoriza_text' => $inc->autoriza_txt,
                        'capturado_por' => $inc->capturado_por ?? 'Sist',
                        'fecha_capturado' => $inc->created_at->format('d/m H:i'),
                        'time' => 'GUARDADO'
                    ];
                })->toArray();
            } else {
                $this->recentCaptures = [];
            }
            
            $this->dispatch('focus-next', ['field' => 'codigo']);
        }
    }

    public function clearEmployee()
    {
        $this->employee_input = '';
        $this->employeeId = null;
        $this->selectedEmployee = null;
        $this->recentCaptures = [];
        $this->resetRow();
        session()->forget('recent_captures_v2');
    }

    public function updatedEmployeeInput($value)
    {
        $this->employeeId = null;
        $this->selectedEmployee = null;
        // Limpiar el grid al cambiar de empleado (Requisito 1)
        $this->recentCaptures = [];
        session()->forget('recent_captures_v2');

        if ($value && strlen($value) >= 3) {
            $employee = Employe::with(['department'])->where('num_empleado', $value)->first();
            
            if ($employee) {
                $this->employeeId = $employee->id;
                $this->loadEmployeeData($employee->id);
            }
        }
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

                // Si el código es válido y no hay errores bloqueantes, saltar a la fecha
                if (!$this->getErrorBag()->has('general')) {
                    $this->dispatch('focus-next', ['field' => 'fecha_inicio']);
                }
            }
        }
    }

    public function updatedFechaInicio($value)
    {
        if ($value && strlen($value) >= 8) {
            $this->dispatch('focus-next', ['field' => 'fecha_final']);
        }
    }

    public function updatedFechaFinal($value)
    {
        if ($value && strlen($value) >= 8) {
            // Si es incapacidad, saltar al buscador de médicos
            if ($this->is_incapacidad) {
                $this->dispatch('focus-next', ['field' => 'medico_search']);
            } 
            // Si es TXT, saltar a quién cubrió
            elseif ($this->is_txt) {
                $this->dispatch('focus-next', ['field' => 'cobertura_txt']);
            }
            // Si es comisión, saltar al motivo
            elseif ($this->is_comision) {
                $this->dispatch('focus-next', ['field' => 'motivo_comision']);
            }
            // Si es otorgado, saltar a los detalles
            elseif ($this->is_otorgado) {
                $this->dispatch('focus-next', ['field' => 'otorgado_txt']);
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
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_final' => $this->fecha_final,
                'medico_id' => $this->medico_id,
                'diagnostico' => $this->diagnostico,
                'num_licencia' => $this->num_licencia,
                'fecha_expedida' => $this->fecha_expedida,
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

            $token = sha1(time() . '_' . $this->employeeId . '_' . $this->codigo_id);
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

            $this->loadEmployeeData($this->employeeId);
            
            // Tomamos el token del primer registro (el más reciente) para el brillo
            if (isset($this->recentCaptures[0]['token'])) {
                $this->lastAddedToken = $this->recentCaptures[0]['token'];
            }
            
            // Resetear solo los datos del renglón, mantener al empleado
            $this->resetRow();
            
        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        }
    }

    public function delete($token)
    {
        // Si el token empieza con db_, es un registro real
        if (str_starts_with($token, 'db_')) {
            $id = substr($token, 3);
            $inc = Incidencia::find($id);
            if ($inc) {
                $inc->delete();
            }
        }

        $this->recentCaptures = array_filter($this->recentCaptures, function($cap) use ($token) {
            return $cap['token'] !== $token;
        });
        
        $this->recentCaptures = array_values($this->recentCaptures);
        session()->put('recent_captures_v2', $this->recentCaptures);
    }

    public function selectPeriodo($id, $name)
    {
        $this->periodo_id = $id;
        $this->periodo_selected_name = $name;
        $this->periodo_search = ''; 
        $this->validateRealTime();
        // Después de elegir periodo, saltamos al botón de guardar (o código si prefieres)
        $this->dispatch('focus-next', ['field' => 'save_button']);
    }

    public function selectMedico($id, $name)
    {
        $this->medico_id = $id;
        $this->medico_selected_name = $name;
        $this->medico_search = ''; // Limpiar búsqueda para cerrar lista
        $this->validateRealTime();
    }

    private function resetRow()
    {
        $this->codigo_input = '';
        $this->codigo_id = null;
        $this->selectedCodigo = null;
        $this->fecha_inicio = '';
        $this->fecha_final = '';
        $this->periodo_id = null;
        $this->periodo_search = '';
        $this->periodo_selected_name = '';
        
        $this->medico_id = null;
        $this->medico_selected_name = '';
        $this->medico_search = '';
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
            $allMedicos = Cache::remember('medicos_list_array_v3', 3600, function() {
                $doctorPuestos = ['24','25','28','30','56','57','58','59','60','61','62','63','64','65','66','67','68','87','88','101','95','96','97','98'];
                return Employe::whereIn('puesto_id', $doctorPuestos)
                    ->orderBy('name')
                    ->get(['id', 'name', 'father_lastname', 'mother_lastname', 'num_empleado'])
                    ->map(function($m) {
                        return [
                            'id' => $m->id,
                            'fullname' => $m->name . ' ' . $m->father_lastname . ' ' . $m->mother_lastname,
                            'num_empleado' => $m->num_empleado
                        ];
                    });
            });

            if ($this->medico_search) {
                $search = strtoupper($this->medico_search);
                $medicos = $allMedicos->filter(function($m) use ($search) {
                    return str_contains(strtoupper($m['fullname']), $search) || 
                           str_contains($m['num_empleado'], $search);
                })->take(10); // Solo mostramos los primeros 10 resultados para no saturar
            }
        }

        $periodos = [];
        if ($this->is_vacaciones) {
            $allPeriodos = Cache::remember('catalogo_periodos_5yrs_v2', 3600, function() {
                return Periodo::where('year', '>=', (int)date('Y') - 5)
                    ->orderBy('year', 'desc')
                    ->orderBy('periodo', 'desc')
                    ->get(['id', 'periodo', 'year'])
                    ->map(function($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->periodo . '/' . $p->year
                        ];
                    });
            });

            if ($this->periodo_search) {
                $search = $this->periodo_search;
                $periodos = $allPeriodos->filter(function($p) use ($search) {
                    return str_contains($p['name'], $search);
                })->take(10);
            } else {
                $periodos = $allPeriodos->take(10);
            }
        }

        return view('livewire.incidencias.rapida', [
            'medicos' => $medicos,
            'periodos' => $periodos,
            'displayCaptures' => $this->recentCaptures
        ])->layout('layouts.app');
    }
}
