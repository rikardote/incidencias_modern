<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Incidencia;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Lazy]
class LiveCaptureLog extends Component
{
    public $logs = [];

    public function mount()
    {
        $this->loadInitialLogs();
    }

    private function loadInitialLogs()
    {
        try {
            // Un solo query consolidado para evitar el problema de N+1 queries.
            // Agrupamos por token y obtenemos los datos principales en un solo paso.
            $batches = DB::table('incidencias')
                ->join('employees', 'incidencias.employee_id', '=', 'employees.id')
                ->join('codigos_de_incidencias', 'incidencias.codigodeincidencia_id', '=', 'codigos_de_incidencias.id')
                ->leftJoin('periodos', 'incidencias.periodo_id', '=', 'periodos.id')
                ->select([
                    'incidencias.token',
                    'employees.name',
                    'employees.father_lastname',
                    'employees.mother_lastname',
                    'codigos_de_incidencias.code as type',
                    'incidencias.capturado_por as user_name',
                    'incidencias.created_at',
                    DB::raw('MIN(incidencias.fecha_inicio) as fecha_inicio'),
                    DB::raw('MAX(incidencias.fecha_final) as fecha_final'),
                    DB::raw('SUM(incidencias.total_dias) as total_dias'),
                    DB::raw('periodos.periodo as per_num'),
                    DB::raw('periodos.year as per_year')
                ])
                ->whereNull('incidencias.deleted_at')
                ->groupBy(
                    'incidencias.token', 
                    'employees.name', 
                    'employees.father_lastname', 
                    'employees.mother_lastname', 
                    'codigos_de_incidencias.code', 
                    'incidencias.capturado_por', 
                    'incidencias.created_at',
                    'periodos.periodo',
                    'periodos.year'
                )
                ->orderBy('incidencias.created_at', 'desc')
                ->take(15)
                ->get();

            $this->logs = $batches->map(function($g) {
                return [
                    'employee_name' => "{$g->name} {$g->father_lastname} {$g->mother_lastname}",
                    'type' => $g->type,
                    'user_name' => $g->user_name,
                    'details' => [
                        'fecha_inicio' => $g->fecha_inicio,
                        'fecha_final' => $g->fecha_final,
                        'total_dias' => (int)$g->total_dias,
                        'periodo' => $g->per_num ? "P{$g->per_num}/" . substr($g->per_year, -2) : 'N/A',
                        'qnas' => $this->resolveQnasForToken($g->token)
                    ],
                    'created_at' => $g->created_at
                ];
            })->toArray();

            if (empty($this->logs)) {
                $this->logs[] = $this->getSystemOnlinePlaceholder();
            }
        } catch (\Exception $e) {
            Log::error("LiveLog Mount Error: " . $e->getMessage());
            $this->logs = [$this->getSystemOnlinePlaceholder('Error de carga')];
        }
    }

    private function resolveQnasForToken($token)
    {
        // Aunque sigue siendo un query por token, al menos el mount ahora es diferido (lazy)
        // y el query principal está indexado.
        return DB::table('incidencias')
            ->join('qnas', 'incidencias.qna_id', '=', 'qnas.id')
            ->where('incidencias.token', $token)
            ->whereNull('incidencias.deleted_at')
            ->select('qnas.qna', 'qnas.year')
            ->distinct()
            ->get()
            ->map(fn($q) => "Q{$q->qna}/" . substr($q->year, -2))
            ->implode(', ');
    }

    private function getSystemOnlinePlaceholder($msg = 'ACTIVO')
    {
        return [
            'id' => 0,
            'employee_name' => 'SISTEMA DE MONITOREO',
            'type' => '--',
            'user_name' => 'ROOT',
            'details' => [
                'qnas' => $msg,
                'periodo' => '--',
                'fecha_inicio' => date('Y-m-d'),
                'fecha_final' => date('Y-m-d'),
                'total_dias' => 0
            ],
            'created_at' => now()->toDateTimeString()
        ];
    }

    #[On('echo-presence:chat,NewIncidenciaBatchCreated')]
    #[On('echo-presence:chat,.NewIncidenciaBatchCreated')]
    public function onNewIncidenciaItem($payload)
    {
        $newLog = isset($payload['log']) ? $payload['log'] : $payload;
        array_unshift($this->logs, $newLog);
        if (count($this->logs) > 15) array_pop($this->logs);
    }

    public function render()
    {
        return view('livewire.admin.live-capture-log');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="fixed bottom-6 right-24 z-[60]">
            <div class="animate-pulse w-12 h-12 bg-white/5 rounded-full border border-white/10"></div>
        </div>
        HTML;
    }
}