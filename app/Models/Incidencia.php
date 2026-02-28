<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencia extends Model
{
    use SoftDeletes;

    protected $table = "incidencias";

    protected $fillable = [
        "qna_id", "employee_id", "fecha_inicio", "fecha_final",
        "codigodeincidencia_id", "periodo_id", "token", "diagnostico",
        "medico_id", "fecha_expedida", "num_licencia", "otorgado",
        "pendientes", "becas_comments", "fecha_capturado",
        "cobertura_txt", "horas_otorgadas", "autoriza_txt", "total_dias", "capturado_por"
    ];

    /*
     |--------------------------------------------------------------------------
     | MUTATORS — Guardar siempre en MAYÚSCULAS
     |--------------------------------------------------------------------------
     */
    public function setDiagnosticoAttribute($v)
    {
        $this->attributes['diagnostico'] = $v ? mb_strtoupper(trim($v)) : null;
    }
    public function setCoberturaTxtAttribute($v)
    {
        $this->attributes['cobertura_txt'] = $v ? mb_strtoupper(trim($v)) : null;
    }
    public function setAutorizaTxtAttribute($v)
    {
        $this->attributes['autoriza_txt'] = $v ? mb_strtoupper(trim($v)) : null;
    }
    public function setNumLicenciaAttribute($v)
    {
        $this->attributes['num_licencia'] = $v ? mb_strtoupper(trim($v)) : null;
    }
    public function setCapturadoPorAttribute($v)
    {
        $this->attributes['capturado_por'] = $v ? mb_strtoupper(trim($v)) : null;
    }
    public function setBecasCommentsAttribute($v)
    {
        $this->attributes['becas_comments'] = $v ? mb_strtoupper(trim($v)) : null;
    }


    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    public function qna(): BelongsTo
    {
        return $this->belongsTo(Qna::class);
    }

    public function codigo(): BelongsTo
    {
        return $this->belongsTo(CodigoDeIncidencia::class , "codigodeincidencia_id");
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class , "periodo_id");
    }

    /**
     * Get the total days of vacation used by an employee for a specific period and code.
     *
     * @param int $employee_id
     * @param int $periodo_id
     * @param int $codigodeincidencia_id
     * @return int
     */
    public static function getTotalVacaciones($employee_id, $periodo_id, $codigodeincidencia_id)
    {
        return self::where('employee_id', $employee_id)
            ->where('periodo_id', $periodo_id)
            ->where('codigodeincidencia_id', $codigodeincidencia_id)
            ->sum('total_dias');
    }

    /**
     * Suma los días del código 41 (Permisos con goce por antigüedad)
     * de un empleado en el rango de fechas dado.
     * Replica exactamente el comportamiento del sistema legacy:
     *   WHERE codigos_de_incidencias.id = 3  (código 41)
     *   AND num_empleado = $num_empleado
     *   AND fecha_inicio BETWEEN $fecha_inicio AND $fecha_final
     */
    public static function getTotalLicencias($num_empleado, $fecha_inicio, $fecha_final)
    {
        $result = self::selectRaw('SUM(total_dias) as total')
            ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
            ->whereNull('incidencias.deleted_at')
            ->where('incidencias.codigodeincidencia_id', 3) // ID 3 = código 41
            ->where('employees.num_empleado', $num_empleado)
            ->whereBetween('incidencias.fecha_inicio', [$fecha_inicio, $fecha_final])
            ->first();

        return $result->total ?? 0;
    }

    /**
     * Suma los días de T.X.T (código 900) de un empleado en un rango de fechas.
     * Replica exactamente el comportamiento del sistema legacy:
     *   WHERE codigos_de_incidencias.code = '900'
     *   AND num_empleado = $num_empleado
     *   AND fecha_inicio BETWEEN $fecha_inicio AND $fecha_final
     */
    public static function Gettxtpormes($fecha_inicio, $fecha_final, $num_empleado)
    {
        $result = self::selectRaw('SUM(total_dias) as total')
            ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
            ->join('codigos_de_incidencias', 'codigos_de_incidencias.id', '=', 'incidencias.codigodeincidencia_id')
            ->whereNull('incidencias.deleted_at')
            ->where('codigos_de_incidencias.code', '900')
            ->where('employees.num_empleado', $num_empleado)
            ->whereBetween('incidencias.fecha_inicio', [$fecha_inicio, $fecha_final])
            ->first();

        return $result->total ?? 0;
    }

    /**
     * Suma los días de incapacidad (códigos 53, 54, 55) de un empleado en un rango de fechas.
     */
    public static function getIncapacidadesEmpleado($num_empleado, $fecha_inicio, $fecha_final)
    {
        $result = self::selectRaw('SUM(total_dias) as total')
            ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
            ->join('codigos_de_incidencias', 'codigos_de_incidencias.id', '=', 'incidencias.codigodeincidencia_id')
            ->whereNull('incidencias.deleted_at')
            ->whereIn('codigos_de_incidencias.code', [53, 54, 55])
            ->where('employees.num_empleado', $num_empleado)
            ->whereBetween('incidencias.fecha_inicio', [$fecha_inicio, $fecha_final])
            ->first();

        return $result->total ?? 0;
    }
}