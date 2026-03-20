<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

// We define 'chat' which will match 'presence-chat' from the client
Broadcast::channel('chat', function ($user) {
    if ($user) {
        return [
        'id' => $user->id,
        'name' => $user->name
        ];
    }

    return false;
}, ['guards' => ['web']]);

// Canal privado por usuario para notificaciones
Broadcast::channel('notifications.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

// Canal global para broadcasts a todos los usuarios (Público)
// Los canales públicos no requieren definición en channels.php para suscripción.