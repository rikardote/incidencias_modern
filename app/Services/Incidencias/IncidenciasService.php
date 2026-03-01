<?php

namespace App\Services\Incidencias;

use App\Models\Employe;
use App\Models\CodigoDeIncidencia;
use App\Models\Incidencia;
use App\Models\Qna;
use App\Services\Incidencias\Rules\DuplicadosRule;
use App\Services\Incidencias\Rules\TXTRule;
use App\Services\Incidencias\Rules\VacacionesRule;
use App\Services\Incidencias\Rules\PaseSalidaRule;
use App\Services\Incidencias\Rules\LicenciaConGoceRule;
use App\Services\Incidencias\Rules\IncapacidadRule;
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

        if ($conflict = $duplicadosRule->yaCapturado($empleado->id, $inicio, $fin, $incidenciaCodigo->code)) {
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

                $esLicencia = Inc::esLicencia($codeReal);
                $esIncapacidad = Inc::esIncapacidad($codeReal);
                $esFalta = ($codeReal === Inc::FALTA);

                $reglasEspecificas = [
                    new TXTRule($this->helpers),
                    new LicenciaConGoceRule($this->helpers),
                    new IncapacidadRule($this->helpers),
                    new VacacionesRule(), // No usa helpers actualmente
                    new PaseSalidaRule(), // No usa helpers actualmente
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

            // --- LOG EN TIEMPO REAL (AL VUELO) ---
            try {
                $totalDiasFinal = DB::table('incidencias')->where('token', $token)->whereNull('deleted_at')->sum('total_dias');

                // Resolver Qnas involucradas
                $qnasJoined = DB::table('incidencias')
                    ->join('qnas', 'incidencias.qna_id', '=', 'qnas.id')
                    ->where('incidencias.token', $token)
                    ->whereNull('incidencias.deleted_at')
                    ->select('qnas.qna', 'qnas.year')
                    ->distinct()
                    ->get()
                    ->map(fn($q) => "Q{$q->qna}/" . substr($q->year, -2))
                    ->implode(', ');

                if (empty($qnasJoined))
                    $qnasJoined = 'Pendiente';

                $periodoTxt = 'N/A';
                if (!empty($data['periodo_id'])) {
                    $p = \App\Models\Periodo::find($data['periodo_id']);
                    if ($p)
                        $periodoTxt = "P{$p->periodo}/" . substr($p->year, -2);
                }

                // Payload para el broadcast (sin guardar en tabla de logs separada)
                $broadcastPayload = [
                    'employee_name' => $empleado->full_name,
                    'type' => $incidenciaCodigo->code,
                    'user_name' => auth()->user()->name,
                    'details' => [
                        'fecha_inicio' => $inicio,
                        'fecha_final' => $fin,
                        'total_dias' => (int)$totalDiasFinal,
                        'qnas' => $qnasJoined,
                        'periodo' => $periodoTxt
                    ],
                    'created_at' => now()->toDateTimeString()
                ];

                \Illuminate\Support\Facades\Log::info("BROADCASTING BATCH:", $broadcastPayload);
                broadcast(new \App\Events\NewIncidenciaBatchCreated($broadcastPayload));
            }
            catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Real-time Log Broadcast Error: " . $e->getMessage());
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
        if ($token)
            return DB::table('incidencias')->where('token', $token)->update(['deleted_at' => Carbon::now()]);
    }
}