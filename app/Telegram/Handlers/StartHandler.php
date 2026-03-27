<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;

class StartHandler extends TelegramHandler
{
    public function handleCommand($text, $employee, $isAdmin)
    {
        if (str_starts_with($text, '/start')) {
            $parts = explode(' ', $text);
            $alreadyLinked = Employe::where('telegram_chat_id', $this->chatId)->first();

            if (count($parts) > 1) {
                $token = $parts[1];
                $employeeToLink = Employe::where('telegram_link_token', $token)->first();

                if ($employeeToLink) {
                    $employeeToLink->update(['telegram_chat_id' => $this->chatId, 'telegram_link_token' => null]);
                    $safeName = htmlspecialchars($employeeToLink->name);
                    $this->sendMessage("¡Hola <b>{$safeName}</b>! ✅ Vinculación exitosa.", ['parse_mode' => 'HTML']);
                } else {
                    $this->sendMessage("❌ Código inválido.");
                }
            } else {
                $safeName = $alreadyLinked ? htmlspecialchars($alreadyLinked->name) : '';
                $msg = $alreadyLinked ? "👋 ¡Hola <b>{$safeName}</b>! Cuenta activa." : "👋 Vincúlate en el portal para recibir avisos.";
                $this->sendMessage($msg, ['parse_mode' => 'HTML']);
            }
            return true;
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
}
