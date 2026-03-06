<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $connection = 'biometrico';
    protected $table = 'equipos';
    protected $fillable = ['location', 'ip', 'serial_number', 'last_seen'];
}
