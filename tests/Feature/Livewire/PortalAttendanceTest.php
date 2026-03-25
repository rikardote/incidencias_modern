<?php

namespace Tests\Feature\Livewire;

use App\Models\Employe;
use App\Models\Checada;
use App\Livewire\Biometrico\EmployeeAttendance;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PortalAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup a department
        $department = \App\Models\Department::create(['description' => 'TEST DEPT', 'code' => '999']);

        // Create an employee with a password for authentication
        $this->employee = Employe::create([
            'num_empleado' => 'TEST001',
            'name' => 'Test',
            'father_lastname' => 'Employee',
            'mother_lastname' => 'Portal',
            'deparment_id' => $department->id,
            'password' => bcrypt('password'),
            'rpc' => 'TEST800101XXX',
        ]);
        
        // Need to ensure horarios table exists for the join in Checada.php (or the bypass)
        \DB::table('horarios')->insert([
            ['id' => 1, 'horario' => '08:00 A 16:00'],
        ]);
        $this->employee->update(['horario_id' => 1]);
    }

    public function test_employee_can_view_portal_attendance_with_last_month_records()
    {
        // Set fixed date for testing
        Carbon::setTestNow(Carbon::create(2025, 3, 15));

        // Create records with unique identifiers
        Checada::create([
            'num_empleado' => $this->employee->num_empleado,
            'identificador' => 'ID_001',
            'fecha' => '2025-02-20 08:00:00',
        ]);

        Checada::create([
            'num_empleado' => $this->employee->num_empleado,
            'identificador' => 'ID_002',
            'fecha' => '2025-01-10 08:00:00', // Outside range (Feb 15 - Mar 15)
        ]);

        Livewire::actingAs($this->employee, 'employee')
            ->test(EmployeeAttendance::class, [
                'employeeId' => $this->employee->id,
                'isPortal' => true
            ])
            ->assertStatus(200)
            ->assertViewIs('livewire.biometrico.portal-attendance')
            ->assertSee('20/02/2025')
            ->assertDontSee('10/01/2025');
        
        Carbon::setTestNow(); // Reset time
    }

    public function test_portal_attendance_is_sorted_by_date_descending()
    {
        Carbon::setTestNow(Carbon::create(2025, 3, 15));

        // Let's use more unique times to verify
        Checada::create(['num_empleado' => $this->employee->num_empleado, 'identificador' => 'ID_003', 'fecha' => '2025-03-01 07:00:00']);
        Checada::create(['num_empleado' => $this->employee->num_empleado, 'identificador' => 'ID_004', 'fecha' => '2025-03-10 09:00:00']);

        Livewire::actingAs($this->employee, 'employee')
            ->test(EmployeeAttendance::class, [
                'employeeId' => $this->employee->id,
                'isPortal' => true
            ])
            ->assertViewHas('checadas', function($checadas) {
                // The collection is for the whole month. Mar 10 should be before Mar 01 in DESC.
                $rowMar10 = $checadas->firstWhere('fecha', '2025-03-10');
                $rowMar01 = $checadas->firstWhere('fecha', '2025-03-01');
                
                // Find their positions in the collection
                $index10 = $checadas->search($rowMar10);
                $index01 = $checadas->search($rowMar01);

                return $index10 < $index01; // DESC: Mar 10 comes first
            });

        Carbon::setTestNow();
    }

    public function test_employee_cannot_see_other_employees_records_in_portal()
    {
        Carbon::setTestNow(Carbon::create(2025, 3, 15));

        // Other employee record with a very unique hour
        Checada::create([
            'num_empleado' => 'OTHER999',
            'identificador' => 'ID_005',
            'fecha' => '2025-03-05 13:37:00', // Unique hour
        ]);

        // Own record with different hour
        Checada::create([
            'num_empleado' => $this->employee->num_empleado,
            'identificador' => 'ID_006',
            'fecha' => '2025-03-06 08:00:00',
        ]);

        Livewire::actingAs($this->employee, 'employee')
            ->test(EmployeeAttendance::class, [
                'employeeId' => $this->employee->id,
                'isPortal' => true
            ])
            ->assertSee('06/03/2025')
            ->assertSee('08:00')
            ->assertDontSee('13:37'); // Should NOT see the other employee's unique hour

        Carbon::setTestNow();
    }
}
