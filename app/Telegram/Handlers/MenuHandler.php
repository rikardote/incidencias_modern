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

        $this->sendMessage("🛠 **Admin Bot**\n\n¿Qué deseas hacer?", [
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => '📝 Registrar Asistencia Manual', 'callback_data' => 'admin_register_start']],
                    [['text' => '🌴 Consultar Vacaciones', 'callback_data' => 'admin_vaca_start']],
                    [['text' => '💵 Consultar Días Económicos', 'callback_data' => 'admin_eco_start']],
                    [['text' => '🎫 Consultar Pases de Salida', 'callback_data' => 'admin_pases_start']],
                    [['text' => '🕒 Ver Checadas Quincena', 'callback_data' => 'admin_checs_start']],
                ]
            ])
        ]);
        return true;
    }

    public function handleUserMenu($employee)
    {
        if (!$employee) {
            $this->sendMessage("👋 **¡Bienvenido!**\n\nPara consultar tus datos, primero vincula tu cuenta desde el portal institucional.");
            return true;
        }

        $buttons = [
            [['text' => '🕒 Mis Checadas (Qna)', 'callback_data' => 'user_checs']],
            [['text' => '🌴 Mis Vacaciones', 'callback_data' => 'user_vaca']],
        ];

        // Solo personal de Base (ID 1)
        if ($employee->condicion_id == 1) {
            $buttons[] = [['text' => '💵 Mis Días Económicos', 'callback_data' => 'user_eco']];
            $buttons[] = [['text' => '🎫 Mis Pases de Salida', 'callback_data' => 'user_pases']];
        }

        $this->sendMessage("👋 ¡Hola **{$employee->name}**!\n\n¿Qué información deseas consultar?", [
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ])
        ]);
        return true;
    }
}
