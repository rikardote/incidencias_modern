<?php

namespace Tests\Feature\Livewire;

use App\Models\Employe;
use App\Models\User;
use App\Livewire\Incidencias\Manager;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncidenciaManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_component_renders_correctly(): void
    {
        $user = User::factory()->create(['type' => 'admin']);
        $department = \App\Models\Department::create(['description' => 'ADMIN DEPT', 'code' => '101']);
        
        $user->departments()->attach($department->id);

        $employee = Employe::create([
            'num_empleado' => '12345',
            'name' => 'John',
            'father_lastname' => 'Doe',
            'mother_lastname' => 'Smith',
            'deparment_id' => $department->id,
        ]);

        Livewire::actingAs($user)
            ->test(Manager::class, ['employeeId' => $employee->id])
            ->assertStatus(200)
            ->assertSee('JOHN DOE SMITH')
            ->assertSee('12345');
    }

    public function test_non_admin_cannot_access_other_department_employee(): void
    {
        $user = User::factory()->create(['type' => 'user']);
        $dept1 = \App\Models\Department::create(['description' => 'Dept 1']);
        $dept2 = \App\Models\Department::create(['description' => 'Dept 2']);
        
        $employee = Employe::create([
            'num_empleado' => '12345',
            'name' => 'Secret',
            'father_lastname' => 'Employee',
            'mother_lastname' => 'Test',
            'deparment_id' => $dept2->id,
        ]);

        // User is not in dept2
        Livewire::actingAs($user)
            ->test(Manager::class, ['employeeId' => $employee->id])
            ->assertStatus(403);
    }
}
