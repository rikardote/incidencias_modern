<div>
    {{-- Barra de búsqueda y acciones --}}
    <div class="flex flex-col xl:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex flex-col md:flex-row flex-1 gap-4 w-full">
            {{-- Buscador --}}
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por número de empleado o nombre..."
                    class="w-full pl-10 pr-10 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#13322B]/10 focus:border-[#13322B] text-sm shadow-sm transition-all"
                    autocomplete="off">
                <div class="absolute left-3.5 top-3 text-oro">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Selector de Departamento --}}
            <div class="w-full md:w-72 relative group">
                <div
                    class="absolute left-3.5 top-3 text-gray-400 group-hover:text-[#13322B] transition-colors pointer-events-none">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <select wire:model.live="selectedDepartment"
                    class="w-full pl-10 pr-10 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#13322B]/10 focus:border-[#13322B] text-sm shadow-sm transition-all appearance-none cursor-pointer">
                    <option value="">Todos los Departamentos</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->description }}</option>
                    @endforeach
                </select>
                <div class="absolute right-3.5 top-3.5 text-gray-400 pointer-events-none">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full xl:w-auto justify-end">
            @if(!empty($search) || !empty($selectedDepartment) || $listAll)
            <span
                class="text-[10px] font-black uppercase tracking-tighter text-gray-400 dark:text-gray-500 mr-2 whitespace-nowrap">
                {{ $employees->total() }} registros
            </span>
            @endif

            <button wire:click="toggleListAll"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $listAll ? 'bg-oro text-[#13322B] shadow-lg shadow-oro/20' : 'bg-gray-50 dark:bg-gray-800 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span class="hidden sm:inline">{{ $listAll ? 'Ocultar' : 'Mostrar Todos' }}</span>
            </button>

            {{-- Toggle Inactivos --}}
            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-xl border border-transparent hover:border-red-200 transition-all cursor-pointer select-none"
                wire:click="$set('showInactive', {{ $showInactive ? 'false' : 'true' }})">
                <div
                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $showInactive ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-700' }}">
                    <span
                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $showInactive ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </div>
                <span
                    class="text-[10px] font-black uppercase tracking-widest {{ $showInactive ? 'text-red-600' : 'text-gray-400' }}">Ver
                    Bajas</span>
            </div>

            <button wire:click="create"
                class="bg-[#13322B] hover:bg-[#1a4038] text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest flex items-center gap-2 transition-all shadow-lg shadow-[#13322B]/20 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nuevo</span>
            </button>
        </div>
    </div>

    {{-- Lista de empleados (Cards) --}}
    <div wire:loading.class="opacity-50" wire:target="search, selectedDepartment"
        class="transition-opacity duration-200 flex flex-col gap-3">
        @forelse($employees as $employee)
        <x-employee-card :employee="$employee" />
        @empty
        <div
            class="text-center py-20 bg-gray-50/50 dark:bg-gray-900/20 rounded-2xl border-2 border-dashed border-gray-100 dark:border-gray-800">
            @if(empty($search) && empty($selectedDepartment) && !$listAll)
            <div class="flex flex-col items-center gap-3">
                <div
                    class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl shadow-sm flex items-center justify-center text-oro">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-tight">Buscador de
                    Personal</h3>
                <p class="text-xs text-gray-400 max-w-xs mx-auto leading-relaxed">
                    Ingresa un número de empleado o nombre para comenzar, o utiliza la opción <span
                        class="text-oro font-bold">"Mostrar Todos"</span>.
                </p>
            </div>
            @else
            <div class="flex flex-col items-center gap-2">
                <svg class="w-12 h-12 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-gray-400 italic">No se encontraron empleados que coincidan con los criterios.</p>
            </div>
            @endif
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $employees->links() }}</div>

    {{-- MODAL DE REGISTRO / EDICION --}}
    @if($showEmployeeModal)
    <div x-data="{ tab: 'personal' }" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-[#13322B]/40 backdrop-blur-md" aria-hidden="true"
                wire:click="$set('showEmployeeModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-100 dark:border-gray-800 animate-fadeInScale">

                <div class="flex flex-col md:flex-row min-h-[600px]">
                    {{-- SIDEBAR DE PERFIL --}}
                    <div class="w-full md:w-80 bg-gradient-to-b from-[#13322B] to-[#1a4038] text-white p-8 flex flex-col relative overflow-hidden">
                        {{-- Elementos decorativos --}}
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                        
                        <div class="relative z-10 flex flex-col items-center md:items-start space-y-6">
                            {{-- Avatar --}}
                            <div class="w-24 h-24 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 flex items-center justify-center shadow-2xl">
                                @if($editingEmployeeId)
                                    <span class="text-3xl font-black text-[#e6d194] uppercase tracking-tighter">
                                        {{ strtoupper(mb_substr($name, 0, 1)) }}{{ strtoupper(mb_substr($father_lastname, 0, 1)) }}
                                    </span>
                                @else
                                    <svg class="w-12 h-12 text-[#e6d194]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                @endif
                            </div>

                            <div class="text-center md:text-left space-y-2">
                                <h3 class="text-xl font-black leading-tight tracking-tight uppercase">
                                    {{ $editingEmployeeId ? ($name . ' ' . $father_lastname) : 'Nuevo Empleado' }}
                                </h3>
                                @if($editingEmployeeId)
                                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                                        <span class="bg-[#e6d194] text-[#13322B] px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                            #{{ $num_empleado }}
                                        </span>
                                        <span class="bg-white/10 text-white/80 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border border-white/10 text-center">
                                            {{ $this->getGender() }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Información Secundaria --}}
                            <div class="w-full space-y-4 pt-6 border-t border-white/10 mt-2">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">CURP / RFC</p>
                                    <p class="text-xs font-mono font-bold text-[#e6d194] break-all leading-tight">
                                        {{ $curp ?: 'N/A' }}<br>
                                        <span class="text-white/60">{{ $rfc ?: 'N/A' }}</span>
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">Nivel</p>
                                        <p class="text-xs font-bold text-white/90">
                                            {{ $externalData['id_nivel'] ?? 'N/A' }}{{ isset($externalData['id_sub_nivel']) ? '/' . $externalData['id_sub_nivel'] : '' }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">Turno</p>
                                        <p class="text-xs font-bold text-white/90">
                                            {{ $externalData['id_turno'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">Pago</p>
                                    <p class="text-[10px] font-black text-white/60 uppercase leading-tight tracking-wider">
                                        {{ $externalData['id_forma_pago'] ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">Sindicato</p>
                                    @php
                                        $sindicatoDisplay = 'Ninguno';
                                        if ($externalData) {
                                            $nomina = $externalData['nomina_data'] ?? [];
                                            foreach (\App\Models\Employe::SINDICATOS_MAP as $key => $nameMap) {
                                                $val = $externalData[$key] ?? ($nomina[$key] ?? 0);
                                                if ((float)$val > 0) { $sindicatoDisplay = $nameMap; break; }
                                            }
                                        }
                                    @endphp
                                    <p class="text-xs font-bold {{ $sindicatoDisplay !== 'Ninguno' ? 'text-emerald-400' : 'text-white/60' }}">
                                        {{ $sindicatoDisplay }}
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">Estado</p>
                                    @if($active)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-emerald-400 uppercase">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-red-400 uppercase tracking-widest">
                                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                            Baja
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Botón Cerrar (en móvil) --}}
                        <button type="button" wire:click="$set('showEmployeeModal', false)"
                            class="absolute top-4 right-4 p-2 text-white/40 hover:text-white md:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- CONTENIDO PRINCIPAL --}}
                    <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 overflow-hidden relative">
                        {{-- Botón Cerrar (Escritorio) --}}
                        <button type="button" wire:click="$set('showEmployeeModal', false)"
                            class="absolute top-6 right-8 z-20 p-2 text-gray-300 hover:text-gray-600 dark:hover:text-white transition-colors hidden md:block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        {{-- NAVEGACION DE PESTAÑAS --}}
                        <div class="flex border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 px-8 pt-2">
                            <button type="button" @click="tab = 'personal'"
                                :class="tab === 'personal' ? 'border-[#13322B] text-[#13322B] dark:border-[#e6d194] dark:text-[#e6d194]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                                class="px-6 py-5 border-b-2 text-[11px] font-black uppercase tracking-widest transition-all">Identidad</button>
                            <button type="button" @click="tab = 'laboral'"
                                :class="tab === 'laboral' ? 'border-[#13322B] text-[#13322B] dark:border-[#e6d194] dark:text-[#e6d194]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                                class="px-6 py-5 border-b-2 text-[11px] font-black uppercase tracking-widest transition-all">Estructura</button>
                            <button type="button" @click="tab = 'seguridad'"
                                :class="tab === 'seguridad' ? 'border-[#13322B] text-[#13322B] dark:border-[#e6d194] dark:text-[#e6d194]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                                class="px-6 py-5 border-b-2 text-[11px] font-black uppercase tracking-widest transition-all">Admin y Bio</button>
                        </div>

                        <form wire:submit.prevent="save" class="flex-1 flex flex-col overflow-y-auto">
                            <div class="p-8 lg:p-10 flex-1">
                                <x-employee-modal.tab-personal />
                                <x-employee-modal.tab-laboral :departments="$departments" :puestos="$puestos"
                                    :horarios="$horarios" :jornadas="$jornadas" :condiciones="$condiciones"
                                    :externalData="$externalData" />
                                <x-employee-modal.tab-seguridad />
                            </div>
                            
                            <div class="px-8 py-6 border-t border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/30 sticky bottom-0">
                                <x-employee-modal.footer :editingEmployeeId="$editingEmployeeId" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>