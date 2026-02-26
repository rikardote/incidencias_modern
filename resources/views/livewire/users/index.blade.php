<div>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div
                    class="px-6 py-5 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-gray-100 dark:border-gray-700/50">
                    <div class="w-full md:w-1/2 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400 group-focus-within:text-oro transition-colors" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar por nombre, usuario o email..."
                            class="w-full pl-10 h-11 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all text-sm">
                    </div>

                    <button wire:click="create"
                        class="h-11 bg-[#13322B] hover:bg-[#1a4038] text-white px-8 rounded-xl text-xs font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-[#13322B]/20 hover:shadow-[#13322B]/40 active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Usuario
                    </button>
                </div>

                <div class="p-6">
                    <div wire:loading.class="opacity-50" wire:target="search"
                        class="transition-opacity duration-200 flex flex-col gap-3">
                        @forelse($users as $user)
                        <div wire:key="user-{{ $user->id }}"
                            class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md hover:border-[#13322B]/30 dark:hover:border-[#e6d194]/20 transition-all duration-200 overflow-hidden">

                            <div class="flex items-center gap-4 px-4 py-3">
                                {{-- Avatar --}}
                                <div
                                    class="shrink-0 w-10 h-10 rounded-full bg-[#13322B]/10 dark:bg-[#13322B]/40 flex items-center justify-center">
                                    <span class="text-sm font-black text-[#13322B] dark:text-[#e6d194] leading-none">
                                        @php
                                        $parts = explode(' ', trim($user->name));
                                        $initials = strtoupper(mb_substr($parts[0] ?? '', 0, 1));
                                        if (count($parts) > 1) {
                                        $initials .= strtoupper(mb_substr($parts[1], 0, 1));
                                        }
                                        @endphp
                                        {{ $initials }}
                                    </span>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="font-mono text-xs font-bold text-[#9b2247] dark:text-[#e6d194] shrink-0">
                                            #{{ $user->username }}
                                        </span>
                                        <span class="text-gray-200 dark:text-gray-600 text-xs text-bold">|</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase truncate">
                                            {{ $user->name }}
                                        </span>
                                        @if(auth()->id() === $user->id)
                                        <span
                                            class="shrink-0 px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase ml-1">Tú</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ $user->type === 'admin' ? 'Administrador del Sistema' : 'Usuario de
                                            Captura' }}
                                        </span>
                                        <span class="text-gray-200 dark:text-gray-600 text-xs">·</span>
                                        <button wire:click="toggleActive({{ $user->id }})"
                                            class="flex items-center gap-1 focus:outline-none {{ auth()->id() === $user->id ? 'cursor-not-allowed opacity-50' : 'hover:scale-105 transition-transform' }}"
                                            {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                            <div
                                                class="w-1 h-1 rounded-full {{ $user->active ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}">
                                            </div>
                                            <span
                                                class="text-[10px] font-black uppercase tracking-widest {{ $user->active ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $user->active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Acciones --}}
                                <div class="shrink-0 flex items-center gap-1 text-[#9b2247]">
                                    {{-- Pase --}}
                                    <button wire:click="grantException({{ $user->id }})"
                                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg {{ $user->canCaptureInClosedQna() ? 'bg-[#9b2247]/10 text-[#9b2247]' : 'text-gray-400 hover:bg-[#9b2247]/5 hover:text-[#9b2247]' }} transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        <span class="text-[8px] font-black uppercase tracking-tighter">Pase</span>
                                    </button>

                                    <div class="w-px h-6 bg-gray-100 dark:bg-gray-700 mx-1"></div>

                                    {{-- Seguridad --}}
                                    <button wire:click="changePassword({{ $user->id }})"
                                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span class="text-[8px] font-black uppercase tracking-tighter">Seguridad</span>
                                    </button>

                                    <div class="w-px h-6 bg-gray-100 dark:bg-gray-700 mx-1"></div>

                                    {{-- Editar --}}
                                    <button wire:click="edit({{ $user->id }})"
                                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="text-[8px] font-black uppercase tracking-tighter">Editar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-16 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 text-gray-400 mb-4">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 italic">No se encontraron usuarios para la búsqueda.</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-8 px-2">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Create / Edit -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" aria-hidden="true"
                wire:click="$set('showModal', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-white/20">
                <div
                    class="px-6 py-5 bg-gradient-to-r from-[#13322B] to-[#1e463d] text-white flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md text-[#e6d194]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-widest">
                            {{ $userId ? 'Ficha de Usuario' : 'Nuevo Usuario del Sistema' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="relative z-10 text-white opacity-60 hover:opacity-100 hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="flex flex-col h-full overflow-hidden">

                    <div class="px-8 py-6 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre
                                    Completo</label>
                                <input type="text" wire:model="name" id="name"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="username"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Núm. Empleado
                                    (Usuario)</label>
                                <input type="text" wire:model="username" id="username"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo
                                    Electrónico</label>
                                <input type="email" wire:model="email" id="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            @if(!$userId)
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                                <input type="password" wire:model="password" id="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div>
                                <label for="type"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nivel
                                    de Acceso</label>
                                <select wire:model="type" id="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                    <option value="user">Usuario Básico</option>
                                    <option value="admin">Administrador</option>
                                </select>
                                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departamentos/Centros
                                Asignados</label>
                            <div wire:ignore x-data="{
                                tom: null,
                                selected: @entangle('selectedDepartments'),
                                init() {
                                    this.tom = new window.TomSelect(this.$refs.select, {
                                        create: false,
                                        placeholder: '-- Seleccionar Centro(s) de Trabajo --',
                                        searchField: ['text'],
                                        plugins: ['remove_button'],
                                        maxOptions: 1000
                                    });
                                    
                                    this.tom.setValue(this.selected);

                                    this.tom.on('change', () => {
                                        // Extraemos correctamente los valores en forma de Array
                                        let values = Array.from(this.$refs.select.selectedOptions).map(opt => opt.value);
                                        this.selected = values;
                                    });
                                    
                                    this.$watch('selected', (value) => {
                                        let current = this.tom.getValue();
                                        let target = value ? value : [];
                                        if (current.toString() !== target.toString()) {
                                            this.tom.setValue(target);
                                        }
                                    });
                                }
                            }">
                                <select x-ref="select" multiple
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm text-sm">
                                    @foreach($this->departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->description }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('selectedDepartments') <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div
                        class="px-8 py-5 bg-gray-50 dark:bg-gray-900/80 flex flex-row-reverse rounded-b-2xl border-t dark:border-gray-700 backdrop-blur-md gap-4">
                        <button type="submit"
                            class="relative group bg-gradient-to-r from-[#13322B] to-[#1e463d] hover:from-[#1a4038] hover:to-[#245348] text-white px-10 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-[#13322B]/20 hover:shadow-[#13322B]/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 overflow-hidden">
                            <div
                                class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>
                            <span class="relative z-10">Guardar Cambios</span>
                        </button>
                        <button wire:click="$set('showModal', false)" type="button"
                            class="px-5 py-2 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-gray-800 dark:hover:text-gray-300 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Modal for Password Change -->
        @if($showPasswordModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" aria-hidden="true"
                    wire:click="$set('showPasswordModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-white/20">
                    <div
                        class="px-6 py-5 bg-gradient-to-r from-blue-900 to-blue-700 text-white flex justify-between items-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl">
                        </div>
                        <div class="relative z-10 flex items-center gap-3">
                            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md text-blue-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-black uppercase tracking-widest">Seguridad</h3>
                        </div>
                    </div>

                    <form wire:submit.prevent="updatePassword">
                        <div class="p-8">
                            <h3
                                class="text-lg font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight mb-6">
                                Renovar Contraseña
                            </h3>

                            <div class="mb-4">
                                <label for="newPassword"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva
                                    Contraseña</label>
                                <input type="password" wire:model="newPassword" id="newPassword"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('newPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="newPasswordConfirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar
                                    Contraseña</label>
                                <input type="password" wire:model="newPasswordConfirmation" id="newPasswordConfirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            </div>
                        </div>

                        <div
                            class="px-8 py-5 bg-gray-50 dark:bg-gray-900/80 flex flex-row-reverse rounded-b-2xl border-t dark:border-gray-700 backdrop-blur-md gap-4">
                            <button type="submit"
                                class="relative group bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-600 hover:to-blue-500 text-white px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <span class="relative z-10">Actualizar</span>
                            </button>
                            <button wire:click="$set('showPasswordModal', false)" type="button"
                                class="px-5 py-2 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-gray-800 dark:hover:text-gray-300 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Modal for Capture Exception (Grace Pass) -->
            @if($showExceptionModal)
            <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 transition-opacity p-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl transform transition-all w-full max-w-md max-h-[90vh] overflow-y-auto">
                    <form wire:submit.prevent="saveException">
                        <div class="px-6 py-5">
                            <h3
                                class="text-lg font-bold text-[#a57f2c] dark:text-[#e6d194] uppercase tracking-wide mb-1">
                                Otorgar Pase de Captura
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
                                Usuario: <span class="font-bold text-gray-900 dark:text-gray-100">{{ $exceptionUserName
                                    }}</span>
                            </p>

                            {{-- QNA resuelta automáticamente --}}
                            <div class="mb-5">
                                <label
                                    class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                    Quincena a desbloquear
                                </label>
                                @if($exceptionQnaId)
                                <div
                                    class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-600 rounded-lg px-4 py-3">
                                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-black text-amber-700 dark:text-amber-300">{{
                                            $exceptionQnaLabel
                                            }}</p>
                                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Solo se puede
                                            desbloquear
                                            la quincena inmediatamente recién cerrada.</p>
                                    </div>
                                </div>
                                @else
                                <div
                                    class="flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-600 rounded-lg px-4 py-3">
                                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-sm font-medium text-red-700 dark:text-red-300">No hay ninguna
                                        quincena
                                        cerrada disponible para desbloquear.</p>
                                </div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label for="exceptionDuration"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duración del
                                    pase</label>
                                <select wire:model="exceptionDuration" id="exceptionDuration"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm"
                                    {{ !$exceptionQnaId ? 'disabled' : '' }}>
                                    <option value="5">5 Minutos</option>
                                    <option value="10">10 Minutos</option>
                                    <option value="15">15 Minutos</option>
                                    <option value="20">20 Minutos</option>
                                    <option value="30">30 Minutos</option>
                                </select>
                                @error('exceptionDuration') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <label for="exceptionReason"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo</label>
                                <input type="text" wire:model="exceptionReason" id="exceptionReason"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm"
                                    placeholder="Ej. Olvidó capturar incidencias de enfermería" {{ !$exceptionQnaId
                                    ? 'disabled' : '' }}>
                                @error('exceptionReason') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="px-8 py-5 bg-gray-50 dark:bg-gray-900/80 flex flex-col gap-3 rounded-b-2xl border-t dark:border-gray-700 backdrop-blur-md">
                            <button type="submit"
                                class="w-full relative group bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white py-3 rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-amber-900/20 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 overflow-hidden flex items-center justify-center gap-2 {{ !$exceptionQnaId ? 'opacity-50 grayscale cursor-not-allowed' : '' }}"
                                {{ !$exceptionQnaId ? 'disabled' : '' }}>
                                <div
                                    class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <svg class="w-4 h-4 relative z-10" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="relative z-10">Habilitar Captura</span>
                            </button>
                            <button wire:click="$set('showExceptionModal', false)" type="button"
                                class="w-full py-2 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                Cerrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>