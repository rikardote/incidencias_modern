<?php

namespace App\Livewire\Catalogos;

use App\Models\Horario;
use Livewire\Component;
use Livewire\WithPagination;

class Horarios extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $horarioId;
    public $horario;

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
        $item = Horario::findOrFail($id);
        $this->horarioId = $item->id;
        $this->horario = $item->horario;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'horario' => 'required|string|max:255|unique:horarios,horario,' . $this->horarioId,
        ]);

        Horario::updateOrCreate(['id' => $this->horarioId], [
            'horario' => strtoupper($this->horario),
        ]);

        $this->showModal = false;
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => $this->horarioId ? 'Horario actualizado' : 'Horario creado'
        ]);
        $this->resetForm();
    }

    public function delete($id)
    {
        $item = Horario::findOrFail($id);
        $item->delete();

        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Horario eliminado'
        ]);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->horarioId = null;
        $this->horario = '';
    }

    public function render()
    {
        $horarios = Horario::where('horario', 'like', '%' . $this->search . '%')
            ->orderBy('horario', 'asc')
            ->paginate(15);

        return view('livewire.catalogos.horarios', [
            'horarios' => $horarios,
        ])->layout('layouts.app');
    }
}
