<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Services\Notifications\TelegramService;

Artisan::command('telegram:set-webhook', function (TelegramService $telegram) {
    $url = config('app.url') . '/telegram/webhook/' . config('services.telegram.token');
    $this->info("Setting webhook to: {$url}");
    if ($telegram->setWebhook($url)) {
        $this->info("Webhook set successfully!");
    } else {
        $this->error("Failed to set webhook.");
    }
})->purpose('Set the Telegram bot webhook');
