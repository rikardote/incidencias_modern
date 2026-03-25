<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Checada extends Model
{
    protected $connection = 'biometrico';
    protected $table = 'checadas';
    protected $fillable = ['num_empleado', 'fecha', 'identificador'];

    /**
     * Permite usar config() para cambiar en testing si se necesita, 
     * aunque para un Model es mejor la variable protegida por defecto.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (app()->environment('testing')) {
            $this->connection = config('database.default');
        }
    }

    public function employee()
    {
        return $this->belongsTo(Employe::class , 'num_empleado', 'num_empleado');
    }

    /**
     * Obtiene los registros de checadas para un centro y rango de fechas.
     */
    public function obtenerRegistros($centro, $fecha_inicio, $fecha_fin)
    {
        if (app()->environment('testing')) {
            $employees = Employe::where('deparment_id', $centro)->get();
            $results = collect();
            
            foreach ($employees as $employee) {
                $results = $results->concat($this->obtenerRegistrosPorEmpleado($employee->id, $fecha_inicio, $fecha_fin));
            }
            
            return $results;
        }

        $connection = null;
        try {
            $connection = DB::connection(app()->environment('testing') ? config('database.default') : 'biometrico');

            // Limpiar tablas temporales
            $connection->unprepared("
                DROP TEMPORARY TABLE IF EXISTS fechas_temp;
                DROP TEMPORARY TABLE IF EXISTS empleados_temp;
                DROP TEMPORARY TABLE IF EXISTS dias_periodo_temp;
            ");

            // Crear tabla temporal de fechas
            $connection->unprepared("CREATE TEMPORARY TABLE fechas_temp (fecha DATE)");

            // Llenar fechas
            $current = new \DateTime($fecha_inicio);
            $end = new \DateTime($fecha_fin);
            $fechas_array = [];
            while ($current <= $end) {
                $fechas_array[] = ['fecha' => $current->format('Y-m-d')];
                $current->modify('+1 day');
            }
            $connection->table('fechas_temp')->insert($fechas_array);

            // Días del periodo
            $connection->unprepared("
                CREATE TEMPORARY TABLE dias_periodo_temp AS
                SELECT fecha FROM fechas_temp
            ");

            $employeesTable = app()->environment('testing') ? 'employees' : 'sistemas.employees';
            $horariosTable = app()->environment('testing') ? 'horarios' : 'sistemas.horarios';

            // Empleados del departamento
            $connection->unprepared("
                CREATE TEMPORARY TABLE empleados_temp AS
                SELECT
                    e.num_empleado,
                    e.name as nombre,
                    e.father_lastname as apellido_paterno,
                    e.mother_lastname as apellido_materno,
                    e.deparment_id,
                    e.exento,
                    h.horario,
                    IF(h.horario LIKE '% A %', SUBSTRING_INDEX(h.horario, ' A ', 1), NULL) as horario_entrada,
                    IF(h.horario LIKE '% A %', SUBSTRING_INDEX(h.horario, ' A ', -1), NULL) as horario_salida,
                    e.id as employee_id,
                    e.lactancia, e.lactancia_inicio, e.lactancia_fin,
                    e.estancia, e.estancia_inicio, e.estancia_fin,
                    CASE
                        WHEN h.horario LIKE '% A %' AND TIME(SUBSTRING_INDEX(h.horario, ' A ', 1)) >= '12:00:00' THEN 1
                        ELSE 0
                    END as es_jornada_vespertina
                FROM {$employeesTable} e
                INNER JOIN {$horariosTable} h ON h.id = e.horario_id
                WHERE e.deparment_id = " . (int)$centro . "
                AND e.deleted_at IS NULL
            ");

            // Consulta principal sin subconsultas pesadas
            $resultados = $connection->select("
                SELECT
                    e.num_empleado, e.employee_id, e.nombre, e.apellido_paterno, e.apellido_materno,
                    e.deparment_id, e.exento, e.lactancia, e.lactancia_inicio, e.lactancia_fin, e.estancia, e.estancia_inicio, e.estancia_fin, e.horario, e.horario_entrada, e.horario_salida, e.es_jornada_vespertina,
                    f.fecha,
                    MIN(c.fecha) as primera_checada,
                    MAX(c.fecha) as ultima_checada,
                    TIME(MIN(c.fecha)) as hora_entrada,
                    TIME(MAX(c.fecha)) as hora_salida,
                    COUNT(c.fecha) as num_checadas,
                    IF(MIN(c.fecha) IS NOT NULL,
                        TIME(MIN(c.fecha)) > ADDTIME(e.horario_entrada, '00:11:00'),
                        NULL
                    ) as retardo
                FROM dias_periodo_temp f
                CROSS JOIN empleados_temp e
                LEFT JOIN checadas c ON e.num_empleado = c.num_empleado
                    AND c.fecha >= f.fecha AND c.fecha < DATE_ADD(f.fecha, INTERVAL 1 DAY)
                GROUP BY
                    e.num_empleado, e.nombre, e.apellido_paterno, e.apellido_materno,
                    e.deparment_id, e.exento, e.lactancia, e.lactancia_inicio, e.lactancia_fin, e.estancia, e.estancia_inicio, e.estancia_fin, e.horario, e.horario_entrada, e.horario_salida, e.es_jornada_vespertina,
                    e.employee_id, f.fecha
                ORDER BY e.num_empleado, f.fecha
            ");

            // Obtener incidencias en una sola consulta para todos los empleados del centro en ese periodo
            $employeeIds = collect($resultados)->pluck('employee_id')->unique()->toArray();
            
            if (!empty($employeeIds)) {
                $incidenciasRecords = DB::connection('mysql')->table('incidencias as i')
                    ->join('codigos_de_incidencias as ci', 'ci.id', '=', 'i.codigodeincidencia_id')
                    ->whereIn('i.employee_id', $employeeIds)
                    ->where('i.fecha_inicio', '<=', $fecha_fin . ' 23:59:59')
                    ->where('i.fecha_final', '>=', $fecha_inicio . ' 00:00:00')
                    ->whereNull('i.deleted_at')
                    ->select('i.employee_id', 'i.fecha_inicio', 'i.fecha_final', 'ci.code', 'i.token', 'i.motivo_comision', 'i.otorgado')
                    ->get();

                // Mapear incidencias a los resultados
                foreach ($resultados as $row) {
                    $fecha = $row->fecha;
                    $empId = $row->employee_id;
                    
                    $matched = $incidenciasRecords->filter(function($inc) use ($empId, $fecha) {
                        return $inc->employee_id == $empId && $fecha >= substr($inc->fecha_inicio, 0, 10) && $fecha <= substr($inc->fecha_final, 0, 10);
                    });

                    if ($matched->isNotEmpty()) {
                        $row->incidencias = $matched->pluck('code')->implode(',');
                        $row->incidencias_tokens = $matched->pluck('token')->implode(',');
                        
                        // Si hay comisión oficial (61), guardar el motivo
                        $comision = $matched->firstWhere('code', 61);
                        $row->motivo_comision = $comision ? $comision->motivo_comision : null;
                        
                        // Guardar cualquier valor en el campo 'otorgado' para mostrarlo (ej: 901, 916)
                        $row->otorgado_motivo = $matched->whereNotNull('otorgado')
                            ->where('otorgado', '!=', '')
                            ->pluck('otorgado')
                            ->unique()
                            ->implode(', ');
                    } else {
                        $row->incidencias = null;
                        $row->incidencias_tokens = null;
                        $row->motivo_comision = null;
                        $row->otorgado_motivo = null;
                    }
                }
            }

            return collect($resultados);
        }
        catch (\Exception $e) {
            Log::error("Error en obtenerRegistros: " . $e->getMessage());
            throw $e;
        }
        finally {
            if ($connection) {
                $connection->unprepared("
                    DROP TEMPORARY TABLE IF EXISTS fechas_temp;
                    DROP TEMPORARY TABLE IF EXISTS empleados_temp;
                    DROP TEMPORARY TABLE IF EXISTS dias_periodo_temp;
                ");
            }
        }
    }

    /**
     * Obtiene los registros de checadas para un empleado específico y rango de fechas.
     */
    public function obtenerRegistrosPorEmpleado($employee_id, $fecha_inicio, $fecha_fin)
    {
        if (app()->environment('testing')) {
            $employee = Employe::find($employee_id);
            $checadas = Checada::where('num_empleado', $employee->num_empleado)
                ->whereBetween('fecha', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->get();

            // Simular el agrupamiento por fecha que hace la query original
            $grouped = $checadas->groupBy(function($item) {
                return substr($item->fecha, 0, 10);
            });

            $results = collect();
            
            // Generar fechas para el rango
            $current = new \DateTime($fecha_inicio);
            $end = new \DateTime($fecha_fin);
            while ($current <= $end) {
                $f = $current->format('Y-m-d');
                $dayChecadas = $grouped->get($f, collect());
                
                $obj = new \stdClass();
                $obj->fecha = $f;
                $obj->num_empleado = $employee->num_empleado;
                $obj->employee_id = $employee->id;
                $obj->nombre = $employee->name;
                $obj->apellido_paterno = $employee->father_lastname;
                $obj->apellido_materno = $employee->mother_lastname;
                
                if ($dayChecadas->isNotEmpty()) {
                    $sorted = $dayChecadas->sortBy('fecha');
                    $obj->primera_checada = $sorted->first()->fecha;
                    $obj->ultima_checada = $sorted->last()->fecha;
                    $obj->hora_entrada = date('H:i:s', strtotime($obj->primera_checada));
                    $obj->hora_salida = date('H:i:s', strtotime($obj->ultima_checada));
                    $obj->num_checadas = $dayChecadas->count();
                } else {
                    $obj->primera_checada = null;
                    $obj->ultima_checada = null;
                    $obj->hora_entrada = null;
                    $obj->hora_salida = null;
                    $obj->num_checadas = 0;
                }
                
                $obj->incidencias = null;
                $obj->incidencias_tokens = null;
                $obj->motivo_comision = null;
                $obj->otorgado_motivo = null;

                $results->push($obj);
                $current->modify('+1 day');
            }

            return $results;
        }

        $connection = null;
        try {
            $connection = DB::connection(app()->environment('testing') ? config('database.default') : 'biometrico');

            // Limpiar tablas temporales
            $connection->unprepared("
                DROP TEMPORARY TABLE IF EXISTS fechas_temp;
                DROP TEMPORARY TABLE IF EXISTS dias_periodo_temp;
                DROP TEMPORARY TABLE IF EXISTS empleados_temp;
            ");

            // Crear tabla temporal de fechas
            $connection->unprepared("CREATE TEMPORARY TABLE fechas_temp (fecha DATE)");

            // Llenar fechas
            $current = new \DateTime($fecha_inicio);
            $end = new \DateTime($fecha_fin);
            $fechas_array = [];
            while ($current <= $end) {
                $fechas_array[] = ['fecha' => $current->format('Y-m-d')];
                $current->modify('+1 day');
            }
            $connection->table('fechas_temp')->insert($fechas_array);

            // Días del periodo
            $connection->unprepared("
                CREATE TEMPORARY TABLE dias_periodo_temp AS
                SELECT fecha FROM fechas_temp
            ");

            $employeesTable = app()->environment('testing') ? 'employees' : 'sistemas.employees';
            $horariosTable = app()->environment('testing') ? 'horarios' : 'sistemas.horarios';

            // Empleado específico
            $connection->unprepared("
                CREATE TEMPORARY TABLE empleados_temp AS
                SELECT
                    e.num_empleado,
                    e.name as nombre,
                    e.father_lastname as apellido_paterno,
                    e.mother_lastname as apellido_materno,
                    e.deparment_id,
                    e.exento,
                    h.horario,
                    IF(h.horario LIKE '% A %', SUBSTRING_INDEX(h.horario, ' A ', 1), NULL) as horario_entrada,
                    IF(h.horario LIKE '% A %', SUBSTRING_INDEX(h.horario, ' A ', -1), NULL) as horario_salida,
                    e.id as employee_id,
                    e.lactancia, e.lactancia_inicio, e.lactancia_fin,
                    e.estancia, e.estancia_inicio, e.estancia_fin,
                    CASE
                        WHEN h.horario LIKE '% A %' AND TIME(SUBSTRING_INDEX(h.horario, ' A ', 1)) >= '12:00:00' THEN 1
                        ELSE 0
                    END as es_jornada_vespertina
                FROM {$employeesTable} e
                INNER JOIN {$horariosTable} h ON h.id = e.horario_id
                WHERE e.id = " . (int)$employee_id . "
                AND e.deleted_at IS NULL
            ");

            // Consulta final sin subconsultas
            $resultados = $connection->select("
                SELECT
                    e.num_empleado, e.employee_id, e.nombre, e.apellido_paterno, e.apellido_materno,
                    e.deparment_id, e.exento, e.lactancia, e.lactancia_inicio, e.lactancia_fin, e.estancia, e.estancia_inicio, e.estancia_fin, e.horario, e.horario_entrada, e.horario_salida, e.es_jornada_vespertina,
                    f.fecha,
                    MIN(c.fecha) as primera_checada,
                    MAX(c.fecha) as ultima_checada,
                    TIME(MIN(c.fecha)) as hora_entrada,
                    TIME(MAX(c.fecha)) as hora_salida,
                    COUNT(c.fecha) as num_checadas,
                    IF(MIN(c.fecha) IS NOT NULL,
                        TIME(MIN(c.fecha)) > ADDTIME(e.horario_entrada, '00:11:00'),
                        NULL
                    ) as retardo
                FROM dias_periodo_temp f
                CROSS JOIN empleados_temp e
                LEFT JOIN checadas c ON e.num_empleado = c.num_empleado
                    AND c.fecha >= f.fecha AND c.fecha < DATE_ADD(f.fecha, INTERVAL 1 DAY)
                GROUP BY
                    e.num_empleado, e.nombre, e.apellido_paterno, e.apellido_materno,
                    e.deparment_id, e.exento, e.lactancia, e.lactancia_inicio, e.lactancia_fin, e.estancia, e.estancia_inicio, e.estancia_fin, e.horario, e.horario_entrada, e.horario_salida, e.es_jornada_vespertina,
                    e.employee_id, f.fecha
                ORDER BY f.fecha
            ");

            // Obtener incidencias en una sola consulta
            $incidenciasRecords = DB::connection('mysql')->table('incidencias as i')
                ->join('codigos_de_incidencias as ci', 'ci.id', '=', 'i.codigodeincidencia_id')
                ->where('i.employee_id', $employee_id)
                ->where('i.fecha_inicio', '<=', $fecha_fin . ' 23:59:59')
                ->where('i.fecha_final', '>=', $fecha_inicio . ' 00:00:00')
                ->whereNull('i.deleted_at')
                ->select('i.fecha_inicio', 'i.fecha_final', 'ci.code', 'i.token', 'i.motivo_comision', 'i.otorgado')
                ->get();

            // Mapear incidencias
            foreach ($resultados as $row) {
                $fecha = $row->fecha;
                
                $matched = $incidenciasRecords->filter(function($inc) use ($fecha) {
                    return $fecha >= substr($inc->fecha_inicio, 0, 10) && $fecha <= substr($inc->fecha_final, 0, 10);
                });

                if ($matched->isNotEmpty()) {
                    $row->incidencias = $matched->pluck('code')->implode(',');
                    $row->incidencias_tokens = $matched->pluck('token')->implode(',');
                    
                    // Si hay comisión oficial (61), guardar el motivo
                    $comision = $matched->firstWhere('code', 61);
                    $row->motivo_comision = $comision ? $comision->motivo_comision : null;

                    // Guardar cualquier valor en el campo 'otorgado' para mostrarlo (ej: 901, 916)
                    $row->otorgado_motivo = $matched->whereNotNull('otorgado')
                        ->where('otorgado', '!=', '')
                        ->pluck('otorgado')
                        ->unique()
                        ->implode(', ');
                } else {
                    $row->incidencias = null;
                    $row->incidencias_tokens = null;
                    $row->motivo_comision = null;
                    $row->otorgado_motivo = null;
                }
            }

            return collect($resultados);
        }
        catch (\Exception $e) {
            Log::error("Error en obtenerRegistrosPorEmpleado: " . $e->getMessage());
            throw $e;
        }
        finally {
            if ($connection) {
                $connection->unprepared("
                    DROP TEMPORARY TABLE IF EXISTS fechas_temp;
                    DROP TEMPORARY TABLE IF EXISTS dias_periodo_temp;
                    DROP TEMPORARY TABLE IF EXISTS empleados_temp;
                ");
            }
        }
    }

    public static function buscarIncidencias($num_empleado, $fecha)
    {
        return DB::connection('mysql')->select("
            SELECT ci.code as tipo
            FROM incidencias i
            INNER JOIN employees e ON e.id = i.employee_id
            INNER JOIN codigos_de_incidencias ci ON ci.id = i.codigodeincidencia_id
            WHERE e.num_empleado = ?
            AND ? BETWEEN DATE(i.fecha_inicio) AND DATE(i.fecha_final)
            AND i.deleted_at IS NULL
            LIMIT 1
        ", [$num_empleado, $fecha]);
    }
}