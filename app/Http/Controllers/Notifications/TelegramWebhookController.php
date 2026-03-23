<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employe;
use App\Models\Checada;
use App\Events\ChecadaCreated;
use App\Services\Notifications\TelegramService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramService $telegram)
    {
        $update = $request->all();
        
        if (isset($update['callback_query'])) {
            return $this->handleCallbackQuery($update['callback_query'], $telegram);
        }

        if (!isset($update['message'])) {
            return response()->json(['status' => 'ok']);
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $adminIds = explode(',', env('TELEGRAM_ADMIN_IDS', '1502287926'));

        $state = Cache::get('tg_state_' . $chatId);
        if ($state && isset($state['action'])) {
            return $this->handleStateFlow($chatId, $text, $state, $telegram);
        }

        if (str_starts_with($text, '/admin')) {
            if (!in_array((string)$chatId, $adminIds)) {
                $telegram->sendMessage((string)$chatId, "🚫 Sin permisos de administrador.");
                return response()->json(['status' => 'ok']);
            }

            $telegram->sendMessage((string)$chatId, "🛠 **Admin Bot**\n\n¿Qué deseas hacer?", [
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => '📝 Registrar Asistencia Manual', 'callback_data' => 'admin_register_start']],
                    ]
                ])
            ]);
            return response()->json(['status' => 'ok']);
        }

        if (str_starts_with($text, '/start')) {
            return $this->handleStart($chatId, $text, $telegram);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleStart($chatId, $text, $telegram)
    {
        $parts = explode(' ', $text);
        $alreadyLinked = Employe::where('telegram_chat_id', $chatId)->first();

        if (count($parts) > 1) {
            $token = $parts[1];
            $employee = Employe::where('telegram_link_token', $token)->first();

            if ($employee) {
                $employee->update(['telegram_chat_id' => $chatId, 'telegram_link_token' => null]);
                $telegram->sendMessage((string)$chatId, "¡Hola {$employee->name}! ✅ Vincualción exitosa.");
            } else {
                $telegram->sendMessage((string)$chatId, "❌ Código inválido.");
            }
        } else {
            $msg = $alreadyLinked ? "👋 ¡Hola {$alreadyLinked->name}! Cuenta activa." : "👋 Para recibir avisos, vincúlate en el portal.";
            $telegram->sendMessage((string)$chatId, $msg);
        }
        return response()->json(['status' => 'ok']);
    }

    private function handleCallbackQuery($callback, $telegram)
    {
        $chatId = $callback['message']['chat']['id'];
        $data = $callback['data'];

        if ($data === 'admin_register_start') {
            Cache::put('tg_state_' . $chatId, ['action' => 'waiting_num_empleado'], 300);
            $telegram->sendMessage((string)$chatId, "🔢 Escribe el **Número de Empleado**:");
        }

        if (str_starts_with($data, 'time_select_')) {
            $parts = explode('|', $data); // time_select_PRESET|NUM
            $preset = $parts[0];
            $num = $parts[1];
            
            $timestamp = now();
            if ($preset === 'time_select_m5') $timestamp->subMinutes(5);
            if ($preset === 'time_select_m15') $timestamp->subMinutes(15);
            if ($preset === 'time_select_m30') $timestamp->subMinutes(30);
            
            if ($preset === 'time_select_custom') {
                Cache::put('tg_state_' . $chatId, ['action' => 'waiting_custom_time', 'num' => $num], 300);
                $telegram->sendMessage((string)$chatId, "🕒 Escribe la hora en formato **HH:mm** (ej: 08:30):");
                return response()->json(['status' => 'ok']);
            }

            $this->executeManualPunch($chatId, $num, $timestamp, $telegram);
        }

        if ($data === 'admin_cancel') {
            Cache::forget('tg_state_' . $chatId);
            $telegram->sendMessage((string)$chatId, "🚫 Operación cancelada.");
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleStateFlow($chatId, $text, $state, $telegram)
    {
        if ($state['action'] === 'waiting_num_empleado') {
            $num = trim($text);
            $employee = Employe::where('num_empleado', $num)->first();

            if (!$employee) {
                $telegram->sendMessage((string)$chatId, "❌ No encontré al empleado **{$num}**. Intenta de nuevo:");
                return response()->json(['status' => 'ok']);
            }

            $telegram->sendMessage((string)$chatId, "👤 **Emplado**: {$employee->full_name}\n\n¿Con qué hora registramos?", [
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => '✅ Ahora', 'callback_data' => "time_select_now|{$num}"]],
                        [['text' => '⏳ -5 min', 'callback_data' => "time_select_m5|{$num}"], ['text' => '⏳ -15 min', 'callback_data' => "time_select_m15|{$num}"]],
                        [['text' => '⏳ -30 min', 'callback_data' => "time_select_m30|{$num}"], ['text' => '🕒 Personalizada', 'callback_data' => "time_select_custom|{$num}"]],
                        [['text' => '❌ Cancelar', 'callback_data' => 'admin_cancel']],
                    ]
                ])
            ]);
            Cache::forget('tg_state_' . $chatId); // Transición a botones
        }

        if ($state['action'] === 'waiting_custom_time') {
            $num = $state['num'];
            try {
                $time = Carbon::createFromFormat('H:i', trim($text));
                $timestamp = now()->setTime($time->hour, $time->minute);
                $this->executeManualPunch($chatId, $num, $timestamp, $telegram);
            } catch (\Exception $e) {
                $telegram->sendMessage((string)$chatId, "❌ Formato inválido. Escribe la hora como **HH:mm** (ej: 14:45):");
            }
        }
        
        return response()->json(['status' => 'ok']);
    }

    private function executeManualPunch($chatId, $num, $timestamp, $telegram)
    {
        Cache::forget('tg_state_' . $chatId);
        $location = "ADMIN_TELEGRAM";
        $identificador = "{$num}_" . $timestamp->format('YmdHi') . "_ADMIN_TELEGRAM";

        try {
            $checada = Checada::create([
                'num_empleado' => $num,
                'fecha' => $timestamp->format('Y-m-d H:i:s'),
                'identificador' => $identificador,
            ]);

            if ($checada) {
                event(new ChecadaCreated($checada, $location));
                $telegram->sendMessage((string)$chatId, "✅ Checada registrada: **{$num}** a las " . $timestamp->format('g:i A'));
            } else {
                $telegram->sendMessage((string)$chatId, "❌ Error de BD.");
            }
        } catch (\Exception $e) {
            $telegram->sendMessage((string)$chatId, "❌ Error: Registro ya existe en ese minuto.");
        }
    }
}
