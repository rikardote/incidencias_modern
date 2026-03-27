<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Models\Incidencia;
use Carbon\Carbon;

class ExitPassHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/pases')) {
            $parts = explode(' ', $text);
            if (count($parts) > 1 && $isAdmin) {
                $num = trim($parts[1]);
                $this->getExitPassReport($num);
            } elseif (!$isAdmin && $employee) {
                $this->getExitPassReport($employee->num_empleado);
            } else {
                if ($isAdmin) {
                    $this->setSession('waiting_num_pases');
                    $this->sendMessage("🎫 Escribe el **Número de Empleado** para consultar pases de salida:");
                } else {
                    $this->getExitPassReport($employee->num_empleado);
                }
            }
            return true;
        }
        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        if ($data === 'admin_pases_start' && $isAdmin) {
            $this->setSession('waiting_num_pases');
            $this->sendMessage("🎫 Escribe el **Número de Empleado** para consultar pases de salida:");
            return true;
        }

        if ($data === 'user_pases' && $employee) {
            $this->getExitPassReport($employee->num_empleado);
            return true;
        }

        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        if ($state['action'] === 'waiting_num_pases' && $isAdmin) {
            $this->getExitPassReport($text);
            return true;
        }
        return false;
    }

    public function getExitPassReport($num)
    {
        $this->forgetSession();
        $employee = Employe::where('num_empleado', trim($num))->first();

        if (!$employee) {
            $this->sendMessage("❌ No encontré al empleado **{$num}**.");
            return;
        }

        if ($employee->condicion_id != 1) {
            $this->sendMessage("ℹ️ Los **Pases de Salida** solo aplican para personal de **BASE**.");
            return;
        }

        try {
            $year = now()->year;
            
            // Buscar el ID dinámicamente según el código 905
            $code905 = \App\Models\CodigoDeIncidencia::where('code', 905)->first();
            
            if (!$code905) {
                 $this->sendMessage("❌ No se encontró la configuración para el código 905 (Pases de Salida).");
                 return;
            }

            $incidencias = Incidencia::where('employee_id', $employee->id)
                ->where('codigodeincidencia_id', $code905->id)
                ->whereYear('fecha_inicio', $year)
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            $total = $incidencias->count();
            $rows = [];

            foreach ($incidencias as $inc) {
                $fechaStr = Carbon::parse($inc->fecha_inicio)->translatedFormat('d/M/Y');
                $rows[] = "• {$fechaStr}";
            }

            $msg = "🎫 **Pases de Salida ({$year})**\n";
            $msg .= "👤 {$employee->num_empleado} - {$employee->full_name}\n";
            $msg .= "--------------------------------\n";
            
            if (count($rows) > 0) {
                $msg .= implode("\n", $rows);
            } else {
                $msg .= "_No se registran pases de salida en el año actual._";
            }
            
            $msg .= "\n--------------------------------\n";
            $msg .= "📈 **Total: {$total} pases**";

            $this->sendMessage($msg);
        } catch (\Exception $e) {
            $this->sendMessage("❌ Error al consultar pases: " . $e->getMessage());
        }
    }
}
