<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employe;
use App\Models\Checada;
use App\Models\Incidencia;
use App\Models\Periodo;
use App\Constants\Incidencias as Inc;
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
        $adminIds = explode(',', config('services.telegram.admin_ids', '1502287926'));

        $state = Cache::get('tg_state_' . $chatId);
        if ($state && isset($state['action'])) {
            return $this->handleStateFlow($chatId, $text, $state, $telegram);
        }

        // --- COMMANDS ---

        if (str_starts_with($text, '/admin')) {
             return $this->handleAdminMenu($chatId, $adminIds, $telegram);
        }

        if (str_starts_with($text, '/vacaciones') || str_starts_with($text, '/vaca')) {
            return $this->handleVacationCommand($chatId, $text, $adminIds, $telegram);
        }

        if (str_starts_with($text, '/checadas') || str_starts_with($text, '/checs')) {
            return $this->handleAttendanceCommand($chatId, $text, $adminIds, $telegram);
        }

        if (str_starts_with($text, '/start')) {
            return $this->handleStart($chatId, $text, $telegram);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleAdminMenu($chatId, $adminIds, $telegram)
    {
        if (!in_array((string)$chatId, $adminIds)) {
            $telegram->sendMessage((string)$chatId, "🚫 Sin permisos de administrador.");
            return response()->json(['status' => 'ok']);
        }

        $telegram->sendMessage((string)$chatId, "🛠 **Admin Bot**\n\n¿Qué deseas hacer?", [
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => '📝 Registrar Asistencia Manual', 'callback_data' => 'admin_register_start']],
                    [['text' => '🌴 Consultar Vacaciones', 'callback_data' => 'admin_vaca_start']],
                    [['text' => '🕒 Ver Checadas Quincena', 'callback_data' => 'admin_checs_start']],
                ]
            ])
        ]);
        return response()->json(['status' => 'ok']);
    }

    private function handleAttendanceCommand($chatId, $text, $adminIds, $telegram)
    {
        if (!in_array((string)$chatId, $adminIds)) {
            $telegram->sendMessage((string)$chatId, "🚫 Comando restringido.");
            return response()->json(['status' => 'ok']);
        }

        $parts = explode(' ', $text);
        if (count($parts) > 1) {
            $num = trim($parts[1]);
            $this->getAttendanceReport($num, $chatId, $telegram);
        } else {
            Cache::put('tg_state_' . $chatId, ['action' => 'waiting_num_checadas'], 300);
            $telegram->sendMessage((string)$chatId, "🔢 Escribe el **Número de Empleado** para ver su quincena:");
        }
        return response()->json(['status' => 'ok']);
    }

    private function handleVacationCommand($chatId, $text, $adminIds, $telegram)
    {
        if (!in_array((string)$chatId, $adminIds)) {
            $telegram->sendMessage((string)$chatId, "🚫 Comando restringido.");
            return response()->json(['status' => 'ok']);
        }

        $parts = explode(' ', $text);
        if (count($parts) > 1) {
            $num = trim($parts[1]);
            $this->getVacationReport($num, $chatId, $telegram);
        } else {
            Cache::put('tg_state_' . $chatId, ['action' => 'waiting_num_vacaciones'], 300);
            $telegram->sendMessage((string)$chatId, "🌴 Escribe el **Número de Empleado** para consultar vacaciones:");
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
                $telegram->sendMessage((string)$chatId, "¡Hola {$employee->name}! ✅ Vinculación exitosa.");
            } else {
                $telegram->sendMessage((string)$chatId, "❌ Código inválido.");
            }
        } else {
            $msg = $alreadyLinked ? "👋 ¡Hola {$alreadyLinked->name}! Cuenta activa." : "👋 Vincúlate en el portal para recibir avisos.";
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

        if ($data === 'admin_vaca_start') {
            Cache::put('tg_state_' . $chatId, ['action' => 'waiting_num_vacaciones'], 300);
            $telegram->sendMessage((string)$chatId, "🌴 Escribe el **Número de Empleado**:");
        }

        if ($data === 'admin_checs_start') {
            Cache::put('tg_state_' . $chatId, ['action' => 'waiting_num_checadas'], 300);
            $telegram->sendMessage((string)$chatId, "🔢 Escribe el **Número de Empleado**:");
        }

        if (str_starts_with($data, 'time_select_')) {
            $parts = explode('|', $data);
            $preset = $parts[0];
            $num = $parts[1];
            
            $timestamp = now();
            if ($preset === 'time_select_m5') $timestamp->subMinutes(5);
            if ($preset === 'time_select_m15') $timestamp->subMinutes(15);
            if ($preset === 'time_select_m30') $timestamp->subMinutes(30);
            
            if ($preset === 'time_select_custom') {
                Cache::put('tg_state_' . $chatId, ['action' => 'waiting_custom_time', 'num' => $num], 300);
                $telegram->sendMessage((string)$chatId, "🕒 Escribe la hora (**HH:mm**):");
                return response()->json(['status' => 'ok']);
            }

            $this->executeManualPunch($chatId, $num, $timestamp, $telegram);
        }

        if ($data === 'admin_cancel') {
            Cache::forget('tg_state_' . $chatId);
            $telegram->sendMessage((string)$chatId, "🚫 Cancelado.");
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleStateFlow($chatId, $text, $state, $telegram)
    {
        if ($state['action'] === 'waiting_num_empleado') {
            $num = trim($text);
            $employee = Employe::where('num_empleado', $num)->first();

            if (!$employee) {
                $telegram->sendMessage((string)$chatId, "❌ Empleado **{$num}** no encontrado.");
                return response()->json(['status' => 'ok']);
            }

            $telegram->sendMessage((string)$chatId, "👤 **Empleado**: {$employee->full_name}\n\n¿Hora del registro?", [
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => '✅ Ahora', 'callback_data' => "time_select_now|{$num}"]],
                        [['text' => '⏳ -5 min', 'callback_data' => "time_select_m5|{$num}"], ['text' => '⏳ -15 min', 'callback_data' => "time_select_m15|{$num}"]],
                        [['text' => '⏳ -30 min', 'callback_data' => "time_select_m30|{$num}"], ['text' => '🕒 Personalizada', 'callback_data' => "time_select_custom|{$num}"]],
                        [['text' => '❌ Cancelar', 'callback_data' => 'admin_cancel']],
                    ]
                ])
            ]);
            Cache::forget('tg_state_' . $chatId);
        }

        if ($state['action'] === 'waiting_num_vacaciones') {
            $this->getVacationReport($text, $chatId, $telegram);
        }

        if ($state['action'] === 'waiting_num_checadas') {
            $this->getAttendanceReport($text, $chatId, $telegram);
        }

        if ($state['action'] === 'waiting_custom_time') {
            $num = $state['num'];
            try {
                $time = Carbon::createFromFormat('H:i', trim($text));
                $timestamp = now()->setTime($time->hour, $time->minute);
                $this->executeManualPunch($chatId, $num, $timestamp, $telegram);
            } catch (\Exception $e) {
                $telegram->sendMessage((string)$chatId, "❌ Invalido. Ejemplo (HH:mm): 14:45");
            }
        }
        
        return response()->json(['status' => 'ok']);
    }

    private function getAttendanceReport($num, $chatId, $telegram)
    {
        Cache::forget('tg_state_' . $chatId);
        $employee = Employe::where('num_empleado', trim($num))->first();

        if (!$employee) {
            $telegram->sendMessage((string)$chatId, "❌ No encontré al empleado **{$num}**.");
            return;
        }

        try {
            $qnaId = qna_year(now());
            if (!$qnaId) {
                 $telegram->sendMessage((string)$chatId, "⚠️ No hay una quincena activa actualmente.");
                 return;
            }
            $fechaInicio = getFechaInicioPorQna($qnaId);
            $fechaFin = getFechaFinalPorQna($fechaInicio);

            $registros = (new Checada)->obtenerRegistrosPorEmpleado($employee->id, $fechaInicio, $fechaFin);

            $rows = [];
            foreach ($registros as $r) {
                $fechaStr = Carbon::parse($r->fecha)->translatedFormat('D d/M');
                $entrada = $r->hora_entrada ? substr($r->hora_entrada, 0, 5) : "--:--";
                $salida = $r->hora_salida ? substr($r->hora_salida, 0, 5) : "--:--";
                
                $emoji = ($r->hora_entrada || $r->incidencias) ? "✅" : "❌";
                if ($r->retardo) $emoji = "⚠️";

                $rows[] = "{$emoji} **{$fechaStr}**: {$entrada} - {$salida}";
            }

            $msg = "🕒 **Asistencia Quincena Actual**\n";
            $msg .= "👤 {$employee->num_empleado} - {$employee->full_name}\n";
            $msg .= "--------------------------------\n";
            $msg .= implode("\n", $rows);

            $telegram->sendMessage((string)$chatId, $msg);
        } catch (\Exception $e) {
            $telegram->sendMessage((string)$chatId, "❌ Error al consultar checadas: " . $e->getMessage());
        }
    }

    private function getVacationReport($num, $chatId, $telegram)
    {
        Cache::forget('tg_state_' . $chatId);
        $employee = Employe::where('num_empleado', trim($num))->first();

        if (!$employee) {
            $telegram->sendMessage((string)$chatId, "❌ No encontré al empleado **{$num}**.");
            return;
        }

        // Determinar derecho según jornada
        $entitlement = 10; // Default
        if (in_array($employee->jornada_id, Inc::JORNADA_SYF_DYF)) $entitlement = 2;
        if (in_array($employee->jornada_id, Inc::JORNADA_VAC_5_DIAS)) $entitlement = 5;
        if (in_array($employee->jornada_id, Inc::JORNADA_VAC_6_DIAS)) $entitlement = 6;

        $currentYear = now()->year;
        $years = [$currentYear, $currentYear - 1, $currentYear - 2, $currentYear - 3, $currentYear - 4];
        
        $periods = Periodo::whereIn('year', $years)
            ->whereIn('periodo', ['01', '02'])
            ->orderBy('year', 'desc')
            ->orderBy('periodo', 'desc')
            ->get();

        $rows = [];
        $totalPending = 0;

        foreach ($periods as $p) {
            $used = Incidencia::where('employee_id', $employee->id)
                ->where('periodo_id', $p->id)
                ->whereIn('codigodeincidencia_id', [16, 25, 42])
                ->sum('total_dias');
            
            $pending = max(0, $entitlement - $used);
            $totalPending += $pending;

            $rows[] = "📅 **{$p->periodo}-{$p->year}**: {$used} gozados | *{$pending} pend.*";
        }

        $msg = "🌴 **Reporte de Vacaciones (4 años)**\n";
        $msg .= "👤 {$employee->num_empleado} - {$employee->full_name}\n";
        $msg .= "--------------------------------\n";
        $msg .= implode("\n", $rows);
        $msg .= "\n--------------------------------\n";
        $msg .= "📈 **Total Pendiente: {$totalPending} días**";

        $telegram->sendMessage((string)$chatId, $msg);
    }

    private function executeManualPunch($chatId, $num, $timestamp, $telegram)
    {
        Cache::forget('tg_state_' . $chatId);
        $location = "ADMIN_TELEGRAM";
        $identificador = "{$num}_" . $timestamp->format('YmdHi') . "_ADMIN_TELEGRAM";

        try {
            $checada = Checada::create(['num_empleado' => $num, 'fecha' => $timestamp->format('Y-m-d H:i:s'), 'identificador' => $identificador]);
            if ($checada) {
                event(new ChecadaCreated($checada, $location));
                $telegram->sendMessage((string)$chatId, "✅ Registrado: **{$num}** (" . $timestamp->format('g:i A') . ")");
            }
        } catch (\Exception $e) {
            $telegram->sendMessage((string)$chatId, "❌ Error: Registro duplicado.");
        }
    }
}
