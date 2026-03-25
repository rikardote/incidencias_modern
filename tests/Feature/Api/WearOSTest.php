<?php

namespace Tests\Feature\Api;

use App\Models\Checada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WearOSTest extends TestCase
{
    use RefreshDatabase;

    protected $apiKey = 'secret-wearos-key-123';
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        \App\Models\Department::create(['id' => 1, 'deparment' => 'TEST']);
        \App\Models\Puesto::create(['id' => 1, 'puesto' => 'TEST', 'clave' => 'T1']);
        \App\Models\Horario::create(['id' => 1, 'horario' => '08:00 A 16:00']);

        $this->employee = \App\Models\Employe::create([
            'num_empleado' => '332618',
            'name' => 'TEST',
            'father_lastname' => 'USER',
            'mother_lastname' => 'WEAROS',
            'deparment_id' => 1,
            'puesto_id' => 1,
            'horario_id' => 1,
            'active' => 1
        ]);
    }

    /** @test */
    public function test_wearos_can_register_checkin_with_valid_key()
    {
        $response = $this->withHeader('X-API-KEY', $this->apiKey)
            ->postJson('/api/wearos/checar', [
                'fecha' => '2026-03-25 10:00:00',
                'identificador' => '332618'
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Checada registrada exitosamente'
            ]);

        $this->assertDatabaseHas('checadas', [
            'num_empleado' => '332618',
            'fecha' => '2026-03-25 10:00:00'
        ]);
    }

    /** @test */
    public function test_wearos_handles_non_numeric_identifier_safely()
    {
        $response = $this->withHeader('X-API-KEY', $this->apiKey)
            ->postJson('/api/wearos/checar', [
                'fecha' => '2026-03-25 10:00:00',
                'identificador' => 'WearOS'
            ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('checadas', [
            'num_empleado' => '332618',
            'fecha' => '2026-03-25 10:00:00'
        ]);
    }

    /** @test */
    public function test_wearos_cannot_register_checkin_with_invalid_key()
    {
        $response = $this->withHeader('X-API-KEY', 'wrong-key')
            ->postJson('/api/wearos/checar', [
                'fecha' => '2026-03-25 10:00:00',
                'identificador' => '332618'
            ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'No autorizado']);
    }


    /** @test */
    public function test_wearos_can_retrieve_history()
    {
        Checada::create([
            'num_empleado' => '332618',
            'fecha' => '2026-03-25 08:00:00',
            'identificador' => 'WOS_1'
        ]);

        Checada::create([
            'num_empleado' => '332618',
            'fecha' => '2026-03-25 09:00:00',
            'identificador' => 'WOS_2'
        ]);

        $response = $this->withHeader('X-API-KEY', $this->apiKey)
            ->getJson('/api/wearos/historial/332618');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'check_ins');
    }

    /** @test */
    public function test_wearos_history_requires_valid_key()
    {
        $response = $this->withHeader('X-API-KEY', 'wrong-key')
            ->getJson('/api/wearos/historial/332618');

        $response->assertStatus(401);
    }
}
