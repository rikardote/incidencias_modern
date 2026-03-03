<?php

namespace App\Livewire\System;

use App\Models\Configuration;
use Livewire\Component;

class ChatConfiguration extends Component
{
    public $allToAll = false;

    public function mount()
    {
        if (!auth()->user()->admin()) {
            abort(403);
        }
        $this->allToAll = Configuration::get('chat_all_to_all', false);
    }

    public function toggleAllToAll()
    {
        if (!auth()->user()->admin()) return;

        $this->allToAll = !$this->allToAll;
        Configuration::set('chat_all_to_all', $this->allToAll);

        $this->dispatch('toast', [
            'icon' => $this->allToAll ? 'success' : 'info',
            'title' => $this->allToAll ? 'Chat TODOS-TODOS Activado' : 'Chat restringido a administradores',
        ]);
    }

    public function render()
    {
        return view('livewire.system.chat-configuration');
    }
}
