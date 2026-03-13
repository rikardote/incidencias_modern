<?php

namespace App\Livewire;

use App\Models\Employe;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;

class SearchEmployees extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedDepartment = '';

    public $showInactive = false;
    public $listAll = false;

    #[On('echo-presence:chat,GlobalMaintenanceEvent')]
    public function onMaintenanceToggle()
    {
        // Refrescar para actualizar visualmente los botones de incidencia
    }

    // Propiedades para el formulario
    public $showEmployeeModal = false;
    public $editingEmployeeId = null;

    public $num_empleado, $name, $father_lastname, $mother_lastname;
    public $deparment_id, $condicion_id, $puesto_id, $horario_id, $jornada_id;
    public $num_plaza, $num_seguro, $fecha_ingreso;
    public $curp, $rfc;
    public $active = true, $estancia = false, $comisionado = false, $lactancia = false, $exento = false;
    public $lactancia_inicio, $lactancia_fin, $estancia_inicio, $estancia_fin;

    public function getGender()
    {
        if (!$this->curp || strlen($this->curp) < 11) {
            return 'No definido';
        }

        $genderChar = strtoupper($this->curp[10]);

        switch ($genderChar) {
            case 'H': return 'Masculino';
            case 'M': return 'Femenino';
            default: return 'No definido';
        }
    }


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
            'curp' => 'nullable|min:18|max:18',
            'rfc' => 'nullable|min:12|max:13',
            'lactancia_inicio' => 'required_if:lactancia,true|nullable|date',
            'lactancia_fin' => 'required_if:lactancia,true|nullable|date',
            'estancia_inicio' => 'required_if:estancia,true|nullable|date',
            'estancia_fin' => 'required_if:estancia,true|nullable|date',
            'active' => 'required|boolean',
        ];
    }

    public function create()
    {
        $this->reset(['editingEmployeeId', 'num_empleado', 'name', 'father_lastname', 'mother_lastname', 'curp', 'rfc', 'deparment_id', 'condicion_id', 'puesto_id', 'horario_id', 'jornada_id', 'num_plaza', 'num_seguro', 'fecha_ingreso', 'estancia', 'estancia_inicio', 'estancia_fin', 'comisionado', 'lactancia', 'lactancia_inicio', 'lactancia_fin', 'exento', 'active']);
        $this->resetValidation();
        $this->showEmployeeModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->editingEmployeeId = $id;
        $employee = \App\Models\Employe::withTrashed()->findOrFail($id);
        $this->populateForm($employee);
        $this->showEmployeeModal = true;
    }

    private function populateForm($employee)
    {
        $this->num_empleado = $employee->num_empleado;
        $this->name = $employee->name;
        $this->father_lastname = $employee->father_lastname;
        $this->mother_lastname = $employee->mother_lastname;
        $this->curp = $employee->curp;
        $this->rfc = $employee->rfc;
        $this->deparment_id = $employee->deparment_id;
        $this->condicion_id = $employee->condicion_id;
        $this->puesto_id = $employee->puesto_id;
        $this->horario_id = $employee->horario_id;
        $this->jornada_id = $employee->jornada_id;
        $this->num_plaza = $employee->num_plaza;
        $this->num_seguro = $employee->num_seguro;
        $this->fecha_ingreso = $employee->fecha_ingreso;
        $this->estancia = (bool)$employee->estancia;
        $this->estancia_inicio = $employee->estancia_inicio;
        $this->estancia_fin = $employee->estancia_fin;
        $this->comisionado = (bool)$employee->comisionado;
        $this->lactancia = (bool)$employee->lactancia;
        $this->lactancia_inicio = $employee->lactancia_inicio;
        $this->lactancia_fin = $employee->lactancia_fin;
        $this->exento = (bool)$employee->exento;
        $this->active = (bool)$employee->active;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'num_empleado' => str_pad($this->num_empleado, 6, '0', STR_PAD_LEFT),
            'name' => strtoupper($this->name),
            'father_lastname' => strtoupper($this->father_lastname),
            'mother_lastname' => strtoupper($this->mother_lastname),
            'curp' => $this->curp,
            'rfc' => $this->rfc,
            'deparment_id' => $this->deparment_id,
            'condicion_id' => $this->condicion_id,
            'puesto_id' => $this->puesto_id,
            'horario_id' => $this->horario_id,
            'jornada_id' => $this->jornada_id,
            'num_plaza' => $this->num_plaza,
            'num_seguro' => $this->num_seguro,
            'fecha_ingreso' => $this->fecha_ingreso,
            'estancia' => $this->estancia,
            'estancia_inicio' => $this->estancia ? $this->estancia_inicio : null,
            'estancia_fin' => $this->estancia ? $this->estancia_fin : null,
            'comisionado' => $this->comisionado,
            'lactancia' => $this->lactancia,
            'lactancia_inicio' => $this->lactancia ? $this->lactancia_inicio : null,
            'lactancia_fin' => $this->lactancia ? $this->lactancia_fin : null,
            'active' => $this->active ? 1 : 0,
            'exento' => $this->exento,
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

    public function updatedNumEmpleado($value)
    {
        if (!$this->editingEmployeeId && !empty($value)) {
            $existing = \App\Models\Employe::withTrashed()->where('num_empleado', $value)->first();
            
            if ($existing) {
                // Rellenar campos automáticamente
                $this->editingEmployeeId = $existing->id;
                $this->populateForm($existing);
                
                // Forzar reactivación
                $this->active = true;

                $this->dispatch('swal', [
                    'title' => '¡Registro localizado!',
                    'text' => "Se encontró a {$existing->fullname} con este número. Los campos han sido rellenados automáticamente. Por favor, ACTUALICE TODA LA INFORMACIÓN necesaria (Departamento, Puesto, Horarios, etc.) antes de guardar para reactivar al empleado.",
                    'icon' => 'info',
                    'timer' => 10000
                ]);
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->listAll = false;
    }

    public function updatingSelectedDepartment()
    {
        $this->resetPage();
    }

    public function updatingShowInactive()
    {
        $this->resetPage();
    }

    public function toggleListAll()
    {
        $this->listAll = !$this->listAll;
        if ($this->listAll) {
            $this->search = '';
            $this->selectedDepartment = '';
        }
    }

    public function render()
    {
        $user = auth()->user();

        // Obtener departamentos según acceso
        $deptQuery = \App\Models\Department::orderBy('code');
        if (!$user->admin()) {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $deptQuery->whereIn('id', $departmentIds);
        }
        $availableDepartments = $deptQuery->get();

        // Si no hay búsqueda, ni depto, ni inactivos, ni listar todo, devolvemos vista vacía
        // Catálogos cacheados (solo se envían cuando el modal está abierto)
        $catalogos = $this->showEmployeeModal ? $this->getCachedCatalogs() : [
            'puestos' => collect(),
            'horarios' => collect(),
            'jornadas' => collect(),
            'condiciones' => collect(),
        ];

        if (empty($this->search) && empty($this->selectedDepartment) && !$this->showInactive && !$this->listAll) {
            return view('livewire.search-employees', array_merge([
                'employees' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'departments' => $availableDepartments,
            ], $catalogos));
        }

        if ($this->showInactive) {
            // MODO BAJAS: Ver todo (inactivos, borrados y depto 99999)
            $query = Employe::withTrashed()->with(['department', 'puesto'])
                ->where(function($q) {
                    $q->where('active', 0)->orWhere('active', '0')->orWhere('active', false);
                });
        } else {
            // MODO NORMAL (Activos): Sin borrados y sin depto 99999
            $query = Employe::with(['department', 'puesto'])
                ->where(function($q) {
                    $q->where('active', 1)->orWhere('active', '1')->orWhere('active', true);
                })
                ->whereDoesntHave('department', function ($q) {
                    $q->where('code', '99999');
                });
        }

        // Filtro de seguridad (Solo deptos autorizados)
        if (!$user->admin()) {
            $departmentIds = $user->departments()->pluck('deparment_id')->toArray();
            $query->whereIn('deparment_id', $departmentIds);
        }

        // Filtro por selección de departamento
        if (!empty($this->selectedDepartment)) {
            $query->where('deparment_id', $this->selectedDepartment);
        }

        // Filtro por texto de búsqueda
        $employees = $query->when($this->search, function ($q) {
            $term = "%{$this->search}%";
            return $q->where(function ($subQ) use ($term) {
                $subQ->where('num_empleado', 'LIKE', $term)
                    ->orWhere('name', 'LIKE', $term)
                    ->orWhere('father_lastname', 'LIKE', $term)
                    ->orWhere('mother_lastname', 'LIKE', $term);
            });
        })
        ->orderBy('num_empleado', 'ASC')
        ->paginate(20);

        return view('livewire.search-employees', array_merge([
            'employees' => $employees,
            'departments' => $availableDepartments,
        ], $catalogos));
    }

    private function getCachedCatalogs(): array
    {
        return [
            'puestos' => Cache::remember('catalogo_puestos', 3600, fn() => \App\Models\Puesto::orderBy('puesto')->get()),
            'horarios' => Cache::remember('catalogo_horarios', 3600, fn() => \App\Models\Horario::orderBy('horario')->get()),
            'jornadas' => Cache::remember('catalogo_jornadas', 3600, fn() => \App\Models\Jornada::orderBy('jornada')->get()),
            'condiciones' => Cache::remember('catalogo_condiciones', 3600, fn() => \App\Models\Condicion::orderBy('condicion')->get()),
        ];
    }
}