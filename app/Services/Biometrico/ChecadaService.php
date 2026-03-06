<?php

namespace App\Services\Biometrico;

use App\Events\ChecadaCreated;
use App\Models\Checada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChecadaService
{
    /**
     * Procesa un array de registros de asistencia del biométrico.
     *
     * Optimizaciones implementadas:
     * 1. Pre-carga identificadores existentes en memoria (evita N+1 queries)
     * 2. Usa insertOrIgnore en batch (500 registros por query)
     * 3. Solo dispara eventos para registros realmente nuevos
     *
     * @param array $registros  Array de registros del biométrico [{id, timestamp}, ...]
     * @param string $location  Ubicación del dispositivo
     * @param bool $broadcastEvents  Si debe disparar eventos de broadcast (default: true)
     * @return int Número de registros nuevos insertados
     */
    public function procesarRegistros(array $registros, string $location, bool $broadcastEvents = true): int
    {
        if (empty($registros)) {
            return 0;
        }

        $locationSlug = str_replace(' ', '', $location);
        $ahora = strtotime('+1 day');

        // Paso 1: Preparar registros válidos y sus identificadores
        $registrosValidos = [];
        foreach ($registros as $registro) {
            $timestamp = strtotime($registro['timestamp']);
            $numEmpleado = (string) $registro['id'];

            // Filtrar registros inválidos
            if (empty($numEmpleado) || $numEmpleado === '0') {
                continue;
            }
            if ($timestamp > $ahora) {
                continue;
            }

            $identificador = "{$numEmpleado}_" . date('YmdHi', $timestamp) . "_{$locationSlug}";

            $registrosValidos[] = [
                'num_empleado' => $numEmpleado,
                'fecha' => date('Y-m-d H:i:s', $timestamp),
                'identificador' => $identificador,
            ];
        }

        if (empty($registrosValidos)) {
            return 0;
        }

        // Paso 2: Pre-cargar identificadores existentes en memoria
        // Solo consultamos los que nos interesan, no toda la tabla
        $identificadoresCandidatos = array_column($registrosValidos, 'identificador');
        $existentes = $this->obtenerIdentificadoresExistentes($identificadoresCandidatos);

        // Paso 3: Filtrar solo los registros nuevos
        $registrosNuevos = array_filter($registrosValidos, function ($registro) use ($existentes) {
            return !isset($existentes[$registro['identificador']]);
        });

        if (empty($registrosNuevos)) {
            return 0;
        }

        // Paso 4: Insertar en batch usando insertOrIgnore
        // El UNIQUE index en 'identificador' protege contra duplicados
        $totalInsertados = 0;
        $connection = app()->environment('testing') ? config('database.default') : 'biometrico';

        foreach (array_chunk($registrosNuevos, 500) as $chunk) {
            $rows = array_map(function ($registro) {
                return [
                    'num_empleado' => $registro['num_empleado'],
                    'fecha' => $registro['fecha'],
                    'identificador' => $registro['identificador'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $chunk);

            $insertados = DB::connection($connection)
                ->table('checadas')
                ->insertOrIgnore($rows);

            $totalInsertados += $insertados;
        }

        // Paso 5: Disparar eventos solo para registros nuevos
        if ($broadcastEvents && $totalInsertados > 0) {
            $this->dispararEventos(array_values($registrosNuevos), $location);
        }

        return $totalInsertados;
    }

    /**
     * Procesa un solo registro en tiempo real (para el daemon de monitoreo).
     * 
     * Usa insertOrIgnore para la protección contra duplicados a nivel DB,
     * sin necesidad de hacer un SELECT previo.
     *
     * @param array $record  Registro del biométrico {id, timestamp}
     * @param string $location  Ubicación del dispositivo
     * @return Checada|null  La checada creada, o null si ya existía
     */
    public function procesarRegistroIndividual(array $record, string $location): ?Checada
    {
        $timestamp = strtotime($record['timestamp']);
        $numEmpleado = (string) $record['id'];

        if (empty($numEmpleado) || $numEmpleado === '0') {
            return null;
        }

        if ($timestamp > strtotime('+1 day')) {
            return null;
        }

        $locationSlug = str_replace(' ', '', $location);
        $identificador = "{$numEmpleado}_" . date('YmdHi', $timestamp) . "_{$locationSlug}";
        $fecha = date('Y-m-d H:i:s', $timestamp);

        $connection = app()->environment('testing') ? config('database.default') : 'biometrico';

        // insertOrIgnore retorna 1 si insertó, 0 si ya existía (gracias al UNIQUE index)
        $insertado = DB::connection($connection)
            ->table('checadas')
            ->insertOrIgnore([
                'num_empleado' => $numEmpleado,
                'fecha' => $fecha,
                'identificador' => $identificador,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        if ($insertado > 0) {
            // Obtener el modelo para el evento
            $checada = Checada::where('identificador', $identificador)->first();

            if ($checada) {
                try {
                    event(new ChecadaCreated($checada, $location));
                } catch (\Exception $e) {
                    Log::error("Error al disparar evento ChecadaCreated: " . $e->getMessage());
                }
            }

            return $checada;
        }

        return null;
    }

    /**
     * Pre-carga los identificadores existentes en la base de datos.
     * Retorna un array asociativo para búsquedas en O(1).
     */
    private function obtenerIdentificadoresExistentes(array $identificadores): array
    {
        if (empty($identificadores)) {
            return [];
        }

        $connection = app()->environment('testing') ? config('database.default') : 'biometrico';

        $existentes = [];
        // Consultar en bloques para no exceder el límite de parámetros
        foreach (array_chunk($identificadores, 1000) as $chunk) {
            $found = DB::connection($connection)
                ->table('checadas')
                ->whereIn('identificador', $chunk)
                ->pluck('identificador');

            foreach ($found as $id) {
                $existentes[$id] = true;
            }
        }

        return $existentes;
    }

    /**
     * Dispara eventos de broadcast para los registros nuevos.
     * Limita a los últimos 10 registros para no saturar el canal.
     */
    private function dispararEventos(array $registrosNuevos, string $location): void
    {
        // Solo disparar eventos de los últimos registros para no saturar broadcasts
        $registrosRecientes = array_slice($registrosNuevos, -10);

        foreach ($registrosRecientes as $registro) {
            try {
                $checada = Checada::where('identificador', $registro['identificador'])->first();
                if ($checada) {
                    event(new ChecadaCreated($checada, $location));
                }
            } catch (\Exception $e) {
                Log::error("Error al disparar evento ChecadaCreated: " . $e->getMessage());
            }
        }
    }
}
