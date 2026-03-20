<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Employees\EmployeeApiService;

class Employe extends Model
{
    use SoftDeletes;

    protected $table = "sistemas.employees";

    public function getTable()
    {
        if (app()->environment('testing')) {
            return 'employees';
        }
        return parent::getTable();
    }

    protected $fillable = [
        "num_empleado", "name", "father_lastname", "mother_lastname",
        "curp", "rfc", "fecha_ingreso",
        "deparment_id", "condicion_id", "puesto_id", "horario_id",
        "num_plaza", "num_seguro", "jornada_id", "lactancia",
        "lactancia_inicio", "lactancia_fin", "comisionado", "estancia",
        "estancia_inicio", "estancia_fin", "active", "exento"
    ];

    public function setCurpAttribute($v)
    {
        $this->attributes['curp'] = $v ? mb_strtoupper(trim($v)) : null;
    }

    public function setRfcAttribute($v)
    {
        $this->attributes['rfc'] = $v ? mb_strtoupper(trim($v)) : null;
    }

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

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'employee_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->father_lastname} {$this->mother_lastname}";
    }

    public function getNumEmpleadoAttribute($value): string
    {
        return str_pad($value, 6, "0", STR_PAD_LEFT);
    }
    public function getGenderAttribute(): string
    {
        if (!$this->curp || strlen($this->curp) < 11) {
            return 'No definido';
        }

        $genderChar = strtoupper($this->curp[10]);

        switch ($genderChar) {
            case 'H':
                return 'Masculino';
            case 'M':
                return 'Femenino';
            default:
                return 'No definido';
        }
    }


    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Get extended data from the External API (Plantilla)
     */
    public function getExternalDataAttribute()
    {
        return app(EmployeeApiService::class)->getEmployeeData($this->num_empleado);
    }

    /**
     * Accessors for common external fields
     */
    /**
     * Accessors for common external fields with API priority
     */
    public function getNameAttribute($value)
    {
        return strtoupper(($this->external_data['nombre'] ?? null) ?: $value);
    }

    public function getFatherLastnameAttribute($value)
    {
        return strtoupper(($this->external_data['apellido_1'] ?? null) ?: $value);
    }

    public function getMotherLastnameAttribute($value)
    {
        return strtoupper(($this->external_data['apellido_2'] ?? null) ?: $value);
    }

    /**
     * Override the 'puesto' relationship/attribute to prioritize API data
     */
    public function getPuestoAttribute($value)
    {
        $externalData = $this->external_data;
        if ($externalData && !empty($externalData['n_puesto_plaza'])) {
            $apiName = trim($externalData['n_puesto_plaza']);
            $local = \App\Models\Puesto::where('puesto', $apiName)->first();
            
            if ($local) {
                return $local;
            }
            
            // Return a generic object if not found locally yet
            return (object)[
                'id' => 0,
                'puesto' => $apiName,
                'clave' => $externalData['id_puesto_plaza'] ?? 'SYNC'
            ];
        }

        return $this->getRelationValue('puesto');
    }

    public function getCurpAttribute($value)
    {
        return ($this->external_data['id_c_u_r_p_st'] ?? null) ?: $value;
    }

    public function getRfcAttribute($value)
    {
        return ($this->external_data['id_legal'] ?? null) ?: $value;
    }

    public function getNumPlazaAttribute($value)
    {
        return ($this->external_data['id_plaza'] ?? null) ?: $value;
    }

    public function getNumSeguroAttribute($value)
    {
        return ($this->external_data['numero_ss'] ?? null) ?: $value;
    }

    public function getNivelAttribute()
    {
        return ($this->external_data['id_nivel'] ?? null) ?: 'N/A';
    }

    public function getSubNivelAttribute()
    {
        return ($this->external_data['id_sub_nivel'] ?? null) ?: 'N/A';
    }

    public function getFormaPagoAttribute()
    {
        return ($this->external_data['id_forma_pago'] ?? null) ?: 'N/A';
    }
}