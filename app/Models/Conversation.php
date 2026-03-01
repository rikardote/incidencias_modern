<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $connection = 'mysql_chats';
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}