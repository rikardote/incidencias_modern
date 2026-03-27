<?php

namespace App\Console\Commands;

use App\Models\Employe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InitEmployeePasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:init-passwords {--force : Force completion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize employee passwords using their RFC from the Employees API hashed';

    /**
     * @var \App\Services\Employees\EmployeeApiService
     */
    protected $apiService;

    /**
     * Create a new command instance.
     */
    public function __construct(\App\Services\Employees\EmployeeApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('Esto sobrescribirá las contraseñas de TODOS los empleados con su RFC (obtenido de la API) hasheado. ¿Continuar?')) {
            return;
        }

        $query = Employe::query();
        $count = $query->count();

        $this->info("Procesando {$count} empleados consultando la API...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(50, function ($employees) use ($bar) {
            foreach ($employees as $employee) {
                // Intentar obtener RFC de la API (algunos vienen en 'rfc', otros en 'id_legal')
                $apiData = $this->apiService->getEmployeeData($employee->num_empleado);
                $rfc = $apiData['rfc'] ?? $apiData['id_legal'] ?? null;
                
                if ($rfc) {
                    $rfc = strtoupper(trim($rfc));
                    $employee->password = Hash::make($rfc);
                    
                    // Sincronizar RFC local
                    $employee->rfc = $rfc;
                    
                    $employee->save();
                }
                
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Contraseñas inicializadas correctamente usando RFC de la API.');
    }
}
