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
    
    // Banderas de UI
    public $isLicencia = false;
    public $isIncapacidad = false;
    public $isVacacional = false;
    public $isTxt = false;

    // Propiedades para optimización (No se envían al cliente en cada request si son pesadas)
    protected $medicos = [];

    #[On('refreshIncidencias')]
    public function refresh()
    {
        // Refresco automático
    }

    public function mount($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->employee = Employe::with(['department', 'puesto', 'horario', 'jornada'])->findOrFail($employeeId);

        $user = auth()->user();
        if (!$user->admin()) {
            $hasAccess = $user->departments()->wherePivot('deparment_id', $this->employee->deparment_id)->exists();
            if (!$hasAccess) {
                abort(403, 'No tienes permiso para gestionar incidencias de este empleado.');
            }
        }
    }

    public function updatedCodigo($value)
    {
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
            $this->dateMode = ($this->isLicencia || $this->isIncapacidad || $this->isVacacional) ? 'range' : 'multiple';
        }
    }

    private function resetFlags()
    {
        $this->isLicencia = false;
        $this->isIncapacidad = false;
        $this->isVacacional = false;
        $this->isTxt = false;
    }

    public function store(IncidenciasService $service)
    {
        if (Cache::get('capture_maintenance', false) && !auth()->user()->admin()) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Sistema en mantenimiento.']);
            return;
        }

        $this->validate([
            'codigo' => 'required|exists:codigos_de_incidencias,id',
            'fechas_seleccionadas' => 'required|string',
        ]);

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

            // NOTIFICACIÓN TIEMPO REAL
            broadcast(new NewIncidenciaBatchCreated())->toOthers();

            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Incidencia Capturada']);
            $this->dispatch('reset-calendar');
            $this->fechas_seleccionadas = ''; 
        } catch (\Exception $e) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => $e->getMessage()]);
        }
    }

    public function delete($token, IncidenciasService $service)
    {
        if (Cache::get('capture_maintenance', false) && !auth()->user()->admin()) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Mantenimiento activo.']);
            return;
        }

        try {
            $service->eliminarPorToken($token);
            
            // NOTIFICACIÓN TIEMPO REAL
            broadcast(new NewIncidenciaBatchCreated())->toOthers();

            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Incidencia Eliminada']);
        } catch (\Exception $e) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => $e->getMessage()]);
        }
    }

    public function render()
    {
        // 1. Obtener IDs de Qnas permitidas
        $allowedQnaIds = Qna::where('active', '1')->pluck('id');
        $exception = auth()->user()->activeCaptureException();
        if ($exception) {
            $qnaId = $exception->qna_id ?? Qna::where('active', '0')->orderBy('year', 'desc')->orderBy('qna', 'desc')->value('id');
            if ($qnaId) $allowedQnaIds->push($qnaId)->unique();
        }

        // 2. Cargar Médicos solo si es Incapacidad (Carga diferida)
        $medicos = [];
        if ($this->isIncapacidad) {
            $medicos = Cache::remember('medicos_list', 3600, function() {
                $doctorPuestos = ['24','25','28','30','56','57','58','59','60','61','62','63','64','65','66','67','68','87','88','101','95','96','97','98'];
                return Employe::whereIn('puesto_id', $doctorPuestos)->orderBy('name')->get();
            });
        }

        // 3. Rangos de fechas habilitados con Caché
        $enabledDateRanges = $this->getEnabledDateRanges($allowedQnaIds);

        // 4. Códigos y Periodos
        $incidencias = Incidencia::with(['qna', 'codigo', 'periodo'])
            ->where('employee_id', $this->employeeId)
            ->whereIn('qna_id', $allowedQnaIds)
            ->orderBy('fecha_inicio', 'desc')->get();

        $todosLosCodigos = CodigoDeIncidencia::orderBy('code')->get();
        $frecuentesIds = Cache::get('codigos_frecuentes_3yrs', []);
        $topCodigos = $todosLosCodigos->whereIn('id', $frecuentesIds)->sortBy('code');
        $otrosCodigos = $todosLosCodigos->whereNotIn('id', $frecuentesIds);

        $periodos = Periodo::where('year', '>=', (int)date('Y') - 5)
            ->orderBy('year', 'desc')->orderBy('periodo', 'desc')->get();

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