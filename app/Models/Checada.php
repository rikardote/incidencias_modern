<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Checada extends Model
{
    protected $connection = 'biometrico';
    protected $table = 'checadas';
    protected $fillable = ['num_empleado', 'fecha', 'identificador'];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'num_empleado', 'num_empleado');
    }

    /**
     * Obtiene los registros de checadas para un centro y rango de fechas.
     * Esta versión está adaptada para la arquitectura moderna.
     */
    public function obtenerRegistros($centro, $fecha_inicio, $fecha_fin)
    {
        try {
            $connection = DB::connection('biometrico');

            // Limpiar tablas temporales
            $connection->unprepared("
                DROP TEMPORARY TABLE IF EXISTS fechas_temp;
                DROP TEMPORARY TABLE IF EXISTS dias_laborables_temp;
                DROP TEMPORARY TABLE IF EXISTS empleados_temp;
            ");

            // Crear tabla temporal de fechas
            $connection->unprepared("CREATE TEMPORARY TABLE fechas_temp (fecha DATE)");

            // Llenar fechas
            $current = new \DateTime($fecha_inicio);
            $end = new \DateTime($fecha_fin);
            while ($current <= $end) {
                $connection->table('fechas_temp')->insert(['fecha' => $current->format('Y-m-d')]);
                $current->modify('+1 day');
            }

            // Días del periodo (Todos los días)
            $connection->unprepared("
                CREATE TEMPORARY TABLE dias_periodo_temp AS
                SELECT fecha FROM fechas_temp
            ");

            // Empleados del departamento
            // IMPORTANTE: En el entorno moderno, estamos usando 'sistemas' como default.
            // La consulta asume que las tablas de sistemas están accesibles.
            $connection->unprepared("
                CREATE TEMPORARY TABLE empleados_temp AS
                SELECT
                    e.num_empleado,
                    e.name as nombre,
                    e.father_lastname as apellido_paterno,
                    e.mother_lastname as apellido_materno,
                    e.deparment_id,
                    h.horario,
                    IF(h.horario LIKE '% A %', SUBSTRING_INDEX(h.horario, ' A ', 1), NULL) as horario_entrada,
                    IF(h.horario LIKE '% A %', SUBSTRING_INDEX(h.horario, ' A ', -1), NULL) as horario_salida,
                    e.id as employee_id,
                    CASE
                        WHEN h.horario LIKE '% A %' AND TIME(SUBSTRING_INDEX(h.horario, ' A ', 1)) >= '12:00:00' THEN 1
                        ELSE 0
                    END as es_jornada_vespertina
                FROM sistemas.employees e
                INNER JOIN sistemas.horarios h ON h.id = e.horario_id
                WHERE e.deparment_id = " . (int)$centro . "
                AND e.deleted_at IS NULL
            ");

            // Consulta final
            $resultados = $connection->select("
                SELECT
                    e.num_empleado, e.nombre, e.apellido_paterno, e.apellido_materno,
                    e.deparment_id, e.horario, e.horario_entrada, e.horario_salida, e.es_jornada_vespertina,
                    f.fecha,
                    MIN(c.fecha) as primera_checada,
                    MAX(c.fecha) as ultima_checada,
                    TIME(MIN(c.fecha)) as hora_entrada,
                    TIME(MAX(c.fecha)) as hora_salida,
                    COUNT(c.fecha) as num_checadas,
                    IF(MIN(c.fecha) IS NOT NULL,
                        TIME(MIN(c.fecha)) > ADDTIME(e.horario_entrada, '00:11:00'),
                        NULL
                    ) as retardo,
                    (
                        SELECT ci.code
                        FROM sistemas.incidencias i
                        INNER JOIN sistemas.codigos_de_incidencias ci ON ci.id = i.codigodeincidencia_id
                        WHERE i.employee_id = e.employee_id
                        AND f.fecha BETWEEN DATE(i.fecha_inicio) AND DATE(i.fecha_final)
                        AND i.deleted_at IS NULL
                        LIMIT 1
                    ) as incidencia
                FROM dias_periodo_temp f
                CROSS JOIN empleados_temp e
                LEFT JOIN checadas c ON e.num_empleado = c.num_empleado
                    AND DATE(c.fecha) = f.fecha
                GROUP BY
                    e.num_empleado, e.nombre, e.apellido_paterno, e.apellido_materno,
                    e.deparment_id, e.horario, e.horario_entrada, e.horario_salida, e.es_jornada_vespertina,
                    e.employee_id, f.fecha
                ORDER BY e.num_empleado, f.fecha
            ");

            return collect($resultados);

        } catch (\Exception $e) {
            \Log::error("Error en obtenerRegistros: " . $e->getMessage());
            throw $e;
        } finally {
            $connection->unprepared("
                DROP TEMPORARY TABLE IF EXISTS fechas_temp;
                DROP TEMPORARY TABLE IF EXISTS dias_periodo_temp;
                DROP TEMPORARY TABLE IF EXISTS empleados_temp;
            ");
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
