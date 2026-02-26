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

class Manager extends Component
{
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
            $hasAccess = $user->departments()->where('deparment_id', $this->employee->deparment_id)->exists();
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
            $this->dispatch('toast', icon: 'error', title: 'Sistema en mantenimiento: Captura deshabilitada.');
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

            $this->dispatch('toast', icon: 'success', title: 'Incidencia(s) capturada(s) exitosamente.');
            $this->fechas_seleccionadas = ''; // Solo limpiamos las fechas, el resto queda para captura rápida
        } catch (\DomainException $e) {
            $this->dispatch('toast', icon: 'error', title: $e->getMessage());
        } catch (\Exception $e) {
            $this->dispatch('toast', icon: 'error', title: 'Error inesperado: ' . $e->getMessage() . ' Line: ' . $e->getLine());
        }
    }

    public function delete($token, IncidenciasService $service)
    {
        if (\Illuminate\Support\Facades\Cache::get('capture_maintenance', false) && !auth()->user()->admin()) {
            $this->dispatch('toast', icon: 'error', title: 'Sistema en mantenimiento: Eliminación deshabilitada.');
            return;
        }
        $incidencias = Incidencia::with('qna')->where('token', $token)->get();
        
        foreach ($incidencias as $inc) {
            if ($inc->qna && $inc->qna->active != '1' && !auth()->user()->admin() && !auth()->user()->canCaptureInClosedQna()) {
                $this->dispatch('toast', icon: 'error', title: 'No se puede eliminar porque una de las partes de esta captura pertenece a una Quincena que ya ha sido cerrada.');
                return;
            }
        }

        try {
            $service->eliminarPorToken($token);
            $this->dispatch('toast', icon: 'success', title: 'Incidencia(s) eliminada(s) exitosamente.');
        } catch (\Exception $e) {
            $this->dispatch('toast', icon: 'error', title: 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $incidenciasQuery = Incidencia::with(['qna', 'codigo', 'periodo'])
            ->where('employee_id', $this->employeeId);

        // Si no tiene pase especial, solo vemos incidencias de quincenas activas
        if (!auth()->user()->canCaptureInClosedQna()) {
            $incidenciasQuery->whereHas('qna', fn($q) => $q->where('active', '1'));
        }

        $incidencias = $incidenciasQuery->orderBy('fecha_inicio', 'desc')->get();
            
        $codigos = CodigoDeIncidencia::orderBy('code')->get();
        $periodos = Periodo::all();

        $medicos = [];
        if ($this->isIncapacidad) {
            $doctorPuestos = ['24','25','28','30','56','57','58','59','60','61','62','63','64','65','66','67','68','87','88','101','95','96','97','98'];
            $medicos = Employe::whereIn('puesto_id', $doctorPuestos)
                ->orderBy('name')
                ->get();
        }

        return view('livewire.incidencias.manager', [
            'incidencias' => $incidencias,
            'codigos' => $codigos,
            'periodos' => $periodos,
            'medicos' => $medicos,
        ])->layout('layouts.app');
    }
}
