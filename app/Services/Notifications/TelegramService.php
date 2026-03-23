<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.token') ?? env('TELEGRAM_BOT_TOKEN');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function sendMessage(string $chatId, string $text, array $params = []): bool
    {
        try {
            $data = array_merge([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ], $params);

            $response = Http::post("{$this->baseUrl}/sendMessage", $data);

            if ($response->successful()) {
                Log::info("Telegram message sent to {$chatId}");
                return true;
            }

            Log::error("Telegram API error: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Telegram Service Exception: " . $e->getMessage());
            return false;
        }
    }

    public function setWebhook(string $url): bool
    {
        $response = Http::post("{$this->baseUrl}/setWebhook", [
            'url' => $url,
        ]);

        return $response->successful();
    }
}
