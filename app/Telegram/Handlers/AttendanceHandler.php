<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Models\Checada;
use Carbon\Carbon;

class AttendanceHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/checadas') || str_starts_with($text, '/checs')) {
            $parts = explode(' ', $text);
            if (count($parts) > 1 && $isAdmin) {
                $num = trim($parts[1]);
                $this->getAttendanceReport($num);
            } elseif (!$isAdmin && $employee) {
                $this->getAttendanceReport($employee->num_empleado);
            } else {
                if ($isAdmin) {
                    $this->setSession('waiting_num_checadas');
                    $this->sendMessage("🔢 Escribe el **Número de Empleado** para ver su quincena:");
                } else {
                    // Si es empleado pero no puso num, asume el propio
                    $this->getAttendanceReport($employee->num_empleado);
                }
            }
            return true;
        }
        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        if ($data === 'admin_checs_start' && $isAdmin) {
            $this->setSession('waiting_num_checadas');
            $this->sendMessage("🔢 Escribe el **Número de Empleado**:");
            return true;
        }

        if ($data === 'user_checs' && $employee) {
            $this->getAttendanceReport($employee->num_empleado);
            return true;
        }

        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        if ($state['action'] === 'waiting_num_checadas' && $isAdmin) {
            $this->getAttendanceReport($text);
            return true;
        }
        return false;
    }

    public function getAttendanceReport($num)
    {
        $this->forgetSession();
        $employee = Employe::where('num_empleado', trim($num))->first();

        if (!$employee) {
            $this->sendMessage("❌ No encontré al empleado **{$num}**.");
            return;
        }

        try {
            $qnaId = qna_year(now());
            if (!$qnaId) {
                $this->sendMessage("⚠️ No hay una quincena activa actualmente.");
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

                $rows[] = "{$emoji} <b>{$fechaStr}</b>: {$entrada} - {$salida}";
            }

            $safeNum = htmlspecialchars($employee->num_empleado);
            $safeName = htmlspecialchars($employee->full_name);

            $msg = "🕒 <b>Asistencia Quincena Actual</b>\n";
            $msg .= "👤 {$safeNum} - {$safeName}\n";
            $msg .= "--------------------------------\n";
            $msg .= implode("\n", $rows);

            $this->sendMessage($msg, ['parse_mode' => 'HTML']);
        } catch (\Exception $e) {
            $this->sendMessage("❌ Error al consultar checadas: " . $e->getMessage());
        }
    }
}
