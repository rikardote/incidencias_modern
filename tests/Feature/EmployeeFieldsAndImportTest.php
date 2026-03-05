<?php

namespace Tests\Feature;

use App\Models\Employe;
use App\Models\User;
use App\Models\Department;
use App\Models\Puesto;
use App\Models\Horario;
use App\Models\Jornada;
use App\Models\Condicion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class EmployeeFieldsAndImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear datos base necesarios para las relaciones de empleados
        Department::create(['code' => '100', 'description' => 'TEST DEPT']);
        Puesto::create(['puesto' => 'TEST PUESTO']);
        Horario::create(['horario' => 'TEST HORARIO']);
        Jornada::create(['jornada' => 'TEST JORNADA']);
        Condicion::create(['condicion' => 'TEST CONDICION']);
    }

    /** @test */
    public function employee_model_mutates_curp_and_rfc_to_uppercase()
    {
        $employee = Employe::create([
            'num_empleado' => '012345',
            'name' => 'John',
            'father_lastname' => 'Doe',
            'mother_lastname' => 'Smith',
            'curp' => 'abcd123456hqrxyz01',
            'rfc' => 'abcd123456abc',
            'deparment_id' => 1,
            'condicion_id' => 1,
            'puesto_id' => 1,
            'horario_id' => 1,
            'jornada_id' => 1,
        ]);

        $this->assertEquals('ABCD123456HQRXYZ01', $employee->curp);
        $this->assertEquals('ABCD123456ABC', $employee->rfc);
    }

    /** @test */
    public function import_curp_rfc_command_works_correctly()
    {
        // Crear un empleado previo
        $employee = Employe::create([
            'num_empleado' => '099999',
            'name' => 'TEST',
            'father_lastname' => 'USER',
            'mother_lastname' => 'IMPORT',
            'deparment_id' => 1,
            'condicion_id' => 1,
            'puesto_id' => 1,
            'horario_id' => 1,
            'jornada_id' => 1,
        ]);

        // Crear un archivo CSV temporal
        $csvContent = "num_empleado,curp,rfc\n099999,VECJ880101HDFRRR01,VECJ880101ABC";
        $filePath = tempnam(sys_get_temp_dir(), 'test_import') . '.csv';
        file_put_contents($filePath, $csvContent);

        // Ejecutar el comando
        $this->artisan('import:curp-rfc', ['file' => $filePath])
            ->expectsOutput('Iniciando importación...')
            ->expectsOutput('Importación terminada.')
            ->assertExitCode(0);

        // Verificar que se actualizó
        $employee->refresh();
        $this->assertEquals('VECJ880101HDFRRR01', $employee->curp);
        $this->assertEquals('VECJ880101ABC', $employee->rfc);

        @unlink($filePath);
    }

    /** @test */
    public function admin_can_save_curp_and_rfc_via_livewire()
    {
        $admin = User::factory()->create(['type' => 'admin']);
        
        Livewire::actingAs($admin)
            ->test(\App\Livewire\SearchEmployees::class)
            ->set('num_empleado', '55555')
            ->set('name', 'MARIA')
            ->set('father_lastname', 'GARCIA')
            ->set('mother_lastname', 'LOPEZ')
            ->set('curp', 'marg900101hqrxyz01')
            ->set('rfc', 'marg900101abc')
            ->set('deparment_id', 1)
            ->set('puesto_id', 1)
            ->set('horario_id', 1)
            ->set('jornada_id', 1)
            ->set('condicion_id', 1)
            ->set('fecha_ingreso', '2020-01-01')
            ->call('save');

        $this->assertDatabaseHas('employees', [
            'num_empleado' => '055555',
            'curp' => 'MARG900101HQRXYZ01',
            'rfc' => 'MARG900101ABC'
        ]);
    }
}
