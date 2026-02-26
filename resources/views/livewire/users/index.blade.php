<div>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 flex flex-col md:flex-row justify-between items-center bg-gray-50 border-b border-gray-200 dark:bg-gray-700/50 dark:border-gray-600 gap-4">
                    <div class="w-full md:w-1/3">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, usuario o email..." class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                    </div>
                    
                    <button wire:click="create" class="bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-2 rounded text-xs font-bold uppercase tracking-wider transition whitespace-nowrap shadow-sm">
                        + Nuevo Usuario
                    </button>
                </div>

                <div class="overflow-x-auto relative">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">Num. Empleado</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">Tipo</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">Estado</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition" wire:key="user-{{ $user->id }}">
                                <td class="px-4 py-4 text-center font-mono text-sm font-bold text-gray-900 dark:text-gray-100">
                                    {{ $user->username }}
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $user->name }}
                                    @if(auth()->id() === $user->id)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">Tú</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($user->type === 'admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Admin</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Usuario</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button wire:click="toggleActive({{ $user->id }})" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-oro rounded-full {{ auth()->id() === $user->id ? 'cursor-not-allowed opacity-50' : '' }}" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                        @if($user->active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 transition hover:bg-green-200">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 transition hover:bg-red-200">
                                                Inactivo
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-4 py-4 text-right text-sm">
                                    <button wire:click="grantException({{ $user->id }})" class="text-oro hover:underline font-bold uppercase tracking-wide text-xs mr-3 {{ $user->canCaptureInClosedQna() ? 'animate-pulse' : '' }}" title="Otorgar Pase de Captura Extemporánea">
                                        @if($user->canCaptureInClosedQna())
                                            ★ Pase Activo
                                        @else
                                            Pase
                                        @endif
                                    </button>
                                    <button wire:click="changePassword({{ $user->id }})" class="text-blue-600 dark:text-blue-400 hover:underline font-bold uppercase tracking-wide text-xs mr-3" title="Cambiar Contraseña">
                                        Contraseña
                                    </button>
                                    <button wire:click="edit({{ $user->id }})" class="text-[#9b2247] dark:text-[#e6d194] hover:underline font-bold uppercase tracking-wide text-xs">Editar</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No se encontraron usuarios que coincidan con la búsqueda.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Create / Edit -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity p-4">
        <div class="bg-white dark:bg-gray-800 rounded shadow-xl transform transition-all w-full max-w-2xl max-h-[90vh] flex flex-col">
            <form wire:submit.prevent="save" class="flex flex-col h-full overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wide">
                        {{ $userId ? 'Editar Usuario' : 'Nuevo Usuario' }}
                    </h3>
                </div>
                
                <div class="px-6 py-4 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Completo</label>
                            <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Núm. Empleado (Usuario)</label>
                            <input type="text" wire:model="username" id="username" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                            <input type="email" wire:model="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        @if(!$userId)
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                            <input type="password" wire:model="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nivel de Acceso</label>
                            <select wire:model="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                <option value="user">Usuario Básico</option>
                                <option value="admin">Administrador</option>
                            </select>
                            @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departamentos/Centros Asignados</label>
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
                            <select x-ref="select" multiple class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm text-sm">
                                @foreach($this->departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('selectedDepartments') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex flex-row-reverse rounded-b-lg border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="inline-flex justify-center rounded px-6 py-2 bg-[#13322B] text-xs font-bold text-white uppercase tracking-wider hover:bg-[#0a1f1a] shadow-sm ml-3">
                        Guardar
                    </button>
                    <button wire:click="$set('showModal', false)" type="button" class="inline-flex justify-center rounded border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 shadow-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal for Password Change -->
    @if($showPasswordModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity p-4">
        <div class="bg-white dark:bg-gray-800 rounded shadow-xl transform transition-all w-full max-w-md">
            <form wire:submit.prevent="updatePassword">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wide mb-4">
                        Cambiar Contraseña
                    </h3>
                    
                    <div class="mb-4">
                        <label for="newPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva Contraseña</label>
                        <input type="password" wire:model="newPassword" id="newPassword" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                        @error('newPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="newPasswordConfirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
                        <input type="password" wire:model="newPasswordConfirmation" id="newPasswordConfirmation" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex flex-row-reverse rounded-b-lg border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="inline-flex justify-center rounded px-6 py-2 bg-[#13322B] text-xs font-bold text-white uppercase tracking-wider hover:bg-[#0a1f1a] shadow-sm ml-3">
                        Actualizar
                    </button>
                    <button wire:click="$set('showPasswordModal', false)" type="button" class="inline-flex justify-center rounded border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 shadow-sm">
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl transform transition-all w-full max-w-md max-h-[90vh] overflow-y-auto">
            <form wire:submit.prevent="saveException">
                <div class="px-6 py-5">
                    <h3 class="text-lg font-bold text-[#a57f2c] dark:text-[#e6d194] uppercase tracking-wide mb-4">
                        Otorgar Pase de Captura
                    </h3>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Usuario: <span class="font-bold text-gray-900 dark:text-gray-100">{{ $exceptionUserName }}</span>
                    </p>

                    <div class="mb-4">
                        <label for="exceptionDuration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duración del pase</label>
                        <select wire:model="exceptionDuration" id="exceptionDuration" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            <option value="15">15 Minutos</option>
                            <option value="30">30 Minutos</option>
                            <option value="60">1 Hora</option>
                            <option value="120">2 Horas</option>
                            <option value="240">4 Horas</option>
                            <option value="480">8 Horas</option>
                            <option value="1440">24 Horas</option>
                        </select>
                        @error('exceptionDuration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-2">
                        <label for="exceptionReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo</label>
                        <input type="text" wire:model="exceptionReason" id="exceptionReason" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm" placeholder="Ej. Olvidó capturar incidencias de enfermería">
                        @error('exceptionReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <button type="submit" class="w-full justify-center rounded px-6 py-2.5 bg-[#a57f2c] text-xs font-bold text-white uppercase tracking-wider hover:bg-[#8e6b23] shadow-sm inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Habilitar Captura
                    </button>
                    <button wire:click="$set('showExceptionModal', false)" type="button" class="w-full justify-center rounded border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 shadow-sm inline-flex">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
