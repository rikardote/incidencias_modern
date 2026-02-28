<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/employees', [\App\Http\Controllers\EmployeeController::class , 'index'])->name('employees.index');
    Route::get('/employees/{employeeId}/incidencias', \App\Livewire\Incidencias\Manager::class)->name('employees.incidencias');
    Route::get('/employees/{employeeId}/vacaciones', \App\Livewire\Reports\VacacionesReport::class)->name('employees.vacaciones');
    Route::get('/employees/{employeeId}/kardex', \App\Livewire\Reports\KardexReport::class)->name('employees.kardex');
    Route::get('/qnas', [\App\Http\Controllers\QnaController::class , 'index'])->name('qnas.index');

    // Reportes
    Route::get('/reportes/general', \App\Livewire\Reports\GeneralReport::class)->name('reports.general');
    Route::get('/reportes/rh5-pdf/{qnaId}/{departmentId}', [\App\Http\Controllers\ReportController::class , 'rh5Pdf'])->name('reports.rh5.pdf');
    Route::get('/reportes/sinderecho', \App\Livewire\Reports\SinDerechoReport::class)->name('reports.sinderecho');
    Route::get('/reportes/sinderecho-pdf/{year}/{month}/{departmentId}', [\App\Http\Controllers\ReportController::class , 'sinDerechoPdf'])->name('reports.sinderecho.pdf');
    Route::get('/reportes/kardex', \App\Livewire\Reports\KardexReport::class)->name('reports.kardex');
    Route::get('/reportes/kardex-pdf/{num_empleado}/{fecha_inicio}/{fecha_final}', [\App\Http\Controllers\ReportController::class , 'kardexPdf'])->name('reports.kardex.pdf');

    // Biométrico
    Route::get('/biometrico', [\App\Http\Controllers\BiometricoController::class , 'index'])->name('biometrico.index');
    Route::get('/biometrico/exportar', [\App\Http\Controllers\BiometricoController::class , 'exportar'])->name('biometrico.exportar');
    Route::get('/employees/{employeeId}/biometrico', \App\Livewire\Biometrico\EmployeeAttendance::class)->name('employees.biometrico');
    Route::get('/employees/{employeeId}/biometrico/pdf/{year}/{quincena}', [\App\Http\Controllers\ReportController::class , 'biometricoIndividualPdf'])->name('biometrico.individual.pdf');

    // Usuarios (solo admins, logic in component)
    Route::get('/usuarios', \App\Livewire\Users\Index::class)->name('users.index');

    // Búsqueda de empleados para el switcher de incidencias
    Route::get('/api/employees/search', function (\Illuminate\Http\Request $request) {
            $q = trim($request->get('q', ''));
            if (strlen($q) < 2)
                return response()->json([]);

            $user = auth()->user();
            $query = \App\Models\Employe::where('active', '1')
                ->where(function ($query) use ($q) {
                $query->where('num_empleado', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('father_lastname', 'like', "%{$q}%")
                    ->orWhere('mother_lastname', 'like', "%{$q}%");
            }
            );

            if (!$user->admin()) {
                $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
                $query->whereIn('deparment_id', $departmentIds);
            }

            return response()->json(
            $query->orderBy('num_empleado')
            ->limit(20)
            ->get(['id', 'num_empleado', 'name', 'father_lastname', 'mother_lastname'])
            ->map(fn($e) => [
            'id' => $e->id,
            'label' => $e->num_empleado . ' - ' . $e->name . ' ' . $e->father_lastname . ' ' . $e->mother_lastname,
            ])
            );
        }
        )->name('employees.search');

        Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
    });

require __DIR__ . '/auth.php';
Route::get('/test-config', function () {
    return config('database.connections.biometrico');
});