<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Checada;
use Illuminate\Support\Facades\Log;
use App\Events\ChecadaCreated;

class WearOSController extends Controller
{
    public function storeCheckin(Request $request)
    {
        // Validación de seguridad simple usando un secret key estático.
        // Se recomienda definir WEAROS_API_KEY en tu archivo .env
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');
        if ($apiKey !== config('services.wearos.api_key')) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $validated = $request->validate([
            'fecha' => 'required|date_format:Y-m-d H:i:s',
            'num_empleado' => 'nullable|numeric',
            'identificador' => 'nullable|string'
        ]);

        try {
            // Generar identificador único para la checada
            $baseId = !empty($validated['identificador']) ? $validated['identificador'] : 'WOS';
            $uniqueId = $baseId . '_' . uniqid() . '_' . time();

            // num_empleado TEMPORALMENTE hardcodeado a 332618 por solicitud del usuario
            // hasta que se actualice la app de WearOS.
            $originalId = $validated['num_empleado'] ?? $validated['identificador'] ?? 'N/A';
            $numEmpleado = '332618';

            // Verificar si el empleado default existe
            $employee = \App\Models\Employe::where('num_empleado', (int)$numEmpleado)->first();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => "Empleado maestro {$numEmpleado} no encontrado"
                ], 404);
            }
            
            $checada = Checada::create([
                'num_empleado' => $numEmpleado,
                'fecha' => $validated['fecha'],
                'identificador' => $uniqueId,
            ]);

            Log::channel('daily')->info("[WearOS] Registro forzado para 332618. ID original enviado: {$originalId}");

            // Disparar evento para enviar notificaciones Push/Telegram
            event(new ChecadaCreated($checada, 'App WearOS'));

            return response()->json([
                'success' => true,
                'message' => 'Checada registrada exitosamente',
                'data' => [
                    'num_empleado' => $checada->num_empleado,
                    'fecha' => $checada->fecha,
                ]
            ], 201);
            
        } catch (\Exception $e) {
            Log::error("WearOS Checkin Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al registrar la checada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(Request $request, $identificador)
    {
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');
        if ($apiKey !== config('services.wearos.api_key')) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $checadas = Checada::where('num_empleado', $identificador)
            ->orWhere('identificador', 'LIKE', $identificador . '%')
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'check_ins' => $checadas->map(function($c) {
                return [
                    'id' => $c->id,
                    'employee_id' => $c->num_empleado,
                    'employee_name' => 'Usuario', // Simplificado
                    'type' => 'CHECK', // El modelo Checada no parece tener un tipo Entrada/Salida explícito en este controlador
                    'timestamp' => strtotime($c->fecha) * 1000,
                    'fecha' => $c->fecha,
                    'identificador' => $c->identificador
                ];
            })
        ]);
    }
}
