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