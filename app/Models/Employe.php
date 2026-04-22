<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Employees\EmployeeApiService;
use Illuminate\Notifications\Notifiable;

class Employe extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $table = "sistemas.employees";
    
    const SINDICATOS_MAP = [
        'c_sindic_local' => 'SNTISSSTE',
        'sindicato_tres' => 'SNADETISSSTE',
        'sindicato_cuatro' => 'SINADTEISSSTE',
        'concepto_nuevo_02' => 'SUTISSSTE',
        'concepto_nuevo_03' => 'SINAPTEISSSTE',
    ];

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
        "estancia_inicio", "estancia_fin", "active", "exento",
        "password", "remember_token", "fcm_token",
        "telegram_chat_id", "telegram_link_token"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

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
        $curp = $this->api_curp;
        if (!$curp || strlen($curp) < 11) {
            return 'No definido';
        }

        $genderChar = strtoupper($curp[10]);

        switch ($genderChar) {
            case 'H':
                return 'Masculino';
            case 'M':
                return 'Femenino';
            default:
                return 'No definido';
        }
    }

    public function getEdadAttribute(): string
    {
        $curp = $this->api_curp;
        if (!$curp && $this->api_rfc) {
            $curp = $this->api_rfc;
        }

        if ($curp && strlen($curp) >= 10) {
            $year = substr($curp, 4, 2);
            $month = substr($curp, 6, 2);
            $day = substr($curp, 8, 2);

            if (is_numeric($year) && is_numeric($month) && is_numeric($day)) {
                $currentYear2 = (int) date('y');
                $fullYear = (int) $year > $currentYear2 ? 1900 + (int) $year : 2000 + (int) $year;
                
                try {
                    $dob = \Carbon\Carbon::createFromFormat('Y-m-d', "$fullYear-$month-$day");
                    
                    // Si la edad calculada es menor a 15, asumimos que nació el siglo pasado (19XX en lugar de 20XX)
                    if ($dob->age < 15) {
                        $dob->subYears(100);
                    }
                    
                    return $dob->age . ' años';
                } catch (\Exception $e) {
                    // Ignore exception
                }
            }
        }
        
        return 'N/A';
    }

    /**
     * Compatibility with Admin layouts/logic
     */
    public function admin(): bool
    {
        return false;
    }

    public function getActiveAttribute($value): bool
    {
        return (bool)$value;
    }

    public function getAvatarAttribute(): ?string
    {
        return null;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->rfc . "@sistemas.example.com";
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
    /**
     * Explicit API Accessors for /employees module
     */
    public function getApiNameAttribute()
    {
        return strtoupper($this->external_data['nombre'] ?? $this->getRawOriginal('name'));
    }

    public function getApiFatherLastnameAttribute()
    {
        return strtoupper($this->external_data['apellido_1'] ?? $this->getRawOriginal('father_lastname'));
    }

    public function getApiMotherLastnameAttribute()
    {
        return strtoupper($this->external_data['apellido_2'] ?? $this->getRawOriginal('mother_lastname'));
    }

    public function getApiFullnameAttribute()
    {
        return trim("{$this->api_name} {$this->api_father_lastname} {$this->api_mother_lastname}");
    }

    public function getApiPuestoAttribute()
    {
        $externalData = $this->external_data;
        if ($externalData && !empty($externalData['n_puesto_plaza'])) {
            $apiName = trim($externalData['n_puesto_plaza']);
            $local = \App\Models\Puesto::where('puesto', $apiName)->first();
            if ($local) return $local;
            
            return (object)[
                'id' => 0,
                'puesto' => $apiName,
                'clave' => $externalData['id_puesto_plaza'] ?? 'SYNC'
            ];
        }
        return $this->puesto;
    }

    public function getApiCurpAttribute()
    {
        return $this->external_data['id_c_u_r_p_st'] ?? $this->getRawOriginal('curp');
    }

    public function getApiRfcAttribute()
    {
        return $this->external_data['id_legal'] ?? $this->getRawOriginal('rfc');
    }

    public function getApiNumPlazaAttribute()
    {
        return $this->external_data['id_plaza'] ?? $this->getRawOriginal('num_plaza');
    }

    public function getApiNumSeguroAttribute()
    {
        return $this->external_data['numero_ss'] ?? $this->getRawOriginal('num_seguro');
    }

    public function getApiNivelAttribute()
    {
        return ($this->external_data['id_nivel'] ?? null) ?: 'N/A';
    }

    public function getApiSubNivelAttribute()
    {
        return ($this->external_data['id_sub_nivel'] ?? null) ?: 'N/A';
    }

    public function getApiFormaPagoAttribute()
    {
        return ($this->external_data['id_forma_pago'] ?? null) ?: 'N/A';
    }

    public function getApiSindicatoAttribute()
    {
        $externalData = $this->external_data;
        if (!$externalData) return 'Ninguno';

        $nomina = $externalData['nomina_data'] ?? [];
        foreach (self::SINDICATOS_MAP as $key => $name) {
            $val = $externalData[$key] ?? ($nomina[$key] ?? 0);
            if ((float)$val > 0) return $name;
        }
        return 'Ninguno';
    }

    public function getTelegramLinkUrl(): string
    {
        if (!$this->telegram_link_token) {
            $this->telegram_link_token = \Illuminate\Support\Str::random(16);
            $this->save();
        }

        $botName = config('services.telegram.bot_name', 'IsssteBot');
        return "https://t.me/{$botName}?start={$this->telegram_link_token}";
    }
}