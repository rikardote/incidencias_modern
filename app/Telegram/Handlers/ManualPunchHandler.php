<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Models\Checada;
use App\Events\ChecadaCreated;
use Carbon\Carbon;

class ManualPunchHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        if (!$isAdmin) return false;

        if ($data === 'admin_register_start') {
            $this->setSession('waiting_num_empleado');
            $this->sendMessage("🔢 Escribe el <b>Número de Empleado</b>:", ['parse_mode' => 'HTML']);
            return true;
        }

        if ($data === 'admin_cancel') {
            $this->forgetSession();
            $this->sendMessage("🚫 Cancelado.");
            return true;
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
                $this->setSession('waiting_custom_time', ['num' => $num]);
                $this->sendMessage("🕒 Escribe la hora (**HH:mm**):");
                return true;
            }

            $this->executeManualPunch($num, $timestamp);
            return true;
        }

        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        if (!$isAdmin) return false;

        if ($state['action'] === 'waiting_num_empleado') {
            $num = trim($text);
            $employeeToRegister = Employe::where('num_empleado', $num)->first();

            if (!$employeeToRegister) {
                $safeNum = htmlspecialchars($num);
                $this->sendMessage("❌ Empleado <b>{$safeNum}</b> no encontrado.", ['parse_mode' => 'HTML']);
                return true;
            }

            $safeName = htmlspecialchars($employeeToRegister->full_name);
            $this->sendMessage("👤 <b>Empleado</b>: {$safeName}\n\n¿Hora del registro?", [
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => '✅ Ahora', 'callback_data' => "time_select_now|{$num}"]],
                        [['text' => '⏳ -5 min', 'callback_data' => "time_select_m5|{$num}"], ['text' => '⏳ -15 min', 'callback_data' => "time_select_m15|{$num}"]],
                        [['text' => '⏳ -30 min', 'callback_data' => "time_select_m30|{$num}"], ['text' => '🕒 Personalizada', 'callback_data' => "time_select_custom|{$num}"]],
                        [['text' => '❌ Cancelar', 'callback_data' => 'admin_cancel']],
                    ]
                ])
            ]);
            return true;
        }

        if ($state['action'] === 'waiting_custom_time') {
            $num = $state['num'];
            try {
                $time = Carbon::createFromFormat('H:i', trim($text));
                $timestamp = now()->setTime($time->hour, $time->minute);
                $this->executeManualPunch($num, $timestamp);
            } catch (\Exception $e) {
                $this->sendMessage("❌ Invalido. Ejemplo (HH:mm): 14:45");
            }
            return true;
        }

        return false;
    }

    private function executeManualPunch($num, $timestamp)
    {
        $this->forgetSession();
        $location = "ADMIN_TELEGRAM";
        $identificador = "{$num}_" . $timestamp->format('YmdHi') . "_ADMIN_TELEGRAM";

        try {
            $checada = Checada::create(['num_empleado' => $num, 'fecha' => $timestamp->format('Y-m-d H:i:s'), 'identificador' => $identificador]);
            if ($checada) {
                event(new ChecadaCreated($checada, $location));
                $safeNum = htmlspecialchars($num);
                $this->sendMessage("✅ Registrado: <b>{$safeNum}</b> (" . $timestamp->format('g:i A') . ")", ['parse_mode' => 'HTML']);
            }
        } catch (\Exception $e) {
            $this->sendMessage("❌ Error: Registro duplicado.");
        }
    }
}
