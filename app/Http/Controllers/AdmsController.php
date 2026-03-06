<?php

namespace App\Http\Controllers;

use App\Events\ChecadaCreated;
use App\Models\Checada;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ADMS Controller — Servidor ADMS para dispositivos ZKTeco.
 * 
 * Los equipos ZKTeco con ADMS habilitado hacen peticiones HTTP 
 * a este servidor para enviar checadas en tiempo real (push).
 * 
 * Endpoints:
 *   GET  /iclock/cdata          → Handshake inicial del equipo
 *   POST /iclock/cdata          → Recepción de ATTLOG (checadas) y OPERLOG (usuarios)
 *   GET  /iclock/getrequest     → Heartbeat / comandos pendientes
 * 
 * Configuración en el equipo MB360:
 *   Comm. → Cloud Server Setting → Habilitar
 *   Server Address: IP_DE_TU_SERVIDOR
 *   Server Port: 8190 (o el puerto de tu nginx)
 */
class AdmsController extends Controller
{
    /**
     * GET /iclock/cdata
     * 
     * Handshake inicial del equipo cuando se conecta por primera vez.
     * El equipo envía su número de serie y espera la configuración.
     */
    public function handshake(Request $request)
    {
        $sn = $request->query('SN', 'unknown');

        Log::channel('daily')->info("[ADMS] Handshake recibido de equipo SN: {$sn}", [
            'ip' => $request->ip(),
            'query' => $request->query(),
        ]);

        // Registrar o actualizar el equipo
        $this->registrarEquipo($sn, $request->ip());

        // Responder con configuraciones para el equipo
        // Estos parámetros le dicen al equipo cada cuánto enviar datos
        $config = [
            'GET OPTION FROM: ' . $sn,
            'ATTLOGStamp=0',      // Enviar todos los registros de asistencia
            'OPERLOGStamp=0',     // Enviar todos los registros de operación
            'ATTPHOTOStamp=0',    // Enviar fotos de asistencia
            'ErrorDelay=60',      // Reintentar cada 60s si hay error
            'Delay=5',            // Intervalo de ping cada 5s
            'TransTimes=00:00;14:05', // Horarios de transferencia
            'TransInterval=1',    // Intervalo de transferencia en minutos
            'TransFlag=TransData AttLog\tOpLog\tEnrollUser\tChgUser\tEnrollFP\tChgFP\tFACE',
            'Realtime=1',         // Habilitar envío en tiempo real
            'Encrypt=0',          // Sin encriptación
        ];

        return response(implode("\n", $config), 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * POST /iclock/cdata
     * 
     * Recepción de datos del equipo: ATTLOG (checadas) u OPERLOG (operaciones).
     * 
     * Formato ATTLOG (tab-separated):
     *   PIN\tFecha\tStatus\tVerifyMode\t\tWorkCode\tReserved
     *   1234\t2026-03-05 08:30:00\t0\t1\t\t0\t0
     * 
     * Formato OPERLOG (key=value tab-separated):
     *   OPLOG PIN=2\tName=Juan Perez\tPri=0\t...
     */
    public function receiveData(Request $request)
    {
        $sn = $request->query('SN', 'unknown');
        $table = $request->query('table', '');
        $rawData = $request->getContent();

        Log::channel('daily')->info("[ADMS] Datos recibidos de SN: {$sn}, tabla: {$table}", [
            'ip' => $request->ip(),
            'content_length' => strlen($rawData),
        ]);

        try {
            switch (strtoupper($table)) {
                case 'ATTLOG':
                    $count = $this->procesarAttLog($rawData, $sn);
                    Log::channel('daily')->info("[ADMS] Procesados {$count} registros ATTLOG de SN: {$sn}");
                    break;

                case 'OPERLOG':
                    $this->procesarOperLog($rawData, $sn);
                    Log::channel('daily')->info("[ADMS] Procesado OPERLOG de SN: {$sn}");
                    break;

                default:
                    Log::channel('daily')->warning("[ADMS] Tabla desconocida: {$table} de SN: {$sn}", [
                        'data' => substr($rawData, 0, 500),
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error("[ADMS] Error procesando {$table} de SN: {$sn}: " . $e->getMessage());
        }

        // Siempre responder OK para que el equipo no reenvíe
        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * GET /iclock/getrequest
     * 
     * Heartbeat del equipo — lo envía periódicamente.
     * Aquí podemos responder con comandos para el equipo.
     */
    public function getRequest(Request $request)
    {
        $sn = $request->query('SN', 'unknown');

        // Actualizar último contacto del equipo
        $this->actualizarUltimoContacto($sn);

        // Responder OK (o enviar comandos si los hay)
        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Procesa registros de asistencia (ATTLOG)
     * 
     * Cada línea tiene formato tab-separated:
     * PIN\tFecha\tStatus\tVerifyMode\t\tWorkCode\tReserved
     */
    private function procesarAttLog(string $rawData, string $sn): int
    {
        $lines = array_filter(explode("\n", trim($rawData)));
        $location = $this->obtenerLocationPorSN($sn);
        $connection = app()->environment('testing') ? config('database.default') : 'biometrico';
        $locationSlug = str_replace(' ', '', $location);

        $registrosNuevos = [];
        $insertRows = [];

        foreach ($lines as $line) {
            $parts = explode("\t", trim($line));

            if (count($parts) < 2) {
                continue;
            }

            $pin = trim($parts[0]);           // Número de empleado
            $fecha = trim($parts[1]);          // 2026-03-05 08:30:00
            $status = isset($parts[2]) ? trim($parts[2]) : '0';      // 0=entrada, 1=salida
            $verifyMode = isset($parts[3]) ? trim($parts[3]) : '0';  // 0=password, 1=huella, 2=tarjeta

            if (empty($pin) || $pin === '0') {
                continue;
            }

            $timestamp = strtotime($fecha);
            if ($timestamp === false || $timestamp > strtotime('+1 day')) {
                continue;
            }

            $identificador = "{$pin}_" . date('YmdHi', $timestamp) . "_{$locationSlug}";

            $insertRows[] = [
                'num_empleado' => $pin,
                'fecha' => $fecha,
                'identificador' => $identificador,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $registrosNuevos[] = [
                'identificador' => $identificador,
                'pin' => $pin,
                'fecha' => $fecha,
                'status' => $status,
                'verify_mode' => $verifyMode,
            ];
        }

        if (empty($insertRows)) {
            return 0;
        }

        // Insertar en batch, ignorar duplicados gracias al UNIQUE index
        $totalInsertados = 0;
        foreach (array_chunk($insertRows, 500) as $chunk) {
            $totalInsertados += DB::connection($connection)
                ->table('checadas')
                ->insertOrIgnore($chunk);
        }

        // Disparar eventos para los registros realmente nuevos (máximo 10)
        if ($totalInsertados > 0) {
            $recientes = array_slice($registrosNuevos, -10);
            foreach ($recientes as $registro) {
                try {
                    $checada = Checada::where('identificador', $registro['identificador'])->first();
                    if ($checada) {
                        event(new ChecadaCreated($checada, $location));
                    }
                } catch (\Exception $e) {
                    Log::channel('daily')->error("[ADMS] Error disparando evento: " . $e->getMessage());
                }
            }
        }

        return $totalInsertados;
    }

    /**
     * Procesa registros de operación (OPERLOG)
     * 
     * Contiene información de usuarios, cambios de configuración, etc.
     * Por ahora solo lo loggeamos para referencia.
     */
    private function procesarOperLog(string $rawData, string $sn): void
    {
        $lines = array_filter(explode("\n", trim($rawData)));

        foreach ($lines as $line) {
            // Parsear key=value pairs separados por tab
            $fields = [];
            $parts = explode("\t", trim($line));
            foreach ($parts as $part) {
                if (str_contains($part, '=')) {
                    [$key, $value] = explode('=', $part, 2);
                    $fields[trim($key)] = trim($value);
                }
            }

            if (!empty($fields)) {
                Log::channel('daily')->info("[ADMS] OPERLOG de SN {$sn}", $fields);

                // Futuro: Aquí podrías sincronizar usuarios automáticamente
                // if (isset($fields['PIN']) && isset($fields['Name'])) {
                //     $this->sincronizarUsuario($fields);
                // }
            }
        }
    }

    /**
     * Registra o actualiza un equipo basado en su número de serie.
     */
    private function registrarEquipo(string $sn, string $ip): void
    {
        try {
            $connection = app()->environment('testing') ? config('database.default') : 'biometrico';

            DB::connection($connection)->table('equipos')->updateOrInsert(
                ['serial_number' => $sn],
                [
                    'ip' => $ip,
                    'last_seen' => now(),
                    'updated_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            // Si la columna serial_number no existe aún, solo loggeamos
            Log::channel('daily')->debug("[ADMS] No se pudo registrar equipo SN {$sn}: " . $e->getMessage());
        }
    }

    /**
     * Actualiza la marca de último contacto del equipo.
     */
    private function actualizarUltimoContacto(string $sn): void
    {
        try {
            $connection = app()->environment('testing') ? config('database.default') : 'biometrico';

            DB::connection($connection)->table('equipos')
                ->where('serial_number', $sn)
                ->update(['last_seen' => now(), 'updated_at' => now()]);
        } catch (\Exception $e) {
            // Silencioso si falla
        }
    }

    /**
     * Obtiene la ubicación del equipo por su número de serie.
     * Si no se encuentra, retorna un fallback con el SN.
     */
    private function obtenerLocationPorSN(string $sn): string
    {
        try {
            $connection = app()->environment('testing') ? config('database.default') : 'biometrico';

            $equipo = DB::connection($connection)->table('equipos')
                ->where('serial_number', $sn)
                ->first();

            return $equipo->location ?? "ADMS_{$sn}";
        } catch (\Exception $e) {
            return "ADMS_{$sn}";
        }
    }
}
