<?php

namespace App\Livewire\Catalogos;

use App\Models\Jornada;
use Livewire\Component;
use Livewire\WithPagination;

class Jornadas extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $jornadaId;
    public $jornada;

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
        $item = Jornada::findOrFail($id);
        $this->jornadaId = $item->id;
        $this->jornada = $item->jornada;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'jornada' => 'required|string|max:255|unique:jornadas,jornada,' . $this->jornadaId,
        ]);

        Jornada::updateOrCreate(['id' => $this->jornadaId], [
            'jornada' => strtoupper($this->jornada),
        ]);

        $this->showModal = false;
        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => $this->jornadaId ? 'Jornada actualizada' : 'Jornada creada'
        ]);
        $this->resetForm();
    }

    public function delete($id)
    {
        $item = Jornada::findOrFail($id);
        $item->delete();

        $this->dispatch('toast', [
            'icon' => 'success',
            'title' => 'Jornada eliminada'
        ]);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->jornadaId = null;
        $this->jornada = '';
    }

    public function render()
    {
        $jornadas = Jornada::where('jornada', 'like', '%' . $this->search . '%')
            ->orderBy('jornada', 'asc')
            ->paginate(15);

        return view('livewire.catalogos.jornadas', [
            'jornadas' => $jornadas,
        ])->layout('layouts.app');
    }
}
