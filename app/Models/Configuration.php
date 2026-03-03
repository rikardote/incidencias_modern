<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = ['name', 'state'];

    public static function get($name, $default = false)
    {
        $config = self::where('name', $name)->first();
        return $config ? (bool)$config->state : $default;
    }

    public static function set($name, $state)
    {
        return self::updateOrCreate(['name' => $name], ['state' => $state]);
    }
}
