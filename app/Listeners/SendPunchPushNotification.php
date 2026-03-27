<?php

namespace App\Listeners;

use App\Events\ChecadaCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPunchPushNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ChecadaCreated $event): void
    {
        Log::info("SendPunchPushNotification: Processing event for checada #" . ($event->checada->id ?? 'new'));
        try {
            $checada = $event->checada;
            $empleado = \App\Models\Employe::where('num_empleado', ltrim($checada->num_empleado, '0'))->first();

            if (!$empleado) {
                return;
            }

            // --- FIREBASE PUSH NOTIFICATION ---
            if (!empty($empleado->fcm_token) && app()->bound('firebase.messaging')) {
                try {
                    $messaging = app('firebase.messaging');
                    $title = 'Nueva Checada Biométrico';
                    $body = "Se ha registrado tu checada a las " . \Carbon\Carbon::parse($checada->fecha)->format('g:i A');

                    $message = \Kreait\Firebase\Messaging\CloudMessage::fromArray([
                        'token' => $empleado->fcm_token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'num_empleado' => (string)$checada->num_empleado,
                            'fecha' => (string)$checada->fecha,
                        ],
                    ]);

                    $messaging->send($message);
                    Log::info("Push notification sent to employee {$empleado->num_empleado} for punch {$checada->id}.");
                } catch (\Exception $fe) {
                    Log::error("Firebase Notification Error for employee {$empleado->num_empleado}: " . $fe->getMessage());
                }
            } else if (empty($empleado->fcm_token)) {
                Log::debug("Skipping Firebase notification for employee {$empleado->num_empleado}: No FCM token.");
            }

            // --- TELEGRAM NOTIFICATION ---
            if (!empty($empleado->telegram_chat_id)) {
                $telegram = app(\App\Services\Notifications\TelegramService::class);
                $safeTitle = htmlspecialchars($title);
                $safeBody = htmlspecialchars($body);
                
                $telegram->sendMessage($empleado->telegram_chat_id, "🔔 <b>{$safeTitle}</b>\n\n{$safeBody}", ['parse_mode' => 'HTML']);
                Log::info("Telegram notification sent to employee {$empleado->num_empleado} (Chat: {$empleado->telegram_chat_id}).");
            }

        } catch (\Exception $e) {
            Log::error("Failed to process notifications for punch {$event->checada->id}: " . $e->getMessage());
        }
    }
}
