<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaptureException extends Model
{
    protected $fillable = ["user_id", "expires_at", "reason"];

    protected $casts = [
        "expires_at" => "datetime",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
