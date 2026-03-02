<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen"
    x-data="{ openYear: false, openMonth: false, openDept: false, searchDept: '' }">
    {{-- Header del Reporte --}}
    <div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Reporte <span
                    class="text-oro">Sin Derecho</span></h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Personal con incidencias críticas en el periodo.
            </p>
        </div>

        <div
            class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
            {{-- Año --}}
            <div class="relative w-full lg:w-[120px]" @click.away="openYear = false">
                <button @click="openYear = !openYear" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    <span class="truncate">{{ empty($year) ? 'Año' : $year }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openYear}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openYear"
                    class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($years as $yr)
                        <div wire:click="$set('year', '{{ $yr }}')" @click="openYear = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $year == $yr ? 'bg-oro/10 text-oro' : '' }}">
                            {{ $yr }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Mes --}}
            <div class="relative w-full lg:w-[150px]" @click.away="openMonth = false">
                <button @click="openMonth = !openMonth" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    <span class="truncate">{{ empty($month) ? 'Mes' : ($months[$month] ?? 'Mes') }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openMonth}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openMonth"
                    class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($months as $num => $mes)
                        <div wire:click="$set('month', '{{ $num }}')" @click="openMonth = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $month == $num ? 'bg-oro/10 text-oro' : '' }}">
                            {{ $mes }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Centro de Trabajo --}}
            <div class="relative w-full lg:w-[250px]" @click.away="openDept = false; searchDept = ''">
                <button @click="openDept = !openDept" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    @php
                    $selectedDeptLabel = 'Centro de Trabajo';
                    foreach($departments as $dept) if($dept->id == $departmentId) $selectedDeptLabel = $dept->code . ' -
                    ' . $dept->description;
                    @endphp
                    <span class="truncate">{{ $selectedDeptLabel }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openDept}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openDept"
                    class="absolute z-50 w-full lg:w-[350px] right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="p-2 border-b border-gray-50 dark:border-gray-700">
                        <input type="text" x-model="searchDept" placeholder="Buscar centro..."
                            class="w-full px-3 py-1.5 text-[10px] bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-lg outline-none font-bold uppercase">
                    </div>
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($departments as $dept)
                        <div wire:click="$set('departmentId', '{{ $dept->id }}')"
                            x-show="'{{ strtolower($dept->code . ' ' . $dept->description) }}'.includes(searchDept.toLowerCase())"
                            @click="openDept = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $departmentId == $dept->id ? 'bg-oro/10 text-oro' : '' }}">
                            [{{ $dept->code }}] {{ $dept->description }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button wire:click="generate" wire:loading.attr="disabled"
                class="h-[42px] px-6 bg-[#13322B] hover:bg-[#0a1b17] text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all flex items-center gap-2">
                <span wire:loading.remove wire:target="generate">Consultar</span>
                <span wire:loading wire:target="generate">...</span>
            </button>
        </div>
    </div>

    {{-- Vista Previa --}}
    @if($results !== null)
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden divide-y dark:divide-gray-700">
        <div class="p-5 bg-gray-50/50 dark:bg-gray-900/50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-6 bg-[#9b2247] rounded-full"></div>
                <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest">
                    Previsualización</h3>
            </div>
            <a href="{{ route('reports.sinderecho.pdf', ['year' => $year, 'month' => $month, 'departmentId' => $departmentId]) }}"
                target="_blank"
                class="px-4 py-2 bg-[#9b2247] hover:bg-[#7a1b38] text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-transform hover:scale-105 shadow-md shadow-[#9b2247]/20">
                Exportar PDF
            </a>
        </div>

        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/10">
                        <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Empleado
                        </th>
                        <th
                            class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($results as $emp)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase">{{
                                    $emp->fullname }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">ID: #{{
                                    $emp->num_empleado }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="showDetails({{ $emp->id }})"
                                class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[#13322B] dark:text-[#e6d194] hover:bg-[#13322B] hover:text-white transition-all shadow-sm mx-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2"
                            class="px-6 py-12 text-center text-xs font-black text-gray-400 uppercase tracking-widest">
                            Sin resultados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista Móvil (Tarjetas) --}}
        <div class="md:hidden flex flex-col divide-y divide-gray-100 dark:divide-gray-700/50">
            @forelse($results as $emp)
            <div class="p-4 bg-white dark:bg-gray-800 flex items-center justify-between gap-4">
                <div class="flex flex-col min-w-0">
                    <span class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase truncate">{{
                        $emp->fullname }}</span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight mt-1">ID: #{{
                        $emp->num_empleado }}</span>
                </div>
                <button wire:click="showDetails({{ $emp->id }})"
                    class="w-10 h-10 shrink-0 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[#13322B] dark:text-[#e6d194] hover:bg-[#13322B] hover:text-white transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @empty
            <div
                class="p-10 text-center text-xs font-black text-gray-400 uppercase tracking-widest bg-white dark:bg-gray-800">
                Sin resultados</div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- MODAL REPARADO (SIN DEGRADADO, FECHAS SEPARADAS) --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-[#13322B]/60 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

        {{-- Cuerpo del Modal --}}
        <div
            class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden bg-white dark:bg-gray-900 rounded-3xl shadow-2xl flex flex-col z-10 transition-all transform animate-in zoom-in-95 duration-200">
            {{-- Header --}}
            <div
                class="px-6 py-5 bg-[#13322B] text-white flex justify-between items-center relative overflow-hidden shrink-0 border-b border-white/5">
                <div class="relative z-20 flex items-center gap-3">
                    <div class="p-2 bg-white/10 rounded-lg text-[#e6d194]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest">Detalle de Incidencias</h3>
                        <p class="text-[9px] font-bold text-[#e6d194] uppercase tracking-widest opacity-80">{{
                            $selectedEmployeeName }}</p>
                    </div>
                </div>
                <button wire:click="closeModal"
                    class="relative z-20 p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Contenido con Scroll --}}
            <div class="p-0 overflow-y-auto flex-1 bg-gray-50/30 dark:bg-gray-950/30">
                <div class="p-4 sm:p-8">
                    <div
                        class="hidden md:block overflow-hidden border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm bg-white dark:bg-gray-800">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                                <tr>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Código</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Descripción</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Inicio</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Fin</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Días</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @forelse($selectedEmployeeIncidencias as $inc)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="px-2 py-0.5 bg-[#9b2247] text-white text-[10px] font-black rounded uppercase">
                                            {{ $inc->codigo->code }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-tight">
                                            {{ $inc->codigo->description }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                            {{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                            {{ \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm font-black text-[#9b2247] dark:text-[#e6d194]">
                                            {{ $inc->total_dias }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5"
                                        class="py-12 text-center text-gray-400 italic text-xs uppercase font-black tracking-widest">
                                        Sin detalles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Vista Móvil (Tarjetas) Modal --}}
                    <div class="md:hidden flex flex-col gap-3">
                        @forelse($selectedEmployeeIncidencias as $inc)
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 shadow-sm flex flex-col gap-3">
                            <div class="flex flex-col gap-2 border-b border-gray-100 dark:border-gray-700 pb-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 bg-[#9b2247] text-white text-[10px] font-black rounded uppercase inline-block">
                                        CÓDIGO: {{ $inc->codigo->code }}
                                    </span>
                                </div>
                                <span
                                    class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight leading-tight">
                                    {{ $inc->codigo->description }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inicio</span>
                                    <span
                                        class="text-xs font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                        {{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Fin</span>
                                    <span
                                        class="text-xs font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                        {{ \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between mt-1 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total
                                    Días:</span>
                                <span class="text-sm font-black text-[#9b2247] dark:text-[#e6d194]">{{ $inc->total_dias
                                    }}</span>
                            </div>
                        </div>
                        @empty
                        <div
                            class="py-12 text-center text-gray-400 italic text-xs uppercase font-black tracking-widest bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                            Sin detalles</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-5 bg-white dark:bg-gray-800 border-t dark:border-gray-700 shrink-0 flex justify-end">
                <button wire:click="closeModal"
                    class="px-10 py-3 bg-[#13322B] text-[#e6d194] text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-[#13322B]/20 hover:scale-105 transition-all">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    @endif
</div>