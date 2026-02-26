<div>
    {{-- Barra de búsqueda y acciones --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="w-full md:w-1/2">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por número de empleado o nombre..."
                    class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-[#13322B] focus:border-[#13322B] text-sm shadow-sm transition"
                    autocomplete="off">
                <div class="absolute left-3 top-2.5 text-oro">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                {{ $employees->total() }} empleado(s) encontrado(s)
            </p>
            <button wire:click="create"
                class="bg-[#13322B] hover:bg-[#13322B]/90 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Empleado
            </button>
        </div>
    </div>

    {{-- Lista de empleados (Cards) --}}
    <div wire:loading.class="opacity-50" wire:target="search"
        class="transition-opacity duration-200 flex flex-col gap-3">
        @forelse($employees as $employee)
        <div wire:key="emp-{{ $employee->id }}"
            class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md hover:border-[#13322B]/30 dark:hover:border-[#e6d194]/20 transition-all duration-200 overflow-hidden">

            <div class="flex items-center gap-4 px-4 py-3">
                {{-- Avatar --}}
                <div
                    class="shrink-0 w-10 h-10 rounded-full bg-[#13322B]/10 dark:bg-[#13322B]/40 flex items-center justify-center">
                    <span class="text-sm font-black text-[#13322B] dark:text-[#e6d194] leading-none">
                        {{ strtoupper(mb_substr($employee->name, 0, 1)) }}{{
                        strtoupper(mb_substr($employee->father_lastname, 0, 1)) }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-xs font-bold text-[#9b2247] dark:text-[#e6d194] shrink-0">
                            {{ $employee->num_empleado }}
                        </span>
                        <span class="text-gray-200 dark:text-gray-600 text-xs">|</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase">
                            {{ $employee->fullname }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                            {{ $employee->department->description ?? 'Sin depto.' }}
                        </span>
                        @if($employee->puesto)
                        <span class="text-gray-200 dark:text-gray-600 text-xs">·</span>
                        <span class="text-[11px] text-[#13322B]/60 dark:text-[#e6d194]/60">
                            {{ $employee->puesto->puesto }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="shrink-0 flex items-center gap-1">
                    {{-- Incidencias --}}
                    <a href="{{ route('employees.incidencias', $employee->id) }}" wire:navigate
                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-[#9b2247] dark:text-[#e6d194] hover:bg-[#9b2247]/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="text-[8px] font-black uppercase tracking-tighter">Incidencias</span>
                    </a>

                    <div class="w-px h-6 bg-gray-100 dark:bg-gray-700"></div>

                    {{-- Biométrico --}}
                    <button type="button" onclick="alert('Módulo Biométrico próximamente')"
                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-500 hover:text-[#13322B] dark:hover:text-[#e6d194] hover:bg-[#13322B]/5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                        <span class="text-[8px] font-black uppercase tracking-tighter text-gray-400">Biométrico</span>
                    </button>

                    <div class="w-px h-6 bg-gray-100 dark:bg-gray-700"></div>

                    {{-- Información (Editar) --}}
                    <button wire:click="edit({{ $employee->id }})"
                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-[8px] font-black uppercase tracking-tighter text-gray-400">Información</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <p class="text-sm text-gray-400 italic">No se encontraron empleados.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $employees->links() }}</div>

    {{-- MODAL DE REGISTRO / EDICION --}}
    @if($showEmployeeModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" aria-hidden="true"
                wire:click="$set('showEmployeeModal', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full border border-white/20">
                <div
                    class="px-6 py-5 bg-gradient-to-r from-[#13322B] to-[#1e463d] text-white flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md">
                            <svg class="w-5 h-5 text-[#e6d194]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-widest">
                            {{ $editingEmployeeId ? 'Ficha de Empleado' : 'Registro de Personal' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showEmployeeModal', false)"
                        class="relative z-10 text-white opacity-60 hover:opacity-100 hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- SECCION 1: Identificación --}}
                            <div class="space-y-4">
                                <h4
                                    class="text-[10px] font-black uppercase text-[#9b2247] dark:text-[#e6d194] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#9b2247] dark:bg-[#e6d194]"></span>
                                    Identificación Básica
                                </h4>

                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">No. de
                                        Empleado</label>
                                    <input type="text" wire:model="num_empleado"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md focus:ring-[#13322B] focus:border-[#13322B]"
                                        maxlength="6">
                                    @error('num_empleado') <span class="text-red-500 text-[10px]">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-1">
                                    <label
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Nombre(s)</label>
                                    <input type="text" wire:model="name"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md focus:ring-[#13322B] focus:border-[#13322B]">
                                    @error('name') <span class="text-red-500 text-[10px]">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Apellido
                                        Paterno</label>
                                    <input type="text" wire:model="father_lastname"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md focus:ring-[#13322B] focus:border-[#13322B]">
                                    @error('father_lastname') <span class="text-red-500 text-[10px]">{{ $message
                                        }}</span> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Apellido
                                        Materno</label>
                                    <input type="text" wire:model="mother_lastname"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md focus:ring-[#13322B] focus:border-[#13322B]">
                                    @error('mother_lastname') <span class="text-red-500 text-[10px]">{{ $message
                                        }}</span> @enderror
                                </div>
                            </div>

                            {{-- SECCION 2: Laboral --}}
                            <div class="space-y-4">
                                <h4
                                    class="text-[10px] font-black uppercase text-[#9b2247] dark:text-[#e6d194] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#9b2247] dark:bg-[#e6d194]"></span>
                                    Ubicación y Perfil
                                </h4>

                                <div class="space-y-1">
                                    <label
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Departamento</label>
                                    <select wire:model="deparment_id"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                                        <option value="">-- Seleccionar --</option>
                                        @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->code }} - {{
                                            $d->description }}</option> @endforeach
                                    </select>
                                    @error('deparment_id') <span class="text-red-500 text-[10px]">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-1">
                                    <label
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Puesto</label>
                                    <select wire:model="puesto_id"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                                        <option value="">-- Seleccionar --</option>
                                        @foreach($puestos as $p) <option value="{{ $p->id }}">{{ $p->puesto }}</option>
                                        @endforeach
                                    </select>
                                    @error('puesto_id') <span class="text-red-500 text-[10px]">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <label
                                            class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Jornada</label>
                                        <select wire:model="jornada_id"
                                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md text-[11px]">
                                            <option value="">-- Sel --</option>
                                            @foreach($jornadas as $j) <option value="{{ $j->id }}">{{ $j->jornada }}
                                            </option> @endforeach
                                        </select>
                                        @error('jornada_id') <span class="text-red-500 text-[10px]">{{ $message
                                            }}</span> @enderror
                                    </div>
                                    <div class="space-y-1">
                                        <label
                                            class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Horario</label>
                                        <select wire:model="horario_id"
                                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md text-[11px]">
                                            <option value="">-- Sel --</option>
                                            @foreach($horarios as $h) <option value="{{ $h->id }}">{{ $h->horario }}
                                            </option> @endforeach
                                        </select>
                                        @error('horario_id') <span class="text-red-500 text-[10px]">{{ $message
                                            }}</span> @enderror
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Condición
                                        Lab.</label>
                                    <select wire:model="condicion_id"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                                        <option value="">-- Seleccionar --</option>
                                        @foreach($condiciones as $c) <option value="{{ $c->id }}">{{ $c->condicion }}
                                        </option> @endforeach
                                    </select>
                                    @error('condicion_id') <span class="text-red-500 text-[10px]">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- SECCION 3: Otros --}}
                            <div class="space-y-4">
                                <h4
                                    class="text-[10px] font-black uppercase text-[#9b2247] dark:text-[#e6d194] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#9b2247] dark:bg-[#e6d194]"></span>
                                    Control y Fechas
                                </h4>

                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <label
                                            class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Fecha
                                            Ingreso</label>
                                        <input type="date" wire:model="fecha_ingreso"
                                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                                        @error('fecha_ingreso') <span class="text-red-500 text-[10px]">{{ $message
                                            }}</span> @enderror
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">No.
                                            de Plaza</label>
                                        <input type="text" wire:model="num_plaza"
                                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">No.
                                        Seguro Social</label>
                                    <input type="text" wire:model="num_seguro"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md">
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border dark:border-gray-700 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label
                                            class="text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase">Estancia</label>
                                        <input type="checkbox" wire:model="estancia"
                                            class="rounded text-[#13322B] focus:ring-[#13322B]">
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label
                                            class="text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase">Comisionado</label>
                                        <input type="checkbox" wire:model="comisionado"
                                            class="rounded text-[#13322B] focus:ring-[#13322B]">
                                    </div>
                                    <div class="space-y-2 pt-1 border-t dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <label
                                                class="text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase">Lactancia</label>
                                            <input type="checkbox" wire:model.live="lactancia"
                                                class="rounded text-[#13322B] focus:ring-[#13322B]">
                                        </div>
                                        @if($lactancia)
                                        <div class="grid grid-cols-2 gap-2 animate-fadeIn">
                                            <div class="space-y-1">
                                                <label
                                                    class="text-[9px] font-bold text-gray-400 uppercase">Inicio</label>
                                                <input type="date" wire:model="lactancia_inicio"
                                                    class="w-full text-[10px] p-1 border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-[9px] font-bold text-gray-400 uppercase">Fin</label>
                                                <input type="date" wire:model="lactancia_fin"
                                                    class="w-full text-[10px] p-1 border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded">
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-900/80 px-8 py-5 flex justify-end gap-4 rounded-b-2xl border-t dark:border-gray-700 backdrop-blur-md">
                        <button type="button" wire:click="$set('showEmployeeModal', false)"
                            class="px-5 py-2 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-gray-800 dark:hover:text-gray-300 transition-colors">Cancelar</button>
                        <button type="submit"
                            class="relative group bg-gradient-to-r from-[#13322B] to-[#1e463d] hover:from-[#1a4038] hover:to-[#245348] text-white px-10 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-[#13322B]/20 hover:shadow-[#13322B]/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 overflow-hidden">
                            <div
                                class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>
                            <span class="relative z-10">{{ $editingEmployeeId ? 'Actualizar Ficha' : 'Dar de Alta'
                                }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>