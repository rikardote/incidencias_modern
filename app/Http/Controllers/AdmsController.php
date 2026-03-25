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

        // Solo permitir equipos que ya estén registrados en la base de datos (evita suplantación)
        $equipoExists = DB::connection(app()->environment('testing') ? config('database.default') : 'biometrico')
            ->table('equipos')
            ->where('serial_number', $sn)
            ->exists();

        if (!$equipoExists) {
            Log::channel('daily')->warning("[ADMS] Intento de handshake de equipo DESCONOCIDO. SN: {$sn}, IP: " . $request->ip());
            return response("ERROR: Device not registered", 401);
        }

        // Registrar o actualizar el equipo
        $this->registrarEquipo($sn, $request->ip());

        // Hora local de Tijuana para sincronizar el equipo
        $localTime = now()->timezone('America/Tijuana')->format('Y-m-d H:i:s');

        // Responder con configuraciones para el equipo
        $config = [
            'GET OPTION FROM: ' . $sn,
            'ATTLOGStamp=0',
            'OPERLOGStamp=0',
            'ATTPHOTOStamp=0',
            'ErrorDelay=60',
            'Delay=5',
            'TransTimes=00:00;14:05',
            'TransInterval=1',
            'TransFlag=TransData AttLog\tOpLog\tEnrollUser\tChgUser\tEnrollFP\tChgFP\tFACE',
            'Realtime=1',
            'Encrypt=0',
            'DuplicateCheck=0',
            'TimeZone=' . (now()->timezone('America/Tijuana')->getOffset() / 60),
            'ServerTime=' . now()->timezone('America/Tijuana')->format('Y-m-d H:i:s'),

        ];

        Log::channel('daily')->info("[ADMS] Enviando handshake con ServerTime (Tijuana REAL) a SN: {$sn}");

        return response(implode("\n", $config), 200)
            ->header('Content-Type', 'text/plain')
            ->header('Date', now()->timezone('UTC')->format('D, d M Y H:i:s') . ' GMT');
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
        
        // Verificar que el equipo exista
        $connection = app()->environment('testing') ? config('database.default') : 'biometrico';
        $equipoExists = DB::connection($connection)->table('equipos')->where('serial_number', $sn)->exists();
        if (!$equipoExists) {
            return response("ERROR: Device not registered", 401);
        }

        $table = $request->query('table', '');
        $rawData = $request->getContent();

        Log::channel('daily')->info("[ADMS] Datos recibidos de SN: {$sn}, tabla: {$table}", [
            'ip' => $request->ip(),
            'content_length' => strlen($rawData),
        ]);

        $response = 'OK';
        try {
            switch (strtoupper($table)) {
                case 'ATTLOG':
                    $count = $this->procesarAttLog($rawData, $sn);
                    Log::channel('daily')->info("[ADMS] Procesados {$count} registros ATTLOG de SN: {$sn}");
                    $response = "OK: {$count}";
                    break;

                case 'OPERLOG':
                    $count = $this->procesarOperLog($rawData, $sn);
                    Log::channel('daily')->info("[ADMS] Procesado OPERLOG de SN: {$sn}");
                    $response = "OK: {$count}";
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

        // Responder OK:count para que el equipo sepa qué registros se procesaron
        return response($response, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * POST /iclock/devicecmd
     * 
     * Recepción de confirmaciones de comandos ejecutados en el equipo.
     */
    public function receiveCommand(Request $request)
    {
        $sn = $request->query('SN', 'unknown');
        $rawData = $request->getContent();

        Log::channel('daily')->info("[ADMS] Confirmación de comando recibida de SN: {$sn}", [
            'data' => substr($rawData, 0, 500)
        ]);

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

        // Verificar que el equipo exista
        $connection = app()->environment('testing') ? config('database.default') : 'biometrico';
        $equipoExists = DB::connection($connection)->table('equipos')->where('serial_number', $sn)->exists();
        if (!$equipoExists) {
            return response("ERROR: Device not registered", 401);
        }

        // Actualizar último contacto del equipo
        $this->actualizarUltimoContacto($sn);

        // OPTIMIZACIÓN: Si el equipo ya nos contactó hace poco, no le mandamos comandos de config
        // Esto evita el bucle infinito de heartbeats que satura el servidor.
        $lastSeen = DB::connection('biometrico')->table('equipos')->where('serial_number', $sn)->value('last_seen');
        
        // Si el equipo es nuevo o no ha sido configurado en este minuto, le mandamos sus parámetros
        if (!$lastSeen || strtotime($lastSeen) < strtotime('-1 minute')) {
            Log::channel('daily')->info("[ADMS] Enviando configuración periódica a SN: {$sn}");
            $commands = [
                "C:101:SET OPTION DuplicateCheck=0",
                "C:102:SET OPTION TimeZone=" . (now()->timezone('America/Tijuana')->getOffset() / 60),
                "C:103:SET OPTION ServerTime=" . now()->timezone('America/Tijuana')->format('Y-m-d H:i:s'),
            ];
            $response = implode("\n", $commands);
        } else {
            // Heartbeat normal (sin comandos pendientes)
            // Respondemos OK para que el equipo espere el tiempo de Delay configurado
            $response = 'OK';
        }

        return response($response, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Date', now()->timezone('UTC')->format('D, d M Y H:i:s') . ' GMT');
    }

    /**
     * Procesa registros de asistencia (ATTLOG)
     * 
     * Cada línea tiene formato tab-separated:
     * PIN\tFecha\tStatus\tVerifyMode\t\tWorkCode\tReserved
     */
    private function procesarAttLog(string $rawData, string $sn): int
    {
        // Usar regex para separar líneas (maneja \r\n, \n y \r que envían algunos equipos)
        $lines = preg_split('/\r\n|\r|\n/', trim($rawData));
        $lines = array_filter($lines);
        
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

            // Protección inteligente de hora:
            // Muchos equipos ZKTeco con ADMS se desfasan +16h solos.
            // Si detectamos que el registro viene con más de 2 horas al futuro,
            // le restamos las 16h para normalizarlo. 
            // Si el reloj está bien (manual), no hacemos nada.
            $timestamp = strtotime($fecha);
            if ($timestamp === false) {
                continue;
            }

            // El usuario confirmó que los equipos ya tienen la hora correcta.
            // No aplicamos correcciones automáticas de desfase a menos que sea necesario.
            $timestampCorregido = $timestamp;

            
            $fechaCorregida = date('Y-m-d H:i:s', $timestampCorregido);
            $identificador = "{$pin}_" . date('YmdHi', $timestampCorregido) . "_{$locationSlug}";

            $insertRows[] = [
                'num_empleado' => $pin,
                'fecha' => $fechaCorregida,
                'identificador' => $identificador,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $registrosNuevos[] = [
                'identificador' => $identificador,
                'pin' => $pin,
                'fecha' => $fechaCorregida,
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
            // Deduplicar registros nuevos por identificador para no disparar eventos dobles
            $identificadoresProcesados = [];
            $recientes = array_slice($registrosNuevos, -20); // Tomamos un margen mayor para deduplicar
            $disparados = 0;

            foreach (array_reverse($recientes) as $registro) {
                if ($disparados >= 10) break;
                
                $id = $registro['identificador'];
                if (isset($identificadoresProcesados[$id])) continue;
                $identificadoresProcesados[$id] = true;

                try {
                    $checada = Checada::where('identificador', $id)->first();
                    if ($checada) {
                        event(new ChecadaCreated($checada, $location));
                        $disparados++;
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
    private function procesarOperLog(string $rawData, string $sn): int
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($rawData));
        $lines = array_filter($lines);
        $count = 0;

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
                $count++;
            }
        }

        return $count;
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
