<?php

namespace App\Livewire;

use App\Models\Employe;
use Livewire\Component;
use Livewire\WithPagination;

class SearchEmployees extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $employees = Employe::with(['department', 'puesto'])
            ->active()
            ->whereDoesntHave('department', function ($query) {
                $query->where('code', '99999');
            })
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('num_empleado', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%")
                      ->orWhere('father_lastname', 'LIKE', "%{$search}%")
                      ->orWhere('mother_lastname', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('num_empleado', 'ASC')
            ->paginate(15);

        return view('livewire.search-employees', [
            'employees' => $employees,
        ]);
    }
}
