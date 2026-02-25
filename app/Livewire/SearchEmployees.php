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
        $query = Employe::with(['department', 'puesto'])
            ->active()
            ->whereDoesntHave('department', function ($q) {
                $q->where('code', '99999');
            });

        $user = auth()->user();
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
                });
            })
            ->orderBy('num_empleado', 'ASC')
            ->paginate(15);

        return view('livewire.search-employees', [
            'employees' => $employees,
        ]);
    }
}
