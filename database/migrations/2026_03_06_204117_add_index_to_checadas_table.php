<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $conn = app()->environment('testing') ? config('database.default') : 'biometrico';
        $db = DB::connection($conn);

        // Verificar si el índice ya existe
        $indices = $db->select("SHOW INDEX FROM checadas WHERE Key_name = 'idx_checadas_lookup'");
        
        if (empty($indices)) {
            $db->statement("ALTER TABLE checadas ADD INDEX idx_checadas_lookup (num_empleado, fecha)");
        }
    }

    public function down(): void
    {
        $conn = app()->environment('testing') ? config('database.default') : 'biometrico';
        $db = DB::connection($conn);

        $indices = $db->select("SHOW INDEX FROM checadas WHERE Key_name = 'idx_checadas_lookup'");
        
        if (!empty($indices)) {
            $db->statement("ALTER TABLE checadas DROP INDEX idx_checadas_lookup");
        }
    }
};
