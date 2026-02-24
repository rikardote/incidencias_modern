<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qna extends Model
{
    protected $table = "qnas";
    protected $fillable = ["qna", "year", "description", "active"];

    public function getQnaAttribute($value)
    {
        return str_pad($value, 2, "0", STR_PAD_LEFT);
    }
}
