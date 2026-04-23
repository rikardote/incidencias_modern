<?php

namespace App\Services\Incidencias;

use App\Models\Employe;
use App\Models\CodigoDeIncidencia;
use App\Models\Incidencia;
use App\Models\Qna;
use App\Services\Incidencias\Rules\DuplicadosRule;
use App\Services\Incidencias\Rules\TXTRule;
use App\Services\Incidencias\Rules\OnomasticoRule;
use App\Services\Incidencias\Rules\VacacionesRule;
use App\Services\Incidencias\Rules\PaseSalidaRule;
use App\Services\Incidencias\Rules\LicenciaConGoceRule;
use App\Services\Incidencias\Rules\IncapacidadRule;
use App\Services\Incidencias\Rules\Limite0809Rule;
use App\Constants\Incidencias as Inc;
use App\Services\Incidencias\SegmentadorQuincenal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncidenciasService
{
    protected $helpers;

    public function __construct(\App\Services\Incidencias\IncidenciaHelpersService $helpers = null)
    {
        $this->helpers = $helpers ?: app(\App\Services\Incidencias\IncidenciaHelpersService::class);
    }
    public function validarCaptura(array $data)
    {
        // 1. Validar existencia básica de empleado
        if (empty($data['empleado_id'])) return;
        $empleado = Employe::find($data['empleado_id']);
        if (!$empleado) throw new \DomainException('Empleado no encontrado');

        // 2. Validar Código (si existe)
        if (!empty($data['codigo'])) {
            $incidenciaCodigo = CodigoDeIncidencia::find($data['codigo']);
            if (!$incidenciaCodigo) throw new \DomainException('Código de incidencia no válido');
            
            $codeReal = (int)$incidenciaCodigo->code;

            // --- REGLAS DE ELEGIBILIDAD INMEDIATA (SIN FECHAS) ---
            
            // Regla: TXT (900) solo para Base (1)
            if ($codeReal == Inc::TXT && (int)$empleado->condicion_id !== 1) {
                throw new \DomainException('Solo el personal de BASE puede cubrir T.X.T.');
            }

            // Regla: Licencia con Goce (41) solo para Base (1)
            if ($codeReal == 41 && (int)$empleado->condicion_id !== 1) {
                throw new \DomainException('Las licencias con goce de sueldo (41) solo aplican para personal de BASE.');
            }

            // Regla: Onomástico (30) - Validar si tiene derecho (Opcional, pero se puede añadir aquí)
            // ----------------------------------------------------
        }

        // Mapear campos de captura rápida a nombres internos si es necesario
        $fInicio = $data['fecha_inicio'] ?? $data['datepicker_inicial'] ?? null;
        $fFinal = $data['fecha_final'] ?? $data['datepicker_final'] ?? null;

        // 3. Validar Fecha Inicio (Solo si está completa: 10 chars)
        if (!empty($fInicio) && strlen($fInicio) === 10) {
            $inicio = $this->helpers->fechaYmd($fInicio);
            $qna = $this->helpers->qnaDesdeFecha($inicio);
            
            if ($qna) {
                $qnaModel = Qna::find($qna);
                if ($qnaModel && ($qnaModel->active != '1' || ($qnaModel->cierre && now()->greaterThan($qnaModel->cierre)))) {
                    if (!auth()->user()->canCaptureInClosedQna($qna)) {
                        throw new \DomainException("La quincena Q{$qnaModel->qna}/{$qnaModel->year} está cerrada para captura.");
                    }
                }
            }
        }

        // 4. Validar Fecha Final y Traslapes (Solo si todo existe y está completo)
        if (!empty($fInicio) && strlen($fInicio) === 10 && !empty($fFinal) && strlen($fFinal) === 10 && !empty($data['codigo'])) {
            $inicio = $this->helpers->fechaYmd($fInicio);
            $fin = $this->helpers->fechaYmd($fFinal);

            if (strtotime($fin) < strtotime($inicio)) {
                throw new \DomainException('La fecha final no puede ser anterior a la inicial');
            }

            // Validar traslapes
            $duplicadosRule = new DuplicadosRule();
            if ($conflict = $duplicadosRule->yaCapturado($empleado, $inicio, $fin, $incidenciaCodigo->code)) {
                $esMismoCodigo = ($conflict->code == $incidenciaCodigo->code);
                throw new \DomainException($esMismoCodigo ? 'Incidencia Duplicada' : 'Traslape con Código ' . $conflict->code);
            }

            // Correr resto de reglas específicas (Incapacidades, etc)
            $incidencia = new Incidencia();
            $incidencia->employee_id = $empleado->id;
            $incidencia->codigodeincidencia_id = $incidenciaCodigo->id;
            $incidencia->fecha_inicio = $inicio;
            $incidencia->fecha_final = $fin;
            $incidencia->total_dias = \Carbon\Carbon::parse($inicio)->diffInDays(\Carbon\Carbon::parse($fin)) + 1;

            $reglasEspecificas = [
                new OnomasticoRule(),
                new LicenciaConGoceRule($this->helpers),
                new IncapacidadRule($this->helpers),
                new VacacionesRule(),
                new PaseSalidaRule(),
                new TXTRule($this->helpers),
            ];

            foreach ($reglasEspecificas as $regla) {
                try {
                    $regla->aplicar($incidencia, $empleado, $data, $codeReal);
                } catch (\DomainException $e) {
                    // Solo lanzamos si no es una excepción de campos faltantes que aún no se capturan
                    if (!str_contains($e->getMessage(), 'Debe') && !str_contains($e->getMessage(), 'seleccionar')) {
                        throw $e;
                    }
                }
            }
        }
    }

    public function crearIncidencias(array $data)
    {
        $empleado = Employe::findOrFail($data['empleado_id']);
        $incidenciaCodigo = CodigoDeIncidencia::findOrFail($data['codigo']);
        $codeReal = (int)$incidenciaCodigo->code;

        // Validar duplicados y traslapes
        $duplicadosRule = new DuplicadosRule();
        $inicio = $this->helpers->fechaYmd($data['datepicker_inicial']);
        $fin = $this->helpers->fechaYmd($data['datepicker_final']);

        if ($conflict = $duplicadosRule->yaCapturado($empleado, $inicio, $fin, $incidenciaCodigo->code)) {
            $esMismoCodigo = ($conflict->code == $incidenciaCodigo->code);
            throw new \DomainException($esMismoCodigo ? 'Incidencia Duplicada' : 'Incidencia Traslape');
        }

        // Usamos request() global como respaldo si $data viene incompleto
        $saltarLic = (
            (isset($data['saltar_validacion_lic']) && $data['saltar_validacion_lic'] == "1") ||
            (request('saltar_validacion_lic') == "1")
            );

        $saltarInca = (
            (isset($data['saltar_validacion_inca']) && $data['saltar_validacion_inca'] == "1") ||
            (request('saltar_validacion_inca') == "1")
            );

        $token = $data['token'] ?? sha1(time() . $empleado->id);

        $segmentos = SegmentadorQuincenal::calcularSegmentos($inicio, $fin, function ($fecha) {
            return $this->helpers->qnaDesdeFecha($fecha);
        });

        return DB::transaction(function () use ($segmentos, $empleado, $incidenciaCodigo, $codeReal, $data, $token, $saltarLic, $saltarInca, $inicio, $fin) {
            foreach ($segmentos as $segmento) {
                $incidencia = new Incidencia();
                $incidencia->employee_id = $empleado->id;
                $incidencia->codigodeincidencia_id = $incidenciaCodigo->id;
                $incidencia->fecha_inicio = $segmento['fecha_inicio'];
                $incidencia->fecha_final = $segmento['fecha_final'];
                $incidencia->qna_id = $segmento['qna_id'];

                // Asignación inicial (4 días)
                $inicioSeg = \Carbon\Carbon::parse($segmento['fecha_inicio']);
                $finSeg = \Carbon\Carbon::parse($segmento['fecha_final']);
                $incidencia->total_dias = $inicioSeg->diffInDays($finSeg) + 1;

                $incidencia->token = $token;
                $incidencia->fecha_capturado = \Carbon\Carbon::now();
                $incidencia->capturado_por = $this->helpers->capturadoPor(auth()->user()->id);
                $incidencia->motivo_comision = $data['motivo_comision'] ?? null;
                $incidencia->otorgado = $data['otorgado'] ?? null;

                $esLicencia = Inc::esLicencia($codeReal);
                $esIncapacidad = Inc::esIncapacidad($codeReal);
                $esFalta = ($codeReal === Inc::FALTA);

                $reglasEspecificas = [
                    new TXTRule($this->helpers),
                    new OnomasticoRule(),
                    new LicenciaConGoceRule($this->helpers),
                    new IncapacidadRule($this->helpers),
                    new VacacionesRule(), // No usa helpers actualmente
                    new PaseSalidaRule(), // No usa helpers actualmente
                    // new Limite0809Rule(), // Desactivado temporalmente
                ];

                foreach ($reglasEspecificas as $regla) {
                    $regla->aplicar($incidencia, $empleado, $data, $codeReal);
                }

                // Si saltarLic es true, NO entra aquí
                if ($esLicencia && !$saltarLic) {
                    $this->aplicarPesoJornada($incidencia, $empleado);
                }

                if ($esIncapacidad && !$saltarInca) {
                    $this->aplicarPesoJornada($incidencia, $empleado);
                }

                if ($esFalta) {
                    $this->aplicarPesoJornada($incidencia, $empleado);
                }

                $incidencia->save();
            }

            return $empleado->id;
        });
    }

    private function aplicarPesoJornada($incidencia, $empleado)
    {
        $jornadaId = (int)$empleado->jornada_id;

        if (Inc::esGuardia($jornadaId)) {
            $incidencia->total_dias = Inc::DIAS_GUARDIAS;
        }
        elseif (Inc::esSyfDyf($jornadaId)) {
            $incidencia->total_dias = Inc::DIAS_SYF_DYF;
        }
    }

    public function eliminarPorToken($token)
    {
        if (!$token) return;

        $user = auth()->user();
        
        // 1. Obtener solo las quincenas únicas involucradas en el lote
        $incidencias = DB::table('incidencias')
            ->where('token', $token)
            ->whereNull('deleted_at')
            ->select('qna_id', 'fecha_inicio')
            ->get();
        
        if ($incidencias->isEmpty()) return;

        $qnaIds = $incidencias->pluck('qna_id')->filter()->unique()->toArray();
        $sinQnaId = $incidencias->whereNull('qna_id');

        // Resolver IDs faltantes quincena por fecha (caso de registros migrados sin ID)
        foreach ($sinQnaId as $inc) {
            $resolvedId = qna_year($inc->fecha_inicio);
            if ($resolvedId && !in_array($resolvedId, $qnaIds)) {
                $qnaIds[] = $resolvedId;
            }
        }

        // 2. Verificar quincenas en lote
        if (!empty($qnaIds)) {
            $qnas = Qna::whereIn('id', $qnaIds)->get();
            
            foreach ($qnas as $qna) {
                $isClosed = ($qna->active != '1' || ($qna->cierre && now()->greaterThan($qna->cierre)));
                
                if ($isClosed) {
                    if ($user && !$user->canCaptureInClosedQna($qna->id)) {
                        throw new \DomainException("No se puede eliminar esta incidencia porque pertenece a una quincena cerrada (Q{$qna->qna}/{$qna->year}).");
                    }
                }
            }
        }

        // 3. Ejecutar la actualización masiva
        return DB::table('incidencias')->where('token', $token)->update(['deleted_at' => Carbon::now()]);
    }
}