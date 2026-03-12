<?php

namespace Tests\Feature\Incidencias;

use App\Constants\Incidencias as Inc;
use App\Models\Employe;
use App\Models\CodigoDeIncidencia;
use App\Models\Qna;
use App\Models\User;
use App\Services\Incidencias\IncidenciasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RulesValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(IncidenciasService::class);
        $this->admin = User::factory()->create(['type' => 'admin']);
        $this->actingAs($this->admin);

        // Crear QNA activa para pruebas
        Qna::create([
            'qna' => 1,
            'year' => 2026,
            'active' => '1'
        ]);

        // Crear Condiciones necesarias
        \App\Models\Condicion::create(['id' => Inc::CONDICION_BASE, 'condicion' => 'BASE']);
        \App\Models\Condicion::create(['id' => Inc::CONDICION_CONFIANZA, 'condicion' => 'CONFIANZA']);

        // Crear Jornada necesaria
        \App\Models\Jornada::create(['id' => 1, 'description' => 'MATUTINO']);
    }

    /** @test */
    public function only_base_personnel_can_capture_txt_code_900()
    {
        // 1. Crear códigos necesarios
        $codigoTxt = CodigoDeIncidencia::create([
            'code' => '900',
            'description' => 'T.X.T.'
        ]);

        // 2. Crear Empleado de BASE (condicion_id = 1)
        $empleadoBase = Employe::create([
            'num_empleado' => '000001',
            'name' => 'JUAN',
            'father_lastname' => 'BASE',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_BASE,
            'jornada_id' => 1, // Normal
            'active' => 1
        ]);

        // 3. Crear Empleado de CONFIANZA (condicion_id = 2)
        $empleadoConfianza = Employe::create([
            'num_empleado' => '000002',
            'name' => 'PEDRO',
            'father_lastname' => 'CONFIANZA',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_CONFIANZA,
            'jornada_id' => 1,
            'active' => 1
        ]);

        $dataBase = [
            'empleado_id' => $empleadoBase->id,
            'codigo' => $codigoTxt->id,
            'datepicker_inicial' => '2026-01-01',
            'datepicker_final' => '2026-01-01',
            'autoriza_txt' => 'JEFE TEST',
            'cobertura_txt' => 'SUSTITUTO TEST',
            'token' => 'token_1'
        ];

        // El de base DEBE poder capturar
        $this->service->crearIncidencias($dataBase);
        $this->assertDatabaseHas('incidencias', [
            'employee_id' => $empleadoBase->id,
            'codigodeincidencia_id' => $codigoTxt->id
        ]);

        // El de confianza NO DEBE poder capturar TXT
        $dataConfianza = $dataBase;
        $dataConfianza['empleado_id'] = $empleadoConfianza->id;
        $dataConfianza['token'] = 'token_2';

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Solo el personal de base puede cubrir T.X.T.');

        $this->service->crearIncidencias($dataConfianza);
    }

    /** @test */
    public function code_14_onomastico_can_only_be_captured_once_per_year_for_base_personnel()
    {
        $codigoOnomastico = CodigoDeIncidencia::create([
            'code' => '14',
            'description' => 'ONOMASTICO'
        ]);

        $empleado = Employe::create([
            'num_empleado' => '000003',
            'name' => 'LUIS',
            'father_lastname' => 'ONOMA',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_BASE,
            'active' => 1
        ]);

        // Crear QNA para mayo 2026 (QNA 9)
        Qna::create(['qna' => 9, 'year' => 2026, 'active' => '1']);

        $data1 = [
            'empleado_id' => $empleado->id,
            'codigo' => $codigoOnomastico->id,
            'datepicker_inicial' => '2026-05-10',
            'datepicker_final' => '2026-05-10',
            'token' => 'token_onoma_1'
        ];

        // Primera captura: OK
        $this->service->crearIncidencias($data1);
        $this->assertDatabaseHas('incidencias', [
            'employee_id' => $empleado->id,
            'codigodeincidencia_id' => $codigoOnomastico->id
        ]);

        // Segunda captura en el mismo año: ERROR
        $data2 = $data1;
        $data2['datepicker_inicial'] = '2026-05-11';
        $data2['datepicker_final'] = '2026-05-11';
        $data2['token'] = 'token_onoma_2';

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El empleado ya cuenta con un código 14 (Onomástico) registrado en el año 2026.");

        $this->service->crearIncidencias($data2);
    }

    /** @test */
    public function code_14_onomastico_allowed_in_different_years()
    {
        $codigoOnomastico = CodigoDeIncidencia::create([
            'code' => '14',
            'description' => 'ONOMASTICO'
        ]);

        $empleado = Employe::create([
            'num_empleado' => '000004',
            'name' => 'MARIA',
            'father_lastname' => 'ONOMA',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_BASE,
            'active' => 1
        ]);

        // Crear QNA para mayo 2025 (QNA 9) y mayo 2026 (QNA 9)
        Qna::create(['qna' => 9, 'year' => 2025, 'active' => '1']);
        Qna::create(['qna' => 9, 'year' => 2026, 'active' => '1']);

        // Captura 2025
        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigoOnomastico->id,
            'datepicker_inicial' => '2025-05-10',
            'datepicker_final' => '2025-05-10',
            'token' => 'token_2025'
        ]);

        // Captura 2026
        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigoOnomastico->id,
            'datepicker_inicial' => '2026-05-10',
            'datepicker_final' => '2026-05-10',
            'token' => 'token_2026'
        ]);

        $incidenciasCount = \App\Models\Incidencia::where('employee_id', $empleado->id)
            ->where('codigodeincidencia_id', $codigoOnomastico->id)
            ->count();

        $this->assertEquals(2, $incidenciasCount);
    }

    /** @test */
    public function pase_salida_allows_overlap_with_specific_codes_for_non_mat_vesp()
    {
        Qna::create(['qna' => 11, 'year' => 2026, 'active' => '1']);
        $periodo = \App\Models\Periodo::create(['name' => '2026-1']);

        $codigoPase = CodigoDeIncidencia::create(['code' => '905', 'description' => 'PASE DE SALIDA']);
        $codigoVacaciones = CodigoDeIncidencia::create(['code' => '60', 'description' => 'VACACIONES']);

        $empleado = Employe::create([
            'num_empleado' => '100001',
            'name' => 'NON_MAT_VESP',
            'father_lastname' => 'TEST',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_BASE,
            'jornada_id' => 1,
            'active' => 1
        ]);

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigoVacaciones->id,
            'datepicker_inicial' => '2026-06-01',
            'datepicker_final' => '2026-06-01',
            'periodo_id' => $periodo->id,
            'token' => 'token_vac'
        ]);

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigoPase->id,
            'datepicker_inicial' => '2026-06-01',
            'datepicker_final' => '2026-06-01',
            'token' => 'token_pase'
        ]);

        $this->assertDatabaseHas('incidencias', [
            'employee_id' => $empleado->id,
            'codigodeincidencia_id' => $codigoPase->id
        ]);
    }

    /** @test */
    public function pase_salida_prevents_overlap_for_matutino_vespertino()
    {
        Qna::create(['qna' => 11, 'year' => 2026, 'active' => '1']);
        $periodo = \App\Models\Periodo::create(['name' => '2026-1']);

        \Illuminate\Support\Facades\DB::table('jornadas')->insert(['id' => 14, 'jornada' => 'MAT/VESP']);

        $codigoPase = CodigoDeIncidencia::create(['code' => '905', 'description' => 'PASE DE SALIDA']);
        $codigoVacaciones = CodigoDeIncidencia::create(['code' => '60', 'description' => 'VACACIONES']);

        $empleado = Employe::create([
            'num_empleado' => '100002',
            'name' => 'MATUTINO_EMP',
            'father_lastname' => 'TEST',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_BASE,
            'jornada_id' => 14,
            'active' => 1
        ]);

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigoVacaciones->id,
            'datepicker_inicial' => '2026-06-02',
            'datepicker_final' => '2026-06-02',
            'periodo_id' => $periodo->id,
            'token' => 'token_vac2'
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Incidencia Traslape');

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigoPase->id,
            'datepicker_inicial' => '2026-06-02',
            'datepicker_final' => '2026-06-02',
            'token' => 'token_pase2'
        ]);
    }

    /** @test */
    public function limits_08_and_09_to_two_per_quincena()
    {
        $this->markTestSkipped('Regla desactivada temporalmente hasta aprobación.');
        // 1. Create codes
        $codigo08 = CodigoDeIncidencia::create(['code' => '08', 'description' => 'OMISION 08']);
        $codigo09 = CodigoDeIncidencia::create(['code' => '09', 'description' => 'OMISION 09']);

        // 2. Create qna and employee
        Qna::create(['qna' => 12, 'year' => 2026, 'active' => '1']);
        $empleado = Employe::create([
            'num_empleado' => '100003',
            'name' => 'LIMIT',
            'father_lastname' => 'TEST',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_BASE,
            'jornada_id' => 1,
            'active' => 1
        ]);

        // 3. Capture one 08
        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigo08->id,
            'datepicker_inicial' => '2026-06-16',
            'datepicker_final' => '2026-06-16',
            'token' => 'token_08_1'
        ]);

        // 4. Capture one 09 (Total = 2)
        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigo09->id,
            'datepicker_inicial' => '2026-06-17',
            'datepicker_final' => '2026-06-17',
            'token' => 'token_09_1'
        ]);

        $this->assertDatabaseHas('incidencias', ['token' => 'token_08_1']);
        $this->assertDatabaseHas('incidencias', ['token' => 'token_09_1']);

        // 5. Third attempt (another 08) should fail
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('No se permite registrar más de 2 incidencias con clave 08 o 09 (combinadas) en la misma quincena.');

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigo08->id,
            'datepicker_inicial' => '2026-06-18', // QNA 12
            'datepicker_final' => '2026-06-18',
            'token' => 'token_08_fail'
        ]);
    }

    /** @test */
    public function allows_08_09_if_in_different_quincenas()
    {
        $this->markTestSkipped('Regla desactivada temporalmente hasta aprobación.');
        $codigo08 = CodigoDeIncidencia::create(['code' => '08', 'description' => 'OMISION 08']);

        Qna::create(['qna' => 13, 'year' => 2026, 'active' => '1']);
        Qna::create(['qna' => 14, 'year' => 2026, 'active' => '1']);
        
        $empleado = Employe::create([
            'num_empleado' => '100004',
            'name' => 'LIMIT2',
            'father_lastname' => 'TEST',
            'mother_lastname' => 'TEST',
            'condicion_id' => Inc::CONDICION_BASE,
            'jornada_id' => 1,
            'active' => 1
        ]);

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigo08->id,
            'datepicker_inicial' => '2026-07-01', // QNA 13
            'datepicker_final' => '2026-07-01',
            'token' => 'token_08_qna13_1'
        ]);

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigo08->id,
            'datepicker_inicial' => '2026-07-02', // QNA 13
            'datepicker_final' => '2026-07-02',
            'token' => 'token_08_qna13_2'
        ]);

        $this->service->crearIncidencias([
            'empleado_id' => $empleado->id,
            'codigo' => $codigo08->id,
            'datepicker_inicial' => '2026-07-16', // QNA 14
            'datepicker_final' => '2026-07-16',
            'token' => 'token_08_qna14_1'
        ]);

        $this->assertDatabaseHas('incidencias', ['token' => 'token_08_qna14_1']);
    }
}
