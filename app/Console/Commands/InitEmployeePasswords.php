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
    protected $description = 'Initialize employee passwords using their RFC hashed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('Esto sobrescribirá las contraseñas vacías de los empleados con su RFC hasheado. ¿Continuar?')) {
            return;
        }

        $query = Employe::whereNull('password')->orWhere('password', '');
        $count = $query->count();

        $this->info("Procesando {$count} empleados...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(200, function ($employees) use ($bar) {
            foreach ($employees as $employee) {
                $rfc = $employee->rfc;
                
                if ($rfc) {
                    $employee->password = Hash::make(strtoupper(trim($rfc)));
                    $employee->save();
                }
                
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Contraseñas inicializadas correctamente.');
    }
}
