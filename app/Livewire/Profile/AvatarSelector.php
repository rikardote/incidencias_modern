<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AvatarSelector extends Component
{
    public $avatar;

    public function mount()
    {
        $this->avatar = Auth::user()->avatar ?: 'man';
    }

    public function selectAvatar($avatar)
    {
        $this->avatar = $avatar;
        
        $user = Auth::user();
        $user->avatar = $avatar;
        $user->save();

        // Dispatch a browser event so the navigation bar can update immediately
        $this->dispatch('avatar-updated', avatar: $avatar);
        
        $this->dispatch('notify', [
            'message' => 'Avatar actualizado correctamente',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.profile.avatar-selector');
    }
}
