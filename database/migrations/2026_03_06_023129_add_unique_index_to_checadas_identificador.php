<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Agrega índice UNIQUE a la columna 'identificador' de checadas.
     * Esto permite:
     * - Búsquedas por identificador en O(log n) en lugar de O(n)
     * - Protección a nivel de DB contra duplicados
     * - Uso de INSERT IGNORE / insertOrIgnore para batch inserts
     * 
     * NOTA: Esta migración es idempotente. Si el índice ya fue creado
     *       manualmente via SQL, se marca como ejecutada sin errores.
     */
    public function up(): void
    {
        $conn = app()->environment('testing') ? config('database.default') : 'biometrico';
        $db = DB::connection($conn);

        // Verificar si el índice ya existe
        $indices = $db->select("SHOW INDEX FROM checadas WHERE Key_name = 'idx_checadas_identificador_unique'");
        
        if (!empty($indices)) {
            // Ya se aplicó manualmente, no hacer nada
            return;
        }

        // 1. Asignar identificadores únicos a registros sin identificador
        $db->statement("
            UPDATE checadas
            SET identificador = CONCAT('legacy_', id)
            WHERE identificador IS NULL OR identificador = ''
        ");

        // 2. Eliminar duplicados (mantener el de menor ID)
        $db->statement("
            CREATE TEMPORARY TABLE checadas_keep AS
            SELECT MIN(id) as id FROM checadas GROUP BY identificador
        ");

        $db->statement("
            DELETE FROM checadas WHERE id NOT IN (SELECT id FROM checadas_keep)
        ");

        $db->statement("DROP TEMPORARY TABLE IF EXISTS checadas_keep");

        // 3. Agregar índice UNIQUE con ALTER TABLE directo
        $db->statement("
            ALTER TABLE checadas
            MODIFY identificador VARCHAR(255) NOT NULL DEFAULT '',
            ADD UNIQUE INDEX idx_checadas_identificador_unique (identificador)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $conn = app()->environment('testing') ? config('database.default') : 'biometrico';
        $db = DB::connection($conn);

        $indices = $db->select("SHOW INDEX FROM checadas WHERE Key_name = 'idx_checadas_identificador_unique'");
        
        if (!empty($indices)) {
            $db->statement("
                ALTER TABLE checadas
                DROP INDEX idx_checadas_identificador_unique,
                MODIFY identificador VARCHAR(255) NULL
            ");
        }
    }
};
