<?php

namespace App\Livewire\Catalogos;

use App\Models\Periodo;
use Livewire\Component;
use Livewire\WithPagination;

class Periodos extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $periodoId;
    public $periodo;
    public $year;

    public function mount()
    {
        if (!auth()->user()->admin()) {
            return redirect()->route('dashboard');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->year = date('Y');
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $item = Periodo::findOrFail($id);
        $this->periodoId = $item->id;
        $this->periodo = $item->periodo;
        $this->year = $item->year;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'periodo' => 'required|integer',
            'year' => 'required|integer',
        ]);

        Periodo::updateOrCreate(['id' => $this->periodoId], [
            'periodo' => $this->periodo,
            'year' => $this->year,
        ]);

        $this->showModal = false;
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => $this->periodoId ? 'Periodo actualizado' : 'Periodo creado'
        ]);
        $this->resetForm();
    }

    public function delete($id)
    {
        $item = Periodo::findOrFail($id);
        $item->delete();

        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Periodo eliminado'
        ]);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->periodoId = null;
        $this->periodo = '';
        $this->year = '';
    }

    public function render()
    {
        $periodos = Periodo::query()
            ->when($this->search, function($q) {
                $q->where('periodo', 'like', '%' . $this->search . '%')
                  ->orWhere('year', 'like', '%' . $this->search . '%');
            })
            ->orderBy('year', 'desc')
            ->orderBy('periodo', 'desc')
            ->paginate(15);

        return view('livewire.catalogos.periodos', [
            'periodos' => $periodos,
        ])->layout('layouts.app');
    }
}
