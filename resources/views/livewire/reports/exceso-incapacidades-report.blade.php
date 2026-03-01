<div class="py-12" x-data="{ 
    selectedEmployee: null,
    open: false,
    startLoading() {
        window.dispatchEvent(new CustomEvent('island-notif', { 
            detail: { message: 'Generando Reporte de Incapacidades...', type: 'info' } 
        }));
    }
}" x-init="startLoading()" wire:init="loadData">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    Exceso de <span class="text-guinda">Incapacidades</span>
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Reporte de empleados que exceden los límites legales de incapacidad según su antigüedad.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto mt-4 md:mt-0">
                @php
                $selectedDeptName = 'Todos los departamentos';
                if($selectedDepartment) {
                $found = collect($departments)->firstWhere('id', $selectedDepartment);
                if($found) $selectedDeptName = '[' . $found->code . '] ' . $found->description;
                }
                @endphp
                <div class="relative w-full sm:w-auto" @click.away="open = false">

                    <button @click="open = !open" type="button"
                        class="flex items-center justify-between w-full sm:w-[280px] py-2.5 pl-4 pr-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-guinda/50 focus:border-guinda transition-all shadow-sm outline-none">

                        <span class="truncate">{{ $selectedDeptName }}</span>

                        <svg class="w-5 h-5 text-gray-400 shrink-0 transition-transform duration-200 ml-2"
                            :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 w-full sm:w-[450px] sm:right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden origin-top-right sm:origin-top-right origin-top"
                        style="display: none;">

                        <div class="max-h-64 overflow-y-auto p-1.5 space-y-1">
                            <div wire:click="setDepartment('')" @click="open = false"
                                class="px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-all flex items-center justify-between {{ !$selectedDepartment ? 'bg-guinda/10 text-guinda dark:bg-guinda/20 dark:text-red-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-guinda' }}">
                                <span>Todos los departamentos</span>
                                @if(!$selectedDepartment)
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                @endif
                            </div>
                            @foreach($departments as $dept)
                            <div wire:click="setDepartment('{{ $dept->id }}')" @click="open = false"
                                class="px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-all flex items-center justify-between {{ $selectedDepartment == $dept->id ? 'bg-guinda/10 text-guinda dark:bg-guinda/20 dark:text-red-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-guinda' }}">
                                <span class="truncate pr-2 w-full">[{{ $dept->code }}] {{ $dept->description }}</span>
                                @if($selectedDepartment == $dept->id)
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button wire:click="loadData" @click="startLoading()" wire:loading.attr="disabled"
                    class="w-full sm:w-auto justify-center inline-flex items-center px-6 py-2.5 bg-guinda hover:bg-guinda-dark text-white font-semibold rounded-xl shadow-lg shadow-guinda/20 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                    <span wire:loading.remove wire:target="loadData">
                        Generar Reporte
                    </span>
                    <span wire:loading wire:target="loadData" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Procesando...
                    </span>
                </button>
            </div>
        </div>

        @if(!$loading)
        <!-- Stats Dashboard -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
            @php
            $c1 = collect($data)->filter(fn($i) => $i['antiguedad'] < 1)->count();
                $c2 = collect($data)->filter(fn($i) => $i['antiguedad'] >= 1 && $i['antiguedad'] <= 4)->count();
                    $c3 = collect($data)->filter(fn($i) => $i['antiguedad'] >= 5 && $i['antiguedad'] <= 9)->count();
                        $c4 = collect($data)->filter(fn($i) => $i['antiguedad'] >= 10)->count();
                        @endphp

                        <div
                            class="bg-white dark:bg-gray-800 p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                            <div
                                class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                < 1 año</div>
                                    <div class="flex items-end justify-between">
                                        <div class="text-xl md:text-2xl font-black text-guinda">{{ $c1 }}</div>
                                        <div class="text-[9px] md:text-[10px] text-gray-400">> 15 días</div>
                                    </div>
                            </div>

                            <div
                                class="bg-white dark:bg-gray-800 p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                                <div
                                    class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                    1 a 4 años</div>
                                <div class="flex items-end justify-between">
                                    <div class="text-xl md:text-2xl font-black text-guinda">{{ $c2 }}</div>
                                    <div class="text-[9px] md:text-[10px] text-gray-400">> 30 días</div>
                                </div>
                            </div>

                            <div
                                class="bg-white dark:bg-gray-800 p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                                <div
                                    class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                    5 a 9 años</div>
                                <div class="flex items-end justify-between">
                                    <div class="text-xl md:text-2xl font-black text-guinda">{{ $c3 }}</div>
                                    <div class="text-[9px] md:text-[10px] text-gray-400">> 45 días</div>
                                </div>
                            </div>

                            <div
                                class="bg-white dark:bg-gray-800 p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                                <div
                                    class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                    10+ años</div>
                                <div class="flex items-end justify-between">
                                    <div class="text-xl md:text-2xl font-black text-guinda">{{ $c4 }}</div>
                                    <div class="text-[9px] md:text-[10px] text-gray-400">> 60 días</div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Presentation (Table Desktop / Cards Mobile) -->
                        <div
                            class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                            <!-- Desktop Table View -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Empleado</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Antigüedad</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                                Días Total</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Período de Análisis</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                        @forelse($data as $num => $info)
                                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group cursor-pointer"
                                            @click="selectedEmployee === '{{ $num }}' ? selectedEmployee = null : selectedEmployee = '{{ $num }}'">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="flex-shrink-0 w-10 h-10 bg-guinda/10 rounded-full flex items-center justify-center text-guinda font-bold text-xs">
                                                        {{ $num }}
                                                    </div>
                                                    <div>
                                                        <div
                                                            class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-guinda transition-colors">
                                                            {{ $info['empleado']->name }} {{
                                                            $info['empleado']->father_lastname }} {{
                                                            $info['empleado']->mother_lastname }}
                                                        </div>
                                                        <div
                                                            class="text-xs text-gray-500 dark:text-gray-400 font-medium flex items-center gap-2">
                                                            {{ $info['empleado']->department->description ?? 'N/A' }}
                                                            @if($info['incapacidad_reciente'])
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[8px] font-black bg-amber-100 text-amber-700 border border-amber-200 uppercase tracking-tighter">
                                                                Reciente
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-xs font-bold {{ $info['antiguedad'] >= 10 ? 'bg-oro/10 text-oro border border-oro/20' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                                    {{ $info['antiguedad'] }} {{ Str::plural('año', $info['antiguedad'])
                                                    }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="text-lg font-black text-guinda leading-none">{{
                                                    $info['total_dias'] }}</div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">Días
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div
                                                    class="flex items-center gap-2 text-xs font-medium text-gray-600 dark:text-gray-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($info['periodo_inicio'])->format('d/m/Y')
                                                    }} — {{
                                                    \Carbon\Carbon::parse($info['periodo_final'])->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end transition-transform duration-200"
                                                    :class="selectedEmployee === '{{ $num }}' ? 'rotate-180' : ''">
                                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-guinda"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Desktop Details -->
                                        <tr x-show="selectedEmployee === '{{ $num }}'"
                                            x-transition:enter="transition ease-out duration-200"
                                            class="bg-gray-50/50 dark:bg-gray-900/20">
                                            <td colspan="5" class="px-6 py-8">
                                                @include('livewire.reports.partials.exceso-incapacidades-details',
                                                ['info' => $info, 'num' => $num])
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-20 text-center">
                                                @include('livewire.reports.partials.exceso-incapacidades-empty')</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($data as $num => $info)
                                <div class="p-4 transition-colors"
                                    :class="selectedEmployee === '{{ $num }}' ? 'bg-gray-50 dark:bg-gray-900/40' : ''">
                                    <div class="flex items-center justify-between cursor-pointer"
                                        @click="selectedEmployee === '{{ $num }}' ? selectedEmployee = null : selectedEmployee = '{{ $num }}'">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-guinda/10 rounded-full flex items-center justify-center text-guinda font-bold text-xs shrink-0">
                                                {{ $num }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                    {{ $info['empleado']->name }} {{ $info['empleado']->father_lastname
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[10px] text-gray-500 font-medium flex items-center gap-2">
                                                    {{ $info['antiguedad'] }} {{ Str::plural('año', $info['antiguedad'])
                                                    }} • {{ $info['total_dias'] }} días
                                                    @if($info['incapacidad_reciente'])
                                                    <span
                                                        class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[7px] font-black bg-amber-100 text-amber-700 border border-amber-200 uppercase tracking-tighter">
                                                        Reciente
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="transition-transform duration-200"
                                            :class="selectedEmployee === '{{ $num }}' ? 'rotate-180' : ''">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Mobile Details -->
                                    <div x-show="selectedEmployee === '{{ $num }}'"
                                        x-transition:enter="transition ease-out duration-200" style="display: none;"
                                        x-cloak class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        @include('livewire.reports.partials.exceso-incapacidades-details', ['info' =>
                                        $info, 'num' => $num])
                                    </div>
                                </div>
                                @empty
                                <div class="px-6 py-20 text-center">
                                    @include('livewire.reports.partials.exceso-incapacidades-empty')</div>
                                @endforelse
                            </div>
                        </div>
                        @else
                        <!-- Loading State -->
                        <div class="flex flex-col items-center justify-center py-24">
                            <div class="relative w-20 h-20">
                                <div class="absolute inset-0 rounded-full border-4 border-guinda/20"></div>
                                <div
                                    class="absolute inset-0 rounded-full border-4 border-guinda border-t-transparent animate-spin">
                                </div>
                            </div>
                            <div class="mt-6 text-sm font-bold text-gray-500 animate-pulse tracking-widest uppercase">
                                Consultando base de datos...</div>
                        </div>
                        @endif
        </div>
    </div>