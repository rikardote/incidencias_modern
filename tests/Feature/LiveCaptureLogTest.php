<?php

namespace Tests\Feature;

use App\Models\Employe;
use App\Models\Incidencia;
use App\Models\User;
use App\Models\Department;
use App\Models\CodigoDeIncidencia;
use App\Models\Qna;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Livewire\Admin\LiveCaptureLog;

class LiveCaptureLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_all_logs()
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $dept1 = Department::create(['description' => 'DEPT 1', 'code' => '001']);
        $dept2 = Department::create(['description' => 'DEPT 2', 'code' => '002']);

        $emp1 = Employe::create(['num_empleado' => '1', 'name' => 'E1', 'father_lastname' => 'L1', 'mother_lastname' => 'M1', 'deparment_id' => $dept1->id]);
        $emp2 = Employe::create(['num_empleado' => '2', 'name' => 'E2', 'father_lastname' => 'L2', 'mother_lastname' => 'M2', 'deparment_id' => $dept2->id]);

        $qna = Qna::create(['qna' => 1, 'year' => 2026, 'active' => '1']);
        $codigo = CodigoDeIncidencia::create(['code' => '01', 'description' => 'TEST']);

        Incidencia::create(['qna_id' => $qna->id, 'employee_id' => $emp1->id, 'codigodeincidencia_id' => $codigo->id, 'fecha_inicio' => now(), 'fecha_final' => now(), 'token' => 'TOKEN1']);
        Incidencia::create(['qna_id' => $qna->id, 'employee_id' => $emp2->id, 'codigodeincidencia_id' => $codigo->id, 'fecha_inicio' => now(), 'fecha_final' => now(), 'token' => 'TOKEN2']);

        Livewire::actingAs($admin)
            ->test(LiveCaptureLog::class)
            ->assertSee('E1')
            ->assertSee('E2');
    }

    /** @test */
    public function user_only_sees_logs_from_allowed_departments()
    {
        $user = User::factory()->create(['type' => 'user']);
        $dept1 = Department::create(['description' => 'DEPT 1', 'code' => '001']);
        $dept2 = Department::create(['description' => 'DEPT 2', 'code' => '002']);

        $user->departments()->attach($dept1->id);

        $emp1 = Employe::create(['num_empleado' => '1', 'name' => 'ALLOWED_EMP', 'father_lastname' => 'L1', 'mother_lastname' => 'M1', 'deparment_id' => $dept1->id]);
        $emp2 = Employe::create(['num_empleado' => '2', 'name' => 'FORBIDDEN_EMP', 'father_lastname' => 'L2', 'mother_lastname' => 'M2', 'deparment_id' => $dept2->id]);

        $qna = Qna::create(['qna' => 1, 'year' => 2026, 'active' => '1']);
        $codigo = CodigoDeIncidencia::create(['code' => '01', 'description' => 'TEST']);

        Incidencia::create(['qna_id' => $qna->id, 'employee_id' => $emp1->id, 'codigodeincidencia_id' => $codigo->id, 'fecha_inicio' => now(), 'fecha_final' => now(), 'token' => 'TOKEN1']);
        Incidencia::create(['qna_id' => $qna->id, 'employee_id' => $emp2->id, 'codigodeincidencia_id' => $codigo->id, 'fecha_inicio' => now(), 'fecha_final' => now(), 'token' => 'TOKEN2']);

        Livewire::actingAs($user)
            ->test(LiveCaptureLog::class)
            ->assertSee('ALLOWED_EMP')
            ->assertDontSee('FORBIDDEN_EMP');
    }
}
