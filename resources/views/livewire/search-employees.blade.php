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
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-800 animate-fadeInScale">

                <x-employee-modal.header :editingEmployeeId="$editingEmployeeId" :name="$name"
                    :father_lastname="$father_lastname" :num_empleado="$num_empleado" :curp="$curp"
                    :gender="$this->getGender()" />

                <div class="flex border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 px-8">
                    <button type="button" @click="tab = 'personal'"
                        :class="tab === 'personal' ? 'border-[#13322B] text-[#13322B] dark:border-[#e6d194] dark:text-[#e6d194]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="px-6 py-4 border-b-2 text-[11px] font-black uppercase tracking-widest transition-all">Identidad</button>
                    <button type="button" @click="tab = 'laboral'"
                        :class="tab === 'laboral' ? 'border-[#13322B] text-[#13322B] dark:border-[#e6d194] dark:text-[#e6d194]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="px-6 py-4 border-b-2 text-[11px] font-black uppercase tracking-widest transition-all">Estructura</button>
                    <button type="button" @click="tab = 'seguridad'"
                        :class="tab === 'seguridad' ? 'border-[#13322B] text-[#13322B] dark:border-[#e6d194] dark:text-[#e6d194]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="px-6 py-4 border-b-2 text-[11px] font-black uppercase tracking-widest transition-all">Admin
                        y Bio</button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="p-10 min-h-[400px]">
                        <x-employee-modal.tab-personal />
                        <x-employee-modal.tab-laboral :departments="$departments" :puestos="$puestos"
                            :horarios="$horarios" :jornadas="$jornadas" :condiciones="$condiciones"
                            :externalData="$externalData" />
                        <x-employee-modal.tab-seguridad />
                    </div>
                    <x-employee-modal.footer :editingEmployeeId="$editingEmployeeId" />
                </form>
            </div>
        </div>
    </div>
    @endif
</div>