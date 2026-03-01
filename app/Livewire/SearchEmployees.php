<?php

namespace App\Livewire;

use App\Models\Employe;
use Livewire\Component;
use Livewire\WithPagination;

class SearchEmployees extends Component
{
    use WithPagination;

    public $search = '';
    public $listAll = false;

    // Propiedades para el formulario
    public $showEmployeeModal = false;
    public $editingEmployeeId = null;

    public $num_empleado, $name, $father_lastname, $mother_lastname;
    public $deparment_id, $condicion_id, $puesto_id, $horario_id, $jornada_id;
    public $num_plaza, $num_seguro, $fecha_ingreso;
    public $estancia = false, $comisionado = false, $lactancia = false;
    public $lactancia_inicio, $lactancia_fin;

    protected function rules()
    {
        return [
            'num_empleado' => 'required|numeric|digits_between:5,6|unique:employees,num_empleado,' . $this->editingEmployeeId,
            'name' => 'required|min:2|max:100',
            'father_lastname' => 'required|min:2|max:100',
            'mother_lastname' => 'required|min:2|max:100',
            'deparment_id' => 'required|exists:deparments,id',
            'puesto_id' => 'required|exists:puestos,id',
            'horario_id' => 'required|exists:horarios,id',
            'jornada_id' => 'required|exists:jornadas,id',
            'condicion_id' => 'required|exists:condiciones,id',
            'fecha_ingreso' => 'required|date',
            'lactancia_inicio' => 'required_if:lactancia,true|nullable|date',
            'lactancia_fin' => 'required_if:lactancia,true|nullable|date',
        ];
    }

    public function create()
    {
        $this->reset(['editingEmployeeId', 'num_empleado', 'name', 'father_lastname', 'mother_lastname', 'deparment_id', 'condicion_id', 'puesto_id', 'horario_id', 'jornada_id', 'num_plaza', 'num_seguro', 'fecha_ingreso', 'estancia', 'comisionado', 'lactancia', 'lactancia_inicio', 'lactancia_fin']);
        $this->resetValidation();
        $this->showEmployeeModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->editingEmployeeId = $id;
        $employee = \App\Models\Employe::findOrFail($id);

        $this->num_empleado = $employee->num_empleado;
        $this->name = $employee->name;
        $this->father_lastname = $employee->father_lastname;
        $this->mother_lastname = $employee->mother_lastname;
        $this->deparment_id = $employee->deparment_id;
        $this->condicion_id = $employee->condicion_id;
        $this->puesto_id = $employee->puesto_id;
        $this->horario_id = $employee->horario_id;
        $this->jornada_id = $employee->jornada_id;
        $this->num_plaza = $employee->num_plaza;
        $this->num_seguro = $employee->num_seguro;
        $this->fecha_ingreso = $employee->fecha_ingreso;
        $this->estancia = (bool)$employee->estancia;
        $this->comisionado = (bool)$employee->comisionado;
        $this->lactancia = (bool)$employee->lactancia;
        $this->lactancia_inicio = $employee->lactancia_inicio;
        $this->lactancia_fin = $employee->lactancia_fin;

        $this->showEmployeeModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'num_empleado' => $this->num_empleado,
            'name' => strtoupper($this->name),
            'father_lastname' => strtoupper($this->father_lastname),
            'mother_lastname' => strtoupper($this->mother_lastname),
            'deparment_id' => $this->deparment_id,
            'condicion_id' => $this->condicion_id,
            'puesto_id' => $this->puesto_id,
            'horario_id' => $this->horario_id,
            'jornada_id' => $this->jornada_id,
            'num_plaza' => $this->num_plaza,
            'num_seguro' => $this->num_seguro,
            'fecha_ingreso' => $this->fecha_ingreso,
            'estancia' => $this->estancia,
            'comisionado' => $this->comisionado,
            'lactancia' => $this->lactancia,
            'lactancia_inicio' => $this->lactancia ? $this->lactancia_inicio : null,
            'lactancia_fin' => $this->lactancia ? $this->lactancia_fin : null,
            'active' => '1',
        ];

        if ($this->editingEmployeeId) {
            \App\Models\Employe::find($this->editingEmployeeId)->update($data);
            $message = 'Empleado actualizado con éxito.';
        }
        else {
            \App\Models\Employe::create($data);
            $message = 'Empleado registrado con éxito.';
        }

        $this->showEmployeeModal = false;
        $this->dispatch('swal', [
            'title' => '¡Hecho!',
            'text' => $message,
            'icon' => 'success'
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->listAll = false;
    }

    public function toggleListAll()
    {
        $this->listAll = !$this->listAll;
        if ($this->listAll) {
            $this->search = '';
        }
    }

    public function render()
    {
        $user = auth()->user();

        // Si no hay búsqueda y no se ha pedido listar todo, devolvemos una vista vacía
        if (empty($this->search) && !$this->listAll) {
            return view('livewire.search-employees', [
                'employees' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'departments' => \App\Models\Department::orderBy('code')->get(),
                'puestos' => \App\Models\Puesto::orderBy('puesto')->get(),
                'horarios' => \App\Models\Horario::orderBy('horario')->get(),
                'jornadas' => \App\Models\Jornada::orderBy('jornada')->get(),
                'condiciones' => \App\Models\Condicion::orderBy('condicion')->get(),
            ]);
        }

        $query = Employe::with(['department', 'puesto'])
            ->active()
            ->whereDoesntHave('department', function ($q) {
            $q->where('code', '99999');
        });

        if (!$user->admin()) {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $query->whereIn('deparment_id', $departmentIds);
        }

        $employees = $query->when($this->search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                    $subQ->where('num_empleado', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%")
                        ->orWhere('father_lastname', 'LIKE', "%{$search}%")
                        ->orWhere('mother_lastname', 'LIKE', "%{$search}%");
                }
                );
            })
            ->orderBy('num_empleado', 'ASC')
            ->paginate(20);

        return view('livewire.search-employees', [
            'employees' => $employees,
            'departments' => \App\Models\Department::orderBy('code')->get(),
            'puestos' => \App\Models\Puesto::orderBy('puesto')->get(),
            'horarios' => \App\Models\Horario::orderBy('horario')->get(),
            'jornadas' => \App\Models\Jornada::orderBy('jornada')->get(),
            'condiciones' => \App\Models\Condicion::orderBy('condicion')->get(),
        ]);
    }
}