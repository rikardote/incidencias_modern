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