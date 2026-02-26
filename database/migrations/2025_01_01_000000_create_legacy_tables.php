<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qnas', function (Blueprint $table) {
            $table->id();
            $table->integer('qna');
            $table->integer('year');
            $table->string('description')->nullable();
            $table->enum('active', ['0', '1'])->default('1');
            $table->date('cierre')->nullable();
            $table->timestamps();
        });

        Schema::create('deparments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('deparment_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deparment_id')->constrained('deparments')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->string('puesto')->nullable();
            $table->timestamps();
        });

        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('jornadas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('condiciones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('num_empleado')->unique();
            $table->string('name');
            $table->string('father_lastname');
            $table->string('mother_lastname');
            $table->foreignId('deparment_id')->nullable()->constrained('deparments');
            $table->foreignId('condicion_id')->nullable()->constrained('condiciones');
            $table->foreignId('puesto_id')->nullable()->constrained('puestos');
            $table->foreignId('horario_id')->nullable()->constrained('horarios');
            $table->foreignId('jornada_id')->nullable()->constrained('jornadas');
            $table->string('num_plaza')->nullable();
            $table->string('num_seguro')->nullable();
            $table->boolean('lactancia')->default(false);
            $table->date('lactancia_inicio')->nullable();
            $table->date('lactancia_fin')->nullable();
            $table->boolean('comisionado')->default(false);
            $table->boolean('estancia')->default(false);
            $table->enum('active', ['0', '1'])->default('1');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('codigos_de_incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('grupo')->nullable();
            $table->timestamps();
        });

        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('periodo')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });

        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qna_id')->constrained('qnas');
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('fecha_inicio');
            $table->date('fecha_final');
            $table->foreignId('codigodeincidencia_id')->constrained('codigos_de_incidencias');
            $table->foreignId('periodo_id')->nullable()->constrained('periodos');
            $table->string('token')->nullable();
            $table->text('diagnostico')->nullable();
            $table->integer('medico_id')->nullable();
            $table->date('fecha_expedida')->nullable();
            $table->string('num_licencia')->nullable();
            $table->integer('otorgado')->nullable();
            $table->integer('pendientes')->nullable();
            $table->text('becas_comments')->nullable();
            $table->dateTime('fecha_capturado')->nullable();
            $table->string('cobertura_txt')->nullable();
            $table->integer('horas_otorgadas')->nullable();
            $table->string('autoriza_txt')->nullable();
            $table->integer('total_dias')->default(0);
            $table->string('capturado_por')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
        Schema::dropIfExists('periodos');
        Schema::dropIfExists('codigos_de_incidencias');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('condiciones');
        Schema::dropIfExists('jornadas');
        Schema::dropIfExists('horarios');
        Schema::dropIfExists('puestos');
        Schema::dropIfExists('deparment_user');
        Schema::dropIfExists('deparments');
        Schema::dropIfExists('qnas');
    }
};
