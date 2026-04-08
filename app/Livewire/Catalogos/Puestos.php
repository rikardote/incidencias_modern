<?php

namespace App\Livewire\Catalogos;

use App\Models\Puesto;
use Livewire\Component;
use Livewire\WithPagination;

class Puestos extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $puestoId;
    public $puesto;
    public $clave;

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
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $item = Puesto::findOrFail($id);
        $this->puestoId = $item->id;
        $this->puesto = $item->puesto;
        $this->clave = $item->clave;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'puesto' => 'required|string|max:255|unique:puestos,puesto,' . $this->puestoId,
            'clave' => 'required|string|max:255|unique:puestos,clave,' . $this->puestoId,
        ], [
            'puesto.required' => 'El nombre del puesto es obligatorio.',
            'clave.required' => 'La clave es obligatoria.',
            'puesto.unique' => 'Este nombre de puesto ya está registrado.',
            'clave.unique' => 'Esta clave ya está registrada.',
        ]);

        Puesto::updateOrCreate(['id' => $this->puestoId], [
            'puesto' => strtoupper($this->puesto),
            'clave' => strtoupper($this->clave),
        ]);

        $this->showModal = false;
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => $this->puestoId ? 'Puesto actualizado' : 'Puesto creado'
        ]);
        $this->resetForm();
    }

    public function delete($id)
    {
        $item = Puesto::findOrFail($id);
        $item->delete();

        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Puesto eliminado'
        ]);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->puestoId = null;
        $this->puesto = '';
        $this->clave = '';
    }

    public function render()
    {
        $puestos = Puesto::where('puesto', 'like', '%' . $this->search . '%')
            ->orWhere('clave', 'like', '%' . $this->search . '%')
            ->orderBy('puesto', 'asc')
            ->paginate(15);

        return view('livewire.catalogos.puestos', [
            'puestos' => $puestos,
        ])->layout('layouts.app');
    }
}
