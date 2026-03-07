<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            // Índices sugeridos para optimizar reportes y búsquedas por empleado/fecha
            $table->index('employee_id', 'idx_incidencias_employee');
            $table->index(['fecha_inicio', 'fecha_final'], 'idx_incidencias_dates');
            $table->index(['employee_id', 'fecha_inicio', 'deleted_at'], 'idx_incidencias_lookup_v2');
            $table->index('codigodeincidencia_id', 'idx_incidencias_codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropIndex('idx_incidencias_employee');
            $table->dropIndex('idx_incidencias_dates');
            $table->dropIndex('idx_incidencias_lookup_v2');
            $table->dropIndex('idx_incidencias_codigo');
        });
    }
};
