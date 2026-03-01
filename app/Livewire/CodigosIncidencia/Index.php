<?php

namespace App\Livewire\CodigosIncidencia;

use App\Models\CodigoDeIncidencia;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    // Modal state
    public $showModal = false;

    // Form data
    public $codigoId;
    public $code;
    public $description;

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

        $codigo = CodigoDeIncidencia::findOrFail($id);

        $this->codigoId = $codigo->id;
        $this->code = $codigo->code;
        $this->description = $codigo->description;

        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'code' => 'required|string|max:50|unique:codigos_de_incidencias,code,' . $this->codigoId,
            'description' => 'required|string|max:255',
        ];

        $this->validate($rules);

        $data = [
            'code' => $this->code,
            'description' => strtoupper($this->description),
        ];

        CodigoDeIncidencia::updateOrCreate(['id' => $this->codigoId], $data);

        $this->showModal = false;
        $this->dispatch('notify', [
            'message' => $this->codigoId ? 'Código actulizado exitosamente' : 'Código creado exitosamente',
            'type' => 'success',
        ]);
        $this->resetForm();
    }

    public function delete($id)
    {
        $codigo = CodigoDeIncidencia::findOrFail($id);
        $codigo->delete();

        $this->dispatch('notify', [
            'message' => 'Código eliminado exitosamente',
            'type' => 'success',
        ]);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->codigoId = null;
        $this->code = '';
        $this->description = '';
    }

    public function render()
    {
        $codigos = CodigoDeIncidencia::where(function ($query) {
            $query->where('code', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->orderBy('code', 'asc')
            ->paginate(15);

        return view('livewire.codigos-incidencia.index', [
            'codigos' => $codigos,
        ])->layout('layouts.app');
    }
}