<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Services\Employees\EmployeeApiService;
use Carbon\Carbon;

class PayrollHandler extends TelegramHandler
{
    private $api;

    protected const MAPPINGS = [
        'sal_base' => 'Sueldo',
        'despensa' => 'Despensa',
        'quniquenio' => 'Quinquenio',
        'prev_social' => 'Prev. Social',
        'ayuda_transp' => 'Ayuda Transp.',
        'ayuda_actualiza' => 'Ayuda Actualiza.',
        'asigna_medico' => 'Asigna. Médico',
        'comision_auxil' => 'Comisión Aux.',
        'f_ahorro_indi' => 'Fondo Ahorro Ind.',
        'ispt' => 'I.S.P.T.',
        'fonacot' => 'FONACOT',
        'ahorro_solid' => 'Ahorro Solid.',
        'gastos_funer' => 'Gastos Fun.',
        'seguro_salud' => 'Seguro Salud',
        'seg_vida_hid2' => 'Seguro Vida',
        'c_sindic_local' => 'SNTISSSTE',
        'cred_fovissste' => 'Crédito FOVISSSTE',
        'seg_danios_fov' => 'Seguro Daños FOV',
        'prest_med_plazo' => 'Prést. Med. Plazo',
        'seg_institucion' => 'Seguro Inst.',
        'seguro_cesantia' => 'Seguro Cesantía',
        'serv_soc_y_cult' => 'Serv. Soc/Cult.',
        'seguro_ries_trab' => 'Seguro Riesgo Trab.',
        'seguro_inval_y_vida' => 'Seguro Inval/Vida',
    ];

    protected const PERCEPTION_KEYS = [
        'sal_base', 'despensa', 'quniquenio', 'prev_social', 'ayuda_transp', 
        'ayuda_actualiza', 'asigna_medico', 'comision_auxil', 'total_devengos'
    ];

    public function __construct(\App\Services\Notifications\TelegramService $telegram, $chatId)
    {
        parent::__construct($telegram, $chatId);
        $this->api = app(EmployeeApiService::class);
    }

    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/nomina')) {
            if (!$employee) {
                $this->sendMessage("⚠️ Debes vincular tu cuenta para consultar tu nómina.");
                return true;
            }
            $this->showQuincenaSelection($employee->num_empleado);
            return true;
        }
        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        if ($data === 'user_incs') return false; // Handled by other
        
        if ($data === 'user_payroll_start' && $employee) {
            $this->showQuincenaSelection($employee->num_empleado);
            return true;
        }

        if (str_starts_with($data, 'pay_select|')) {
            $parts = explode('|', $data);
            $index = (int)$parts[1];
            $num = $parts[2];
            $this->showPayrollDetail($num, $index);
            return true;
        }

        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        return false;
    }

    private function showQuincenaSelection($num)
    {
        $history = $this->api->getPayrollHistory($num);
        if (empty($history)) {
            $this->sendMessage("📭 No se encontraron registros de nómina disponibles.");
            return;
        }

        // Tomar las últimas 6
        $history = array_slice(array_reverse($history), 0, 6);
        
        $buttons = [];
        foreach ($history as $index => $record) {
            $periodo = $record['periodo'] ?? null;
            $label = "📅 " . $this->getQnaLabelFromPeriod($periodo, $record['created_at']);
            $buttons[] = [['text' => $label, 'callback_data' => "pay_select|{$index}|{$num}"]];
        }

        $this->sendMessage("💰 <b>Consulta de Nómina</b>\n\nSelecciona la quincena que deseas ver:", [
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);
    }

    private function showPayrollDetail($num, $index)
    {
        $history = array_slice(array_reverse($this->api->getPayrollHistory($num)), 0, 6);
        $record = $history[$index] ?? null;

        if (!$record) {
            $this->sendMessage("❌ Error: No se pudo recuperar el registro seleccionado.");
            return;
        }

        $nomina = $record['nomina_data'] ?? [];
        $perceptions = [];
        $deductions = [];

        foreach ($nomina as $key => $val) {
            if ($val <= 0) continue;
            if (in_array($key, ['liquido', 'total_devengos', 'total_retenido_a', 'total_neto', 'total_deducciones'])) continue;

            $label = self::MAPPINGS[$key] ?? $key;
            $formatted = "• {$label}: <b>$" . number_format($val, 2) . "</b>";

            if (in_array($key, self::PERCEPTION_KEYS)) {
                $perceptions[] = $formatted;
            } else {
                $deductions[] = $formatted;
            }
        }

        $periodo = $record['periodo'] ?? null;
        $qnaLabel = $this->getQnaLabelFromPeriod($periodo, $record['created_at']);

        $msg = "💰 <b>Desglose de Nómina</b>\n";
        $msg .= "📅 {$qnaLabel}\n";
        $msg .= "--------------------------------\n\n";

        $msg .= "<b>➕ PERCEPCIONES</b>\n";
        $msg .= !empty($perceptions) ? implode("\n", $perceptions) : "<i>Ninguna</i>";
        $msg .= "\n\n";

        $msg .= "<b>➖ DEDUCCIONES</b>\n";
        $msg .= !empty($deductions) ? implode("\n", $deductions) : "<i>Ninguna</i>";
        $msg .= "\n\n";

        $msg .= "--------------------------------\n";
        $totalP = $nomina['total_devengos'] ?? 0;
        $totalD = $nomina['total_retenido_a'] ?? ($nomina['total_deducciones'] ?? 0);
        $neto = $nomina['liquido'] ?? ($nomina['total_neto'] ?? 0);

        $msg .= "💸 Total Percep: <b>$" . number_format($totalP, 2) . "</b>\n";
        $msg .= "📉 Total Deduc: <b>$" . number_format($totalD, 2) . "</b>\n";
        $msg .= "💵 <b>NETO A PAGAR: $" . number_format($neto, 2) . "</b>";

        $this->sendMessage($msg, ['parse_mode' => 'HTML']);
    }

    private function getQnaLabelFromPeriod($periodo, $createdAt)
    {
        if (!$periodo || !str_contains($periodo, '-')) {
            $date = Carbon::parse($createdAt);
            $day = $date->day;
            $part = ($day <= 15) ? "1ra" : "2da";
            return "{$part} Qna. " . ucfirst($date->translatedFormat('F')) . " {$date->year}";
        }
        
        [$qnaNum, $year] = explode('-', $periodo);
        $qnaNum = (int)$qnaNum;
        
        $monthNum = (int)ceil($qnaNum / 2);
        $part = ($qnaNum % 2 === 1) ? "1ra" : "2da";
        
        $monthName = Carbon::create()->month($monthNum)->translatedFormat('F');
        
        return "{$part} Qna. " . ucfirst($monthName) . " {$year}";
    }
}
