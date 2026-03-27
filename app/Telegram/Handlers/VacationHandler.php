<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\Periodo;
use App\Constants\Incidencias as Inc;

class VacationHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/vacaciones') || str_starts_with($text, '/vaca')) {
            $parts = explode(' ', $text);
            if (count($parts) > 1 && $isAdmin) {
                $num = trim($parts[1]);
                $this->getVacationReport($num);
            } elseif (!$isAdmin && $employee) {
                $this->getVacationReport($employee->num_empleado);
            } else {
                if ($isAdmin) {
                    $this->setSession('waiting_num_vacaciones');
                    $this->sendMessage("🌴 Escribe el **Número de Empleado** para consultar vacaciones:");
                } else {
                    $this->getVacationReport($employee->num_empleado);
                }
            }
            return true;
        }
        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        if ($data === 'admin_vaca_start' && $isAdmin) {
            $this->setSession('waiting_num_vacaciones');
            $this->sendMessage("🌴 Escribe el **Número de Empleado**:");
            return true;
        }

        if ($data === 'user_vaca' && $employee) {
            $this->getVacationReport($employee->num_empleado);
            return true;
        }

        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        if ($state['action'] === 'waiting_num_vacaciones' && $isAdmin) {
            $this->getVacationReport($text);
            return true;
        }
        return false;
    }

    public function getVacationReport($num)
    {
        $this->forgetSession();
        $employee = Employe::where('num_empleado', trim($num))->first();

        if (!$employee) {
            $this->sendMessage("❌ No encontré al empleado **{$num}**.");
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

        // Buscar IDs de vacaciones dinámicamente según sus códigos (60, 62, 63)
        $vacationIds = \App\Models\CodigoDeIncidencia::whereIn('code', [60, 62, 63])->pluck('id');

        foreach ($periods as $p) {
            $used = Incidencia::where('employee_id', $employee->id)
                ->where('periodo_id', $p->id)
                ->whereIn('codigodeincidencia_id', $vacationIds)
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

        $this->sendMessage($msg);
    }
}
