<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Agrega columnas necesarias para ADMS a la tabla equipos:
     * - serial_number: Número de serie del equipo (enviado por ADMS)
     * - last_seen: Última vez que el equipo se comunicó con el servidor
     */
    public function up(): void
    {
        $conn = app()->environment('testing') ? config('database.default') : 'biometrico';
        $db = DB::connection($conn);
        $driver = $db->getDriverName();

        if ($driver === 'sqlite') {
            \Illuminate\Support\Facades\Schema::connection($conn)->table('equipos', function (\Illuminate\Database\Schema\Blueprint $table) use ($conn) {
                if (!\Illuminate\Support\Facades\Schema::connection($conn)->hasColumn('equipos', 'serial_number')) {
                    $table->string('serial_number', 100)->nullable()->unique()->after('ip');
                }
                if (!\Illuminate\Support\Facades\Schema::connection($conn)->hasColumn('equipos', 'last_seen')) {
                    $table->dateTime('last_seen')->nullable()->after('serial_number');
                }
            });
            return;
        }

        // Verificar si las columnas ya existen (MySQL)
        $columns = $db->select("SHOW COLUMNS FROM equipos LIKE 'serial_number'");
        if (empty($columns)) {
            $db->statement("ALTER TABLE equipos ADD COLUMN serial_number VARCHAR(100) NULL AFTER ip");
            $db->statement("ALTER TABLE equipos ADD UNIQUE INDEX idx_equipos_serial_number (serial_number)");
        }

        $columns = $db->select("SHOW COLUMNS FROM equipos LIKE 'last_seen'");
        if (empty($columns)) {
            $db->statement("ALTER TABLE equipos ADD COLUMN last_seen DATETIME NULL AFTER serial_number");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $conn = app()->environment('testing') ? config('database.default') : 'biometrico';
        $db = DB::connection($conn);

        $columns = $db->select("SHOW COLUMNS FROM equipos LIKE 'serial_number'");
        if (!empty($columns)) {
            $db->statement("ALTER TABLE equipos DROP INDEX idx_equipos_serial_number, DROP COLUMN serial_number");
        }

        $columns = $db->select("SHOW COLUMNS FROM equipos LIKE 'last_seen'");
        if (!empty($columns)) {
            $db->statement("ALTER TABLE equipos DROP COLUMN last_seen");
        }
    }
};
