<?php

namespace App\Telegram\Handlers;

use App\Models\Employe;
use App\Services\Notifications\TelegramService;
use Illuminate\Support\Facades\Cache;

abstract class TelegramHandler
{
    protected $telegram;
    protected $chatId;

    public function __construct(TelegramService $telegram, $chatId)
    {
        $this->telegram = $telegram;
        $this->chatId = $chatId;
    }

    abstract public function handleCommand($text, $employee, $isAdmin);
    abstract public function handleCallback($data, $employee, $isAdmin);
    abstract public function handleState($text, $state, $employee, $isAdmin);

    protected function sendMessage($text, $options = [])
    {
        return $this->telegram->sendMessage((string)$this->chatId, $text, $options);
    }

    protected function setSession($action, $data = [], $ttl = 300)
    {
        Cache::put('tg_state_' . $this->chatId, array_merge(['action' => $action], $data), $ttl);
    }

    protected function forgetSession()
    {
        Cache::forget('tg_state_' . $this->chatId);
    }
}
