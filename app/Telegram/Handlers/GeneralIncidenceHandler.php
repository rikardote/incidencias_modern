<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Models\Incidencia;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GeneralIncidenceHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/incidencias') || str_starts_with($text, '/incs')) {
            $parts = explode(' ', $text);
            if (count($parts) > 1 && $isAdmin) {
                $num = trim($parts[1]);
                $this->getGeneralIncidencesReport($num);
            } elseif (!$isAdmin && $employee) {
                $this->getGeneralIncidencesReport($employee->num_empleado);
            } else {
                if ($isAdmin) {
                    $this->setSession('waiting_num_incidencias');
                    $this->sendMessage("📋 Escribe el **Número de Empleado** para ver sus incidencias del último mes:");
                } else {
                    $this->getGeneralIncidencesReport($employee->num_empleado);
                }
            }
            return true;
        }
        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        if ($data === 'admin_incs_start' && $isAdmin) {
            $this->setSession('waiting_num_incidencias');
            $this->sendMessage("📋 Escribe el **Número de Empleado** para ver incidencias del último mes:");
            return true;
        }

        if ($data === 'user_incs' && $employee) {
            $this->getGeneralIncidencesReport($employee->num_empleado);
            return true;
        }

        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        if ($state['action'] === 'waiting_num_incidencias' && $isAdmin) {
            $this->getGeneralIncidencesReport($text);
            return true;
        }
        return false;
    }

    public function getGeneralIncidencesReport($num)
    {
        $this->forgetSession();
        $employee = Employe::where('num_empleado', trim($num))->first();

        if (!$employee) {
            $this->sendMessage("❌ No encontré al empleado **{$num}**.");
            return;
        }

        try {
            $lastMonth = now()->subDays(31);
            
            $incidencias = Incidencia::with('codigo')
                ->where('employee_id', $employee->id)
                ->where('fecha_inicio', '>=', $lastMonth)
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            $rows = [];
            foreach ($incidencias as $inc) {
                $fechaStr = Carbon::parse($inc->fecha_inicio)->translatedFormat('d/M');
                $code = $inc->codigo->code ?? '???';
                $name = $inc->codigo->description ?? 'Sin nombre';
                
                // Acortar descripción y escapar para HTML
                $shortName = htmlspecialchars(Str::limit($name, 20, '...'));
                $safeCode = htmlspecialchars($code);
                $safeFecha = htmlspecialchars($fechaStr);
                
                $rows[] = "• <b>{$safeFecha}</b>: [{$safeCode}] {$shortName}";
            }

            $safeNum = htmlspecialchars($employee->num_empleado);
            $safeName = htmlspecialchars($employee->full_name);

            $msg = "📋 <b>Incidencias del Último Mes</b>\n";
            $msg .= "👤 {$safeNum} - {$safeName}\n";
            $msg .= "--------------------------------\n";
            
            if (count($rows) > 0) {
                $msg .= implode("\n", $rows);
            } else {
                $msg .= "<i>No se registran incidencias en los últimos 30 días.</i>";
            }
            
            $msg .= "\n--------------------------------\n";
            $msg .= "Total: <b>" . count($rows) . "</b> incidencias.";

            $this->sendMessage($msg, ['parse_mode' => 'HTML']);
        } catch (\Exception $e) {
            $this->sendMessage("❌ Error al consultar incidencias: " . $e->getMessage());
        }
    }
}
