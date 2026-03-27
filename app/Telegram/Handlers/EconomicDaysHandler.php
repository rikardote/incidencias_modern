<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Models\Incidencia;
use Carbon\Carbon;

class EconomicDaysHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/economicos') || str_starts_with($text, '/ecos')) {
            $parts = explode(' ', $text);
            if (count($parts) > 1 && $isAdmin) {
                $num = trim($parts[1]);
                $this->getEconomicDaysReport($num);
            } elseif (!$isAdmin && $employee) {
                $this->getEconomicDaysReport($employee->num_empleado);
            } else {
                if ($isAdmin) {
                    $this->setSession('waiting_num_economicos');
                    $this->sendMessage("💵 Escribe el **Número de Empleado** para consultar sus económicos:");
                } else {
                    $this->getEconomicDaysReport($employee->num_empleado);
                }
            }
            return true;
        }
        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        if ($data === 'admin_eco_start' && $isAdmin) {
            $this->setSession('waiting_num_economicos');
            $this->sendMessage("💵 Escribe el **Número de Empleado** para consultar económicos:");
            return true;
        }

        if ($data === 'user_eco' && $employee) {
            $this->getEconomicDaysReport($employee->num_empleado);
            return true;
        }

        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        if ($state['action'] === 'waiting_num_economicos' && $isAdmin) {
            $this->getEconomicDaysReport($text);
            return true;
        }
        return false;
    }

    public function getEconomicDaysReport($num)
    {
        $this->forgetSession();
        $employee = Employe::where('num_empleado', trim($num))->first();

        if (!$employee) {
            $this->sendMessage("❌ No encontré al empleado **{$num}**.");
            return;
        }

        if ($employee->condicion_id != 1) {
            $this->sendMessage("ℹ️ Los **Días Económicos** solo aplican para personal de **BASE**.");
            return;
        }

        try {
            $year = now()->year;
            
            // Buscar el ID dinámicamente según el código 41
            $code41 = \App\Models\CodigoDeIncidencia::where('code', 41)->first();
            
            if (!$code41) {
                 $this->sendMessage("❌ No se encontró la configuración para el código 41 (Económicos).");
                 return;
            }

            $incidencias = Incidencia::where('employee_id', $employee->id)
                ->where('codigodeincidencia_id', $code41->id)
                ->whereYear('fecha_inicio', $year)
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            $total = $incidencias->sum('total_dias');
            $rows = [];

            foreach ($incidencias as $inc) {
                $fechaStr = Carbon::parse($inc->fecha_inicio)->translatedFormat('d/M/Y');
                if ($inc->total_dias > 1) {
                    $fechaFin = Carbon::parse($inc->fecha_fin)->translatedFormat('d/M/Y');
                    $fechaStr .= " al {$fechaFin}";
                }
                $rows[] = "• {$fechaStr} ({$inc->total_dias} día/s)";
            }

            $msg = "💵 **Días Económicos Gozados ({$year})**\n";
            $msg .= "👤 {$employee->num_empleado} - {$employee->full_name}\n";
            $msg .= "--------------------------------\n";
            
            if (count($rows) > 0) {
                $msg .= implode("\n", $rows);
            } else {
                $msg .= "_No se registran días económicos en el año actual._";
            }
            
            $msg .= "\n--------------------------------\n";
            $msg .= "📈 **Total Consumido: {$total} días**";

            $this->sendMessage($msg);
        } catch (\Exception $e) {
            $this->sendMessage("❌ Error al consultar económicos: " . $e->getMessage());
        }
    }
}
