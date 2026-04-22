<?php

namespace App\Livewire\Incidencias;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\CodigoDeIncidencia;
use App\Models\Qna;
use App\Models\Periodo;
use App\Services\Incidencias\IncidenciasService;
use App\Constants\Incidencias as IncConstants;
use App\Events\NewIncidenciaBatchCreated; // Importado para tiempo real
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Manager extends Component
{
    public $employeeId;
    public $employee;
    
    // Formulario de Captura
    public $codigo;
    public $fechas_seleccionadas;
    public $dateMode = 'multiple';
    
    // Campos dinámicos
    public $medico_id, $fecha_expedida, $diagnostico, $num_licencia;
    public $periodo_id, $autoriza_txt, $cobertura_txt;
    
    // Flags para saltar validaciones (Admin)
    public $saltar_validacion_inca = false;
    public $saltar_validacion_lic = false;
    
    // Banderas de UI
    public $isLicencia = false;
    public $isIncapacidad = false;
    public $isVacacional = false;
    public $isTxt = false;
    public $isComision = false;
    public $isOtorgado = false;
    public $motivo_comision;
    public $otorgado;

    // Propiedades para optimización (No se envían al cliente en cada request si son pesadas)
    protected $medicos = [];

    #[On('echo-presence:chat,GlobalMaintenanceEvent')]
    public function onMaintenanceToggle($event)
    {
        // El modo mantenimiento cambió. Livewire refrescará el componente solo con recibir el evento.
        // Pero añadimos un toast para informar al usuario inmediatamente.
        $this->dispatch('toast', [
            'icon' => $event['maintenance'] ? 'error' : 'success',
            'title' => $event['maintenance'] ? 'SISTEMA EN MANTENIMIENTO' : 'CAPTURA HABILITADA'
        ]);
    }

    #[On('refreshIncidencias')]
    public function refresh()
    {
        // Refresco automático
    }

    public function mount($numEmpleado)
    {
        $this->employee = Employe::with(['department', 'puesto', 'horario', 'jornada'])->where('num_empleado', $numEmpleado)->firstOrFail();
        $this->employeeId = $this->employee->id;

        $user = auth()->user();
        
        // Bloqueo por mantenimiento (excepto admins)
        if (Cache::get('capture_maintenance', false) && !$user->admin()) {
            // No abortamos para permitir ver el historial, pero el render se encargará de ocultar el form.
            // Solo notificamos si es navegación directa por si acaso.
        }

        if (!$user->admin()) {
            $hasAccess = $user->departments()->wherePivot('deparment_id', $this->employee->deparment_id)->exists();
            if (!$hasAccess) {
                abort(403, 'No tienes permiso para gestionar incidencias de este empleado.');
            }
        }
    }

    public function cambiarEmpleado($id)
    {
        $emp = Employe::findOrFail($id);
        return $this->redirect(route('employees.incidencias', ['numEmpleado' => $emp->num_empleado]), navigate: true);
    }

    public function updatedCodigo($value)
    {
        // Limpiamos la fecha si el usuario cambia de incidencia a media captura
        $this->fechas_seleccionadas = '';
        $this->dispatch('reset-calendar');

        // Resetear siempre los campos extras para evitar persistencia de datos de otros códigos
        $this->resetExtraFields();

        if(!$value) {
            $this->resetFlags();
            return;
        }

        $codigoModel = CodigoDeIncidencia::find($value);
        if($codigoModel) {
            $codeInt = (int) $codigoModel->code;
            $this->isLicencia    = IncConstants::esLicencia($codeInt);
            $this->isIncapacidad = IncConstants::esIncapacidad($codeInt);
            $this->isVacacional  = IncConstants::esVacacional($codeInt);
            $this->isTxt         = ($codeInt === IncConstants::TXT);
            $this->isComision    = IncConstants::esComisionOficial($codeInt);
            $this->isOtorgado    = ($codeInt === 901);
            $this->dateMode = ($this->isLicencia || $this->isIncapacidad || $this->isVacacional || $this->isComision) ? 'range' : 'multiple';
        }
    }

    private function resetExtraFields()
    {
        $this->medico_id = null;
        $this->fecha_expedida = null;
        $this->diagnostico = null;
        $this->num_licencia = null;
        $this->periodo_id = null;
        $this->autoriza_txt = null;
        $this->cobertura_txt = null;
        $this->motivo_comision = null;
        $this->otorgado = null;
    }

    private function resetFlags()
    {
        $this->isLicencia = false;
        $this->isIncapacidad = false;
        $this->isVacacional = false;
        $this->isTxt = false;
        $this->isComision = false;
        $this->isOtorgado = false;
        $this->motivo_comision = null;
        $this->otorgado = null;
    }

    public function store(IncidenciasService $service)
    {
        $user = auth()->user();

        if (!$user->canCapture()) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'No tienes permisos de captura.']);
            return;
        }

        if (Cache::get('capture_maintenance', false) && !$user->admin()) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Sistema en mantenimiento.']);
            return;
        }

        $rules = [
            'codigo' => 'required|exists:codigos_de_incidencias,id',
            'fechas_seleccionadas' => 'required|string',
        ];

        $messages = [
            'codigo.required' => 'Debe seleccionar un código',
            'fechas_seleccionadas.required' => 'Debe seleccionar al menos una fecha',
        ];

        if ($this->isIncapacidad) {
            $rules['medico_id'] = 'required';
            $rules['fecha_expedida'] = 'required|date';
            $rules['diagnostico'] = 'required|string|max:255';
            $rules['num_licencia'] = 'required|string|max:50';
            
            $messages['medico_id.required'] = 'El médico es obligatorio';
            $messages['fecha_expedida.required'] = 'La fecha expedida es obligatoria';
            $messages['diagnostico.required'] = 'El diagnóstico es obligatorio';
            $messages['num_licencia.required'] = 'El número de licencia es obligatorio';
        }

        if ($this->isVacacional) {
            $rules['periodo_id'] = 'required';
            $messages['periodo_id.required'] = 'El periodo vacacional es obligatorio';
        }

        if ($this->isTxt) {
            $rules['cobertura_txt'] = 'required|string|max:255';
            $rules['autoriza_txt'] = 'required|string|max:255';
            
            $messages['cobertura_txt.required'] = 'El sustituto es obligatorio';
            $messages['autoriza_txt.required'] = 'Quién autorizó es obligatorio';
        }

        if ($this->isComision) {
            $rules['motivo_comision'] = 'required|string|max:500';
            $messages['motivo_comision.required'] = 'El motivo de la comisión es obligatorio';
        }

        if ($this->isOtorgado) {
            $rules['otorgado'] = 'required|string|max:255';
            $messages['otorgado.required'] = 'El motivo de la omisión es obligatorio';
        }

        $this->validate($rules, $messages);

        try {
            $token = sha1(time() . '_' . $this->employee->id);
            $baseData = [
                'empleado_id' => $this->employee->id,
                'codigo' => $this->codigo,
                'medico_id' => $this->medico_id,
                'datepicker_expedida' => $this->fecha_expedida,
                'diagnostico' => $this->diagnostico,
                'num_licencia' => $this->num_licencia,
                'periodo_id' => $this->periodo_id,
                'autoriza_txt' => $this->autoriza_txt,
                'cobertura_txt' => $this->cobertura_txt,
                'motivo_comision' => $this->isComision ? $this->motivo_comision : null,
                'otorgado' => $this->isOtorgado ? $this->otorgado : null,
                'saltar_validacion_inca' => $this->saltar_validacion_inca ? "1" : "0",
                'saltar_validacion_lic' => $this->saltar_validacion_lic ? "1" : "0",
                'token' => $token,
            ];

            if ($this->dateMode === 'multiple') {
                $fechas = explode(', ', $this->fechas_seleccionadas);
                foreach ($fechas as $fecha) {
                    if (empty(trim($fecha))) continue;
                    $data = $baseData;
                    $data['datepicker_inicial'] = trim($fecha);
                    $data['datepicker_final'] = trim($fecha);
                    $service->crearIncidencias($data);
                }
            } else {
                $fechasNormalized = str_replace([' a ', ' to ', '||', ' - '], '|', $this->fechas_seleccionadas);
                $parts = explode('|', $fechasNormalized);
                $inicio = trim($parts[0] ?? '');
                $fin = trim($parts[1] ?? $inicio);

                $data = $baseData;
                $data['datepicker_inicial'] = substr($inicio, 0, 10);
                $data['datepicker_final'] = substr($fin, 0, 10);
                $service->crearIncidencias($data);
            }



            if (session()->has('incapacidad_warning')) {
                $this->dispatch('swal', [
                    'icon' => 'warning',
                    'title' => 'Aviso de Exceso',
                    'text' => session('incapacidad_warning'),
                ]);
            }

            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Incidencia Capturada']);
            $this->dispatch('reset-calendar');
            event(new NewIncidenciaBatchCreated());
            $this->fechas_seleccionadas = ''; 
        } catch (\Exception $e) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => $e->getMessage()]);
        }
    }

    public function delete($token, IncidenciasService $service)
    {
        $user = auth()->user();

        if (!$user->canCapture()) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'No tienes permisos para eliminar.']);
            return;
        }

        if (Cache::get('capture_maintenance', false) && !$user->admin()) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Mantenimiento activo.']);
            return;
        }

        try {
            $service->eliminarPorToken($token);
            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Incidencia Eliminada']);
            event(new NewIncidenciaBatchCreated());
        } catch (\Exception $e) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => $e->getMessage()]);
        }
    }

    public function render()
    {
        // 1. Obtener IDs de Qnas permitidas (sin caché agresivo para reflejar cierres inmediatos)
        $allowedQnaIds = Qna::where('active', '1')->pluck('id');
        $exception = auth()->user()->activeCaptureException();
        if ($exception) {
            $qnaId = $exception->qna_id ?? Qna::where('active', '0')->orderBy('year', 'desc')->orderBy('qna', 'desc')->value('id');
            if ($qnaId && !$allowedQnaIds->contains($qnaId)) {
                $allowedQnaIds->push($qnaId);
            }
        }

        // 2. Cargar Médicos solo si es Incapacidad (Carga diferida)
        $medicos = [];
        if ($this->isIncapacidad) {
            $medicos = Cache::remember('medicos_list_array_v2', 3600, function() {
                $doctorPuestos = ['24','25','28','30','56','57','58','59','60','61','62','63','64','65','66','67','68','87','88','101','95','96','97','98'];
                $empleados = Employe::whereIn('puesto_id', $doctorPuestos)->orderBy('name')->get();
                
                // Mapeamos a stdClass para que en los re-renders de Livewire no se disparen los accessors 
                // de Eloquent. Usamos los valores crudos de la BD para evitar consultar por completo la API externa.
                return $empleados->map(function($emp) {
                    $rawName = $emp->getRawOriginal('name') . ' ' . $emp->getRawOriginal('father_lastname') . ' ' . $emp->getRawOriginal('mother_lastname');
                    return (object) [
                        'id' => $emp->id,
                        'fullname' => strtoupper(trim(preg_replace('/\s+/', ' ', $rawName))),
                        'num_empleado' => $emp->num_empleado, // Este accesor es local y seguro
                    ];
                })->toArray();
            });
        }

        // 3. Rangos de fechas habilitados con Caché
        $enabledDateRanges = $this->getEnabledDateRanges($allowedQnaIds);

        // 4. Códigos y Periodos
        $incidencias = Incidencia::with(['qna', 'codigo', 'periodo'])
            ->where('employee_id', $this->employeeId)
            ->whereIn('qna_id', $allowedQnaIds)
            ->orderBy('fecha_inicio', 'desc')->get();

        $todosLosCodigos = Cache::remember('catalogo_codigos_incidencia', 3600, fn() => CodigoDeIncidencia::orderBy('code')->get());
        $frecuentesIds = Cache::remember('codigos_frecuentes_3yrs', 86400, function() {
            return Incidencia::select('codigodeincidencia_id', DB::raw('count(*) as count'))
                ->where('fecha_inicio', '>=', now()->subYears(3))
                ->groupBy('codigodeincidencia_id')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('codigodeincidencia_id')
                ->toArray();
        });
        $topCodigos = $todosLosCodigos->whereIn('id', $frecuentesIds)->sortBy('code');
        $otrosCodigos = $todosLosCodigos->whereNotIn('id', $topCodigos->pluck('id'));
        
        $periodos = Cache::remember('catalogo_periodos_5yrs', 3600, function() {
            return Periodo::where('year', '>=', (int)date('Y') - 5)
                ->orderBy('year', 'desc')->orderBy('periodo', 'desc')->get();
        });

        return view('livewire.incidencias.manager', compact(
            'incidencias', 'topCodigos', 'otrosCodigos', 'periodos', 'medicos', 'enabledDateRanges'
        ))->layout('layouts.app');
    }

    private function getEnabledDateRanges($allowedIds)
    {
        $cacheKey = 'qna_ranges_' . md5(implode(',', $allowedIds->toArray()));
        
        return Cache::remember($cacheKey, 3600, function() use ($allowedIds) {
            $ranges = [];
            $qnas = Qna::whereIn('id', $allowedIds)->get();
            foreach($qnas as $qna) {
                $mes = (int)ceil($qna->qna / 2);
                $isFirst = ($qna->qna % 2) != 0;
                $ranges[] = [
                    'from' => Carbon::createFromDate($qna->year, $mes, $isFirst ? 1 : 16)->format('Y-m-d'),
                    'to' => $isFirst ? Carbon::createFromDate($qna->year, $mes, 15)->format('Y-m-d') 
                                     : Carbon::createFromDate($qna->year, $mes, 1)->endOfMonth()->format('Y-m-d')
                ];
            }
            return $ranges;
        });
    }
}