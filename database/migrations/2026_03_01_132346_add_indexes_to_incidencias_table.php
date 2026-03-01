<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            // Índices de búsqueda frecuente
            $table->index('employee_id');
            $table->index('token');
            $table->index('created_at');

            // Índice compuesto para el Log del Dashboard (Muy importante)
            // Acelera la consulta de "últimos registros no borrados"
            $table->index(['deleted_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['token']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['deleted_at', 'created_at']);
        });
    }
};