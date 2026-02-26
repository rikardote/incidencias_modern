<?php

namespace App\Livewire\Qnas;

use App\Models\Qna;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Index extends Component
{
    public $isModalOpen = 0;
    public $qna_id, $qna, $year, $description, $active_status = '1', $cierre;

    protected $rules = [
        'qna' => 'required|numeric|min:1|max:24',
        'year' => 'required|numeric|min:2000|max:2100',
        'description' => 'nullable|string|max:255',
        'active_status' => 'required|in:0,1',
        'cierre' => 'nullable|date',
    ];

    public function toggleActive($id)
    {
        $qnaModel = Qna::find($id);
        if ($qnaModel) {
            $qnaModel->active = $qnaModel->active == '1' ? '0' : '1';
            $qnaModel->save();
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->year = date('Y');
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->qna_id = null;
        $this->qna = '';
        $this->year = '';
        $this->description = '';
        $this->active_status = '1';
        $this->cierre = '';
    }

    public function store()
    {
        $this->validate();

        Qna::updateOrCreate(['id' => $this->qna_id], [
            'qna' => $this->qna,
            'year' => $this->year,
            'description' => $this->description,
            'active' => $this->active_status,
            'cierre' => $this->cierre ?: null,
        ]);

        session()->flash('message', 
            $this->qna_id ? 'Quincena actualizada correctamente.' : 'Quincena creada correctamente.');
  
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $qnaModel = Qna::findOrFail($id);
        $this->qna_id = $id;
        $this->qna = $qnaModel->qna;
        $this->year = $qnaModel->year;
        $this->description = $qnaModel->description;
        $this->active_status = (string)$qnaModel->active;
        $this->cierre = $qnaModel->cierre;
    
        $this->openModal();
    }

    public function delete($id)
    {
        $qna = Qna::find($id);
        if ($qna && $qna->active == '1') {
            $qna->delete();
            session()->flash('message', 'Quincena eliminada correctamente.');
        } else {
            session()->flash('error', 'No se puede eliminar una Quincena que ya ha sido cerrada.');
        }
    }

    #[Computed]
    public function qnas()
    {
        return Qna::orderBy('year', 'desc')->orderBy('qna', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.qnas.index');
    }
}
