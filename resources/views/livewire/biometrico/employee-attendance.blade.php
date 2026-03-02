<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-950">
    {{-- Header del Reporte --}}
    {{-- Header del Reporte --}}
    <div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
        <div class="flex items-center gap-5">
            <div>
                <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">
                    Asistencia <span class="text-oro">Individual</span></h1>
                <div class="flex items-center gap-3 mt-1 flex-wrap">
                    <span
                        class="px-2.5 py-1 bg-[#9B2247]/10 text-[#9B2247] dark:text-oro text-[10px] font-black rounded-lg uppercase tracking-wider border border-[#9b2247]/20 shadow-sm">
                        #{{ $employee->num_empleado }}
                    </span>
                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">
                        {{ $employee->fullname }}
                    </span>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
            {{-- Año --}}
            <div class="relative w-full lg:w-[120px]" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    <span class="truncate">{{ $año ?? date('Y') }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': open}" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open"
                    class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($años as $a)
                        <div wire:click="$set('año', '{{ $a }}')" @click="open = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $año == $a ? 'bg-oro/10 text-oro' : '' }}">
                            {{ $a }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Quincena --}}
            <div class="relative w-full lg:w-[220px]" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    @php
                    $qLabel = 'Quincena';
                    foreach($quincenas as $q) if($q['value'] == $quincena) $qLabel = $q['label'];
                    @endphp
                    <span class="truncate text-left">{{ $qLabel }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': open}" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open"
                    class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($quincenas as $q)
                        <div wire:click="$set('quincena', '{{ $q['value'] }}')" @click="open = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $quincena == $q['value'] ? 'bg-oro/10 text-oro' : '' }}">
                            {{ $q['label'] }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('biometrico.individual.pdf', ['employeeId' => $employee->id, 'year' => $año, 'quincena' => $quincena]) }}"
                    target="_blank"
                    class="h-[42px] px-5 bg-[#9b2247] hover:bg-[#7a1b38] text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all flex items-center justify-center gap-2">
                    PDF
                </a>
                <a href="{{ route('employees.index') }}" wire:navigate
                    class="h-[42px] px-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-xl shadow-sm transition-all flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Tabla de Checadas --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden divide-y dark:divide-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/10 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Día / Fecha
                        </th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Registros</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Incidencias
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($checadas as $c)
                    @php
                    $fecha = \Carbon\Carbon::parse($c->fecha);
                    $esFinDeSemana = $fecha->isWeekend();
                    @endphp
                    <tr
                        class="@if($esFinDeSemana) bg-gray-50/20 dark:bg-gray-900/10 @endif hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase text-gray-400">
                                    {{ mb_strtoupper($fecha->translatedFormat('l')) }}
                                </span>
                                <span class="text-sm font-black text-gray-800 dark:text-gray-100 tracking-tighter">
                                    {{ $fecha->format('d/m/Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex gap-2">
                                    <span
                                        class="text-xs font-mono font-bold @if($c->hora_entrada) text-green-600 @else text-gray-300 @endif">
                                        {{ $c->hora_entrada ? date('H:i', strtotime($c->primera_checada)) : '--:--' }}
                                    </span>
                                    <span class="text-[10px] text-gray-300">|</span>
                                    <span
                                        class="text-xs font-mono font-bold @if($c->num_checadas > 1) text-[#13322B] dark:text-[#e6d194] @else text-gray-300 @endif">
                                        {{ $c->num_checadas > 1 ? date('H:i', strtotime($c->ultima_checada)) : '--:--'
                                        }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($c->incidencias)
                            <div class="flex flex-wrap gap-1">
                                @foreach(explode(',', $c->incidencias) as $code)
                                <span
                                    class="px-2 py-0.5 bg-[#9b2247] text-white text-[9px] font-black rounded uppercase">
                                    {{ trim($code) }}
                                </span>
                                @endforeach
                            </div>
                            @elseif(!$c->hora_entrada && !$esFinDeSemana && $fecha->isPast())
                            <span class="text-[9px] font-black text-red-400 uppercase italic">Falta</span>
                            @else
                            <span class="text-[9px] text-gray-300 uppercase font-black italic">--</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3"
                            class="px-6 py-10 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Sin registros</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>