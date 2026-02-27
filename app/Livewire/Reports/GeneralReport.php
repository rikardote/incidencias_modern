<?php

namespace App\Livewire\Reports;

use App\Models\Department;
use App\Models\Qna;
use App\Models\Incidencia;
use Livewire\Component;

class GeneralReport extends Component
{
    public $year;
    public $qnaId;
    public $departmentId;
    public $results = null;

    public function mount()
    {
        $activeQna = Qna::where('active', '1')->first();
        if ($activeQna) {
            $this->year = $activeQna->year;
            $this->qnaId = $activeQna->id;
        }
        else {
            $this->year = date('Y');
        }
    }

    public function updatedYear()
    {
        $this->qnaId = null;
        $this->results = null;
    }

    public function updatedQnaId()
    {
        $this->results = null;
    }

    public function updatedDepartmentId()
    {
        $this->results = null;
    }

    public function generate()
    {
        usleep(1500000); // Delay intencional para ver la animación de la Isla
        $this->validate([
            'qnaId' => 'required|exists:qnas,id',
            'departmentId' => 'required|exists:deparments,id',
        ]);
        usleep(800000); // Artificial delay to show the spinner

        $incidencias = Incidencia::with(['employee', 'codigo', 'periodo'])
            ->where('qna_id', $this->qnaId)
            ->whereHas('employee', function ($q) {
            $q->where('deparment_id', $this->departmentId);
        })
            ->whereNotIn('codigodeincidencia_id', function ($q) {
            $q->select('id')->from('codigos_de_incidencias')->whereIn('code', [902, 903, 904]);
        })
            ->get()
            ->groupBy('employee.num_empleado');

        $this->results = [];
        foreach ($incidencias as $numEmpleado => $items) {
            $employee = $items->first()->employee;
            $this->results[$numEmpleado] = [
                'name' => $employee->full_name,
                'items' => $items->map(fn($i) => [
            'code' => $i->codigo->code,
            'fecha_inicio' => $i->fecha_inicio,
            'fecha_final' => $i->fecha_final,
            'periodo' => $i->periodo ? $i->periodo->periodo . '/' . $i->periodo->year : '-',
            'total' => $i->total_dias,
            'otorgado' => $i->otorgado,
            'becas_comments' => $i->becas_comments,
            'horas_otorgadas' => $i->horas_otorgadas,
            'autoriza_txt' => $i->autoriza_txt,
            ])
            ];
        }

        ksort($this->results);

        // Notificar a la Isla Dinámica que hemos terminado
        $this->dispatch('island-progress-update', progress: 100);
        
        $this->dispatch('island-notif', 
            message: 'Reporte Listo', 
            type: 'success'
        );
    }

    public function render()
    {
        $user = auth()->user();
        if ($user->admin()) {
            $departments = Department::orderBy('code')->get();
        }
        else {
            $departments = $user->departments()->orderBy('code')->get();
        }

        $years = Qna::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $qnas = Qna::where('year', $this->year)->orderBy('qna', 'desc')->get();

        return view('livewire.reports.general-report', [
            'years' => $years,
            'qnas' => $qnas,
            'departments' => $departments,
        ])->layout('layouts.app');
    }
}