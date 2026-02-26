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
    public $exceptionQnaId; // Resuelto automáticamente (QNA inmediata recién cerrada)
    public $exceptionQnaLabel; // Texto legible para mostrar en el modal
    public $exceptionDuration = 15;
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
            'selectedDepartments' => 'array',
        ];

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
        $user->departments()->sync($this->selectedDepartments);

        $this->showModal = false;
        $this->dispatch('notify', [
            'message' => $this->userId ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente',
            'type' => 'success',
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
            'type' => 'success',
        ]);
    }

    public function grantException($id)
    {
        $user = User::findOrFail($id);
        $this->exceptionUserId = $id;
        $this->exceptionUserName = $user->name;
        $this->exceptionDuration = 15;
        $this->exceptionReason = 'Corrección de captura urgente';
        $this->resetValidation();

        // Resolver automáticamente: SOLO la QNA inmediatamente recién cerrada
        // (la de mayor year + qna que tenga active = '0')
        $lastClosed = \App\Models\Qna::where('active', '0')
            ->orderBy('year', 'desc')
            ->orderBy('qna', 'desc')
            ->first();

        if ($lastClosed) {
            $this->exceptionQnaId = $lastClosed->id;
            $this->exceptionQnaLabel = 'QNA ' . $lastClosed->qna . '/' . $lastClosed->year
                . ($lastClosed->description ? ' — ' . $lastClosed->description : '');
        }
        else {
            $this->exceptionQnaId = null;
            $this->exceptionQnaLabel = null;
        }

        $this->showExceptionModal = true;
    }

    public function saveException()
    {
        // Re-resolver en el momento del submit para evitar manipulaciones
        $lastClosed = \App\Models\Qna::where('active', '0')
            ->orderBy('year', 'desc')
            ->orderBy('qna', 'desc')
            ->first();

        if (!$lastClosed) {
            $this->dispatch('notify', [
                'message' => 'No existe ninguna quincena cerrada para desbloquear.',
                'type' => 'error',
            ]);
            $this->showExceptionModal = false;
            return;
        }

        // Forzamos siempre el ID correcto (el sistema decide, no el usuario)
        $this->exceptionQnaId = $lastClosed->id;

        $this->validate([
            'exceptionQnaId' => 'required|exists:qnas,id',
            'exceptionDuration' => 'required|numeric|min:1|max:30',
            'exceptionReason' => 'required|string|max:255',
        ]);

        \App\Models\CaptureException::updateOrCreate(
        ['user_id' => $this->exceptionUserId, 'qna_id' => (int)$this->exceptionQnaId],
        [
            'expires_at' => now()->addMinutes((int)$this->exceptionDuration),
            'reason' => $this->exceptionReason,
        ]
        );

        $this->showExceptionModal = false;
        $this->dispatch('notify', [
            'message' => 'Pase otorgado para QNA ' . $lastClosed->qna . '/' . $lastClosed->year
            . ' por ' . $this->exceptionDuration . ' min.',
            'type' => 'success',
        ]);
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', [
                'message' => 'No puedes desactivar tu propia cuenta',
                'type' => 'error',
            ]);
            return;
        }

        $user->active = !$user->active;
        $user->save();

        $this->dispatch('notify', [
            'message' => 'Estado del usuario actualizado',
            'type' => 'success',
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
            ->where(function ($query) {
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