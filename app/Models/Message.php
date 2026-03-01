<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mysql_chats';
    protected $guarded = [];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => \App\Events\MessageSent::class ,
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}