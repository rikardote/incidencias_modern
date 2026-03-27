<?php

namespace App\Telegram\Handlers;

class MenuHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/admin')) {
            return $this->handleAdminMenu($isAdmin);
        }

        if (str_starts_with($text, '/menu') || (!$employee && !$isAdmin && !str_starts_with($text, '/start'))) {
            return $this->handleUserMenu($employee);
        }

        return false;
    }

    public function handleCallback($data, $employee, $isAdmin)
    {
        return false;
    }

    public function handleState($text, $state, $employee, $isAdmin)
    {
        return false;
    }

    public function handleAdminMenu($isAdmin)
    {
        if (!$isAdmin) {
            $this->sendMessage("🚫 Sin permisos de administrador.");
            return true;
        }

        $this->sendMessage("🛠 <b>Admin Bot</b>\n\n¿Qué deseas hacer?", [
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => '📝 Registrar Asistencia Manual', 'callback_data' => 'admin_register_start']],
                    [['text' => '🌴 Consultar Vacaciones', 'callback_data' => 'admin_vaca_start']],
                    [['text' => '💵 Consultar Días Económicos', 'callback_data' => 'admin_eco_start']],
                    [['text' => '🎫 Consultar Pases de Salida', 'callback_data' => 'admin_pases_start']],
                    [['text' => '📋 Consultar Incidencias (Mes)', 'callback_data' => 'admin_incs_start']],
                    [['text' => '🕒 Ver Checadas Quincena', 'callback_data' => 'admin_checs_start']],
                    // [['text' => '💰 Consultar Nómina', 'callback_data' => 'admin_payroll_start']],
                ]
            ])
        ]);
        return true;
    }

    public function handleUserMenu($employee)
    {
        if (!$employee) {
            $this->sendMessage("👋 <b>¡Bienvenido!</b>\n\nPara consultar tus datos, primero vincula tu cuenta desde el portal institucional.", ['parse_mode' => 'HTML']);
            return true;
        }

        $buttons = [
            [['text' => '🕒 Mis Checadas (Qna)', 'callback_data' => 'user_checs']],
            [['text' => '🌴 Mis Vacaciones', 'callback_data' => 'user_vaca']],
            [['text' => '📋 Mis Incidencias (Mes)', 'callback_data' => 'user_incs']],
            // [['text' => '💰 Mi Nómina', 'callback_data' => 'user_payroll_start']],
        ];

        // Solo personal de Base (ID 1)
        if ($employee->condicion_id == 1) {
            $buttons[] = [['text' => '💵 Mis Días Económicos', 'callback_data' => 'user_eco']];
            $buttons[] = [['text' => '🎫 Mis Pases de Salida', 'callback_data' => 'user_pases']];
        }

        $safeName = htmlspecialchars($employee->name);
        $this->sendMessage("👋 ¡Hola <b>{$safeName}</b>!\n\n¿Qué información deseas consultar?", [
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ])
        ]);
        return true;
    }
}
