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

            if (!$empleado || empty($empleado->fcm_token)) {
                return;
            }

            // Check if Firebase is resolved
            if (!app()->bound('firebase.messaging')) {
                Log::warning("Firebase Messaging is not bound. Ensure krAit/laravel-firebase is installed and configured.");
                return;
            }

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

        } catch (\Exception $e) {
            Log::error("Failed to send push notification for punch {$event->checada->id}: " . $e->getMessage());
        }
    }
}
