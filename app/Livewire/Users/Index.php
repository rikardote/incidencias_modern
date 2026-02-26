<?php

namespace App\Livewire\Users;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    
    // Modal state
    public $showModal = false;
    public $showPasswordModal = false;
    public $showExceptionModal = false;
    
    // Form data
    public $userId;
    public $name;
    public $username;
    public $email;
    public $password;
    public $type = 'user';
    public $active = true;
    public $selectedDepartments = [];

    // Exception data
    public $exceptionUserId;
    public $exceptionUserName;
    public $exceptionQnaId;
    public $exceptionDuration = 60; // default 60 minutes
    public $exceptionReason = 'Corrección de captura urgente';

    // Password change
    public $newPassword;
    public $newPasswordConfirmation;

    public function mount()
    {
        if (!auth()->user()->admin()) {
            return redirect()->route('dashboard');
        }
    }

    public function updatingSearch()
    {
        // Reset pagination when searching
        $this->resetPage();
    }

    public function getDepartmentsProperty()
    {
        return Department::orderBy('description')->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        
        $user = User::with('departments')->findOrFail($id);
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->type = $user->type;
        $this->active = $user->active;
        $this->selectedDepartments = $user->departments->pluck('id')->toArray();
        
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $this->userId,
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
            'type' => 'required|in:admin,user',
            'selectedDepartments' => 'array'
        ];

        // Solo requerir password si es usuario nuevo
        if (!$this->userId) {
            $rules['password'] = 'required|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'type' => $this->type,
            'active' => $this->active,
        ];

        if (!$this->userId && $this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);
        
        // Sync departments
        $user->departments()->sync($this->selectedDepartments);

        $this->showModal = false;
        
        $this->dispatch('notify', [
            'message' => $this->userId ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente',
            'type' => 'success'
        ]);
        
        $this->resetForm();
    }

    public function changePassword($id)
    {
        $this->resetValidation();
        $this->userId = $id;
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->showPasswordModal = true;
    }

    public function updatePassword()
    {
        $this->validate([
            'newPassword' => 'required|string|min:6|same:newPasswordConfirmation',
        ]);

        $user = User::findOrFail($this->userId);
        $user->password = Hash::make($this->newPassword);
        $user->save();

        $this->showPasswordModal = false;
        
        $this->dispatch('notify', [
            'message' => 'Contraseña actualizada exitosamente',
            'type' => 'success'
        ]);
    }

    public function grantException($id)
    {
        $user = User::findOrFail($id);
        $this->exceptionUserId = $id;
        $this->exceptionUserName = $user->name;
        $this->showExceptionModal = true;
    }

    public function saveException()
    {
        $this->validate([
            'exceptionQnaId' => 'required|exists:qnas,id',
            'exceptionDuration' => 'required|numeric|min:1|max:1440',
            'exceptionReason' => 'required|string|max:255',
        ]);

        $qna = \App\Models\Qna::find($this->exceptionQnaId);

        \App\Models\CaptureException::updateOrCreate(
            ['user_id' => $this->exceptionUserId, 'qna_id' => (int) $this->exceptionQnaId],
            [
                'expires_at' => now()->addMinutes((int) $this->exceptionDuration),
                'reason' => $this->exceptionReason,
            ]
        );

        $this->showExceptionModal = false;
        
        $this->dispatch('notify', [
            'message' => 'Pase otorgado para QNA ' . $qna->qna . '/' . $qna->year . ' por ' . $this->exceptionDuration . ' min',
            'type' => 'success'
        ]);
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        
        // No permitirse desactivar a si mismo
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', [
                'message' => 'No puedes desactivar tu propia cuenta',
                'type' => 'error'
            ]);
            return;
        }
        
        $user->active = !$user->active;
        $user->save();
        
        $this->dispatch('notify', [
            'message' => 'Estado del usuario actualizado',
            'type' => 'success'
        ]);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->userId = null;
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->type = 'user';
        $this->active = true;
        $this->selectedDepartments = [];
    }

    public function render()
    {
        $users = User::with('departments')
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('active', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}
