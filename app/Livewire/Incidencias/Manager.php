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

class Manager extends Component
{
    #[On('refreshIncidencias')]
    public function refresh()
    {
        // El componente se refrescará automáticamente
    }
    public $employeeId;
    public $employee;
    
    public $isModalOpen = false; // kept for compatibility, not used in new layout
    
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

    // Búscador de empleado
    public $searchEmployee = '';

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
            $this->isLicencia = false;
            $this->isIncapacidad = false;
            $this->isVacacional = false;
            $this->isTxt = false;
            return;
        }

        $codigoModel = CodigoDeIncidencia::find($value);
        if($codigoModel) {
            $codeInt = (int) $codigoModel->code;
            $this->isLicencia    = IncConstants::esLicencia($codeInt);
            $this->isIncapacidad = IncConstants::esIncapacidad($codeInt);
            $this->isVacacional  = IncConstants::esVacacional($codeInt);
            $this->isTxt         = ($codeInt === IncConstants::TXT); // 900
            $this->dateMode = ($this->isLicencia || $this->isIncapacidad || $this->isVacacional) ? 'range' : 'multiple';
        }
    }

    public function create()
    {
        $this->resetInputFields();
    }

    public function cambiarEmpleado($id)
    {
        $this->redirect(route('employees.incidencias', ['employeeId' => $id]));
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->codigo = '';
        $this->fechas_seleccionadas = '';
        $this->dateMode = 'multiple';
        $this->medico_id = '';
        $this->fecha_expedida = '';
        $this->diagnostico = '';
        $this->num_licencia = '';
        $this->periodo_id = '';
        $this->autoriza_txt = '';
        $this->cobertura_txt = '';
        
        $this->isLicencia = false;
        $this->isIncapacidad = false;
        $this->isVacacional = false;
        $this->isTxt = false;
    }

    public function store(IncidenciasService $service)
    {
        if (\Illuminate\Support\Facades\Cache::get('capture_maintenance', false) && !auth()->user()->admin()) {
            $this->dispatch('toast', [
                'icon' => 'error', 
                'title' => 'Sistema en mantenimiento: Captura deshabilitada.'
            ]);
            return;
        }
        $this->validate([
            'codigo' => 'required|exists:codigos_de_incidencias,id',
            'fechas_seleccionadas' => 'required|string',
        ]);

        try {
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
                'token' => sha1(time() . '_' . $this->employee->id),
            ];

            if ($this->dateMode === 'multiple') {
                $fechas = explode(', ', $this->fechas_seleccionadas);
                foreach ($fechas as $fecha) {
                    $fecha = trim($fecha);
                    if (!$fecha) continue;
                    $data = $baseData;
                    $data['datepicker_inicial'] = $fecha;
                    $data['datepicker_final'] = $fecha;
                    $service->crearIncidencias($data);
                }
            } else {
                // Remplazo preventivo en caso de variaciones de Flatpickr (" a ", " to ", "||", " - ", ", ")
                $fechasNormalized = str_replace([' a ', ' to ', '||', ' - ', ', '], '|', $this->fechas_seleccionadas);
                $parts = explode('|', $fechasNormalized);
                
                $inicio = trim($parts[0] ?? '');
                $fin = trim($parts[1] ?? '');
                
                if (empty($fin)) {
                    $fin = $inicio; // Si no hay segunda fecha, asume el mismo día inicial en caso de error de guardado prematuro
                }
                
                // Medida extra de seguridad contra envío de basuras HTML
                if (strlen($inicio) > 10) $inicio = substr($inicio, 0, 10);
                if (strlen($fin) > 10) $fin = substr($fin, 0, 10);
                
                if (empty($inicio) || empty($fin) || strlen($inicio) != 10) {
                    throw new \DomainException("El rango de fechas no se ha seleccionado correctamente por completo. Recibido: '{$this->fechas_seleccionadas}'");
                }
                
                $data = $baseData;
                $data['datepicker_inicial'] = $inicio;
                $data['datepicker_final'] = $fin;
                $service->crearIncidencias($data);
            }

            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Incidencia Capturada']);
            $this->dispatch('reset-calendar');
            // Si el motor de reglas generó un aviso de exceso, lo enviamos como modal de SweetAlert2
            if (session()->has('incapacidad_warning')) {
                $this->dispatch('swal', [
                    'icon' => 'warning',
                    'title' => '¡Aviso de Exceso!',
                    'text' => session('incapacidad_warning')
                ]);
            }

            $this->fechas_seleccionadas = ''; 
        } catch (\DomainException $e) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'icon' => 'error', 
                'title' => 'Error inesperado: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($token, IncidenciasService $service)
    {
        if (\Illuminate\Support\Facades\Cache::get('capture_maintenance', false) && !auth()->user()->admin()) {
            $this->dispatch('toast', [
                'icon' => 'error', 
                'title' => 'Sistema en mantenimiento: Eliminación deshabilitada.'
            ]);
            return;
        }
        $incidencias = Incidencia::with('qna')->where(function($q) use ($token) {
            $q->where('token', $token);
        })->get();
        
        foreach ($incidencias as $inc) {
            if ($inc->qna && $inc->qna->active != '1' && !auth()->user()->admin() && !auth()->user()->canCaptureInClosedQna($inc->qna->id)) {
                $this->dispatch('toast', [
                    'icon' => 'error', 
                    'title' => 'No se puede eliminar porque una de las partes de esta captura pertenece a una Quincena que ya ha sido cerrada.'
                ]);
                return;
            }
        }

        try {
            $service->eliminarPorToken($token);
            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Incidencia Eliminada']);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'icon' => 'error', 
                'title' => 'Error al eliminar: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        // Construir la lista de QNA IDs permitidas para mostrar:
        // 1. Siempre: las QNAs activas
        $allowedQnaIds = Qna::where(function($q) {
            $q->where('active', '1');
        })->pluck('id');

        // 2. Si el usuario tiene un pase temporal activo, agregar la QNA desbloqueada
        $exception = auth()->user()->activeCaptureException();
        if ($exception) {
            if ($exception->qna_id) {
                // Pase nuevo: tiene qna_id explícito
                $allowedQnaIds = $allowedQnaIds->push($exception->qna_id)->unique();
            } else {
                // Pase legacy (qna_id NULL): inferir la QNA más recientemente cerrada
                $lastClosed = Qna::where('active', '0')
                    ->orderBy('year', 'desc')
                    ->orderBy('qna', 'desc')
                    ->first();
                if ($lastClosed) {
                    $allowedQnaIds = $allowedQnaIds->push($lastClosed->id)->unique();
                }
            }
        }

        $incidencias = Incidencia::with(['qna', 'codigo', 'periodo'])
            ->where('employee_id', $this->employeeId)
            ->whereIn('qna_id', $allowedQnaIds)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
            
        $codigosFrecuentesIds = \Illuminate\Support\Facades\Cache::remember('codigos_frecuentes_3yrs', 86400, function() {
            return Incidencia::select('codigodeincidencia_id')
                ->where('fecha_inicio', '>=', now()->subYears(3)->startOfDay())
                ->groupBy('codigodeincidencia_id')
                ->orderByRaw('COUNT(*) DESC')
                ->take(10)
                ->pluck('codigodeincidencia_id')
                ->toArray();
        });

        $todosLosCodigos = CodigoDeIncidencia::orderBy('code')->get();
        // Extraer el top 10 y ordenarlo por el valor de su código de menor a mayor
        $topCodigos = $todosLosCodigos->whereIn('id', $codigosFrecuentesIds)->sortBy('code');
        $otrosCodigos = $todosLosCodigos->whereNotIn('id', $codigosFrecuentesIds);

        $periodos = Periodo::where(function($q) {
            $q->where('year', '>=', (int)date('Y') - 5);
        })
            ->orderBy('year', 'desc')
            ->orderBy('periodo', 'desc')
            ->get();

        $medicos = [];
        if ($this->isIncapacidad) {
            $doctorPuestos = ['24','25','28','30','56','57','58','59','60','61','62','63','64','65','66','67','68','87','88','101','95','96','97','98'];
            $medicos = Employe::whereIn('puesto_id', $doctorPuestos)
                ->orderBy('name')
                ->get();
        }

        $enabledDateRanges = [];
        $qnasPermitidas = Qna::whereIn('id', $allowedQnaIds)->get();
        foreach($qnasPermitidas as $qna) {
            if (!$qna->year || !$qna->qna) continue;
            $year = (int)$qna->year;
            $qnaNum = (int)$qna->qna;
            $mes = (int)ceil($qnaNum / 2);
            $isFirstHalf = ($qnaNum % 2) != 0;
            
            $startDate = \Carbon\Carbon::createFromDate($year, $mes, $isFirstHalf ? 1 : 16)->startOfDay();
            $endDate = $isFirstHalf 
                       ? \Carbon\Carbon::createFromDate($year, $mes, 15)->endOfDay()
                       : \Carbon\Carbon::createFromDate($year, $mes, 1)->endOfMonth()->endOfDay();
            
            $enabledDateRanges[] = [
                'from' => $startDate->format('Y-m-d'),
                'to' => $endDate->format('Y-m-d')
            ];
        }

        return view('livewire.incidencias.manager', compact('incidencias', 'topCodigos', 'otrosCodigos', 'periodos', 'medicos', 'enabledDateRanges'))->layout('layouts.app');
    }
}