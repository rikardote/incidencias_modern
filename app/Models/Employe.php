<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    use SoftDeletes;

    protected $table = "employees";

    protected $fillable = [
        "num_empleado", "name", "father_lastname", "mother_lastname",
        "deparment_id", "condicion_id", "puesto_id", "horario_id",
        "num_plaza", "num_seguro", "jornada_id", "lactancia",
        "lactancia_inicio", "lactancia_fin", "comisionado", "estancia", "active"
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class , "deparment_id");
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->father_lastname} {$this->mother_lastname}";
    }

    public function getNumEmpleadoAttribute($value): string
    {
        return str_pad($value, 5, "0", STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('active', '1');
    }
}