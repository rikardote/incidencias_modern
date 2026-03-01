<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Log::info('Channels.php - DEFINING CHANNELS');

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

// We define 'chat' which will match 'presence-chat' from the client
Broadcast::channel('chat', function ($user) {
    Log::info('CHANNELS_DEBUG: Hit closure for user: ' . ($user ? $user->id : 'ANONYMOUS'));

    if ($user) {
        return [
        'id' => $user->id,
        'name' => $user->name
        ];
    }

    return false;
}, ['guards' => ['web']]);