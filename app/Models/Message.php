<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = app()->environment('testing') ? config('database.default') : 'mysql_chats';
    }
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