<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = "deparments";

    protected $fillable = ["code", "description"];

    public function employees(): HasMany
    {
        return $this->hasMany(Employe::class, "deparment_id");
    }

    public function getCodeAttribute($value)
    {
        return str_pad($value, 5, "0", STR_PAD_LEFT);
    }
}
