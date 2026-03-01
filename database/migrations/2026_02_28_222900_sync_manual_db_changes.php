<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sincronizar tabla de Usuarios (Columnas que faltan en Legacy)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('name')->nullable();
            }
            if (!Schema::hasColumn('users', 'type')) {
                $table->enum('type', ['admin', 'user'])->default('user')->after('password');
            }
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->after('type');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('active');
            }
        });

        // 2. Crear tabla de Comentarios (Si no existe)
        if (!Schema::hasTable('comentarios')) {
            Schema::create('comentarios', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('employee_id');
                $table->text('comment');
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('no action');
            });
        }

        // 3. Crear tabla de Configuraciones (Si no existe)
        if (!Schema::hasTable('configurations')) {
            Schema::create('configurations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('state')->default(false);
                $table->timestamps();
            });
        }

        // 4. Crear espejo de Checadas (Si no existe)
        if (!Schema::hasTable('checadas')) {
            Schema::create('checadas', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('num_empleado');
                $table->dateTime('fecha');
                $table->string('identificador')->nullable();
                $table->timestamps();

                $table->index('num_empleado');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    // No borramos nada por seguridad
    }
};