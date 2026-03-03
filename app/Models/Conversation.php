<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = app()->environment('testing') ? config('database.default') : 'mysql_chats';
    }
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}