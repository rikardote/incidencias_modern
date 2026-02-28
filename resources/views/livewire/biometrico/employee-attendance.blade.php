<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-950">
    {{-- Header del Reporte --}}
    @if($employee)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#13322B] flex items-center justify-center shadow-lg shadow-[#13322B]/20 shrink-0">
                <span class="text-lg font-black text-[#e6d194]">
                    {{ strtoupper(mb_substr($employee->name, 0, 1)) }}{{ strtoupper(mb_substr($employee->father_lastname, 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight truncate">
                    {{ $employee->fullname }}
                </h2>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">
                    ID: #{{ $employee->num_empleado }} | {{ $employee->department->description ?? 'Sin Depto' }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Filtros del Reporte (Estilo Imagen) --}}
    <div class="space-y-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 divide-y divide-gray-50 dark:divide-gray-700">
            
            {{-- Año --}}
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Año Seleccionado</label>
                    <div x-data="{ open: false }" @click.away="open = false" class="relative group">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full py-2 pl-3 pr-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#9b2247]/30 focus:border-[#9b2247] transition-all shadow-sm outline-none">
                            <span class="truncate uppercase">{{ $año ?? 'Seleccione Año' }}</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#9b2247] transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden origin-top" 
                            style="display: none;">
                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                @foreach($años as $a)
                                <div @click="$wire.set('año', '{{ $a }}'); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $año == $a ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span>{{ $a }}</span>
                                    @if($año == $a)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quincena / Periodo --}}
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Quincena / Periodo</label>
                    @php
                        $qLabel = 'Seleccione Quincena';
                        foreach($quincenas as $q) {
                            if($q['value'] == $quincena) {
                                $qLabel = $q['label']; break;
                            }
                        }
                    @endphp
                    <div x-data="{ open: false }" @click.away="open = false" class="relative group">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full py-2 pl-3 pr-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#9b2247]/30 focus:border-[#9b2247] transition-all shadow-sm outline-none">
                            <span class="truncate uppercase">{{ $qLabel }}</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#9b2247] transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden origin-top" 
                            style="display: none;">
                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                @foreach($quincenas as $q)
                                <div @click="$wire.set('quincena', '{{ $q['value'] }}'); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $quincena == $q['value'] ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span>{{ $q['label'] }}</span>
                                    @if($quincena == $q['value'])
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón de Acción --}}
        <a href="{{ route('biometrico.individual.pdf', ['employeeId' => $employee->id, 'year' => $año, 'quincena' => $quincena]) }}"
           target="_blank"
            class="w-full bg-[#9b2247] hover:bg-[#7a1b38] text-white py-4 rounded-2xl text-xs font-black uppercase tracking-[0.3em] transition-all shadow-xl shadow-[#9b2247]/20 flex items-center justify-center gap-2 group">
            <svg class="w-4 h-4 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exportar a PDF
        </a>
    </div>

    {{-- Tabla de Checadas --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden divide-y dark:divide-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/10 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Día / Fecha</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Registros</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Incidencias</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($checadas as $c)
                    @php
                        $fecha = \Carbon\Carbon::parse($c->fecha);
                        $esFinDeSemana = $fecha->isWeekend();
                    @endphp
                    <tr class="@if($esFinDeSemana) bg-gray-50/20 dark:bg-gray-900/10 @endif hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
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
                                    <span class="text-xs font-mono font-bold @if($c->hora_entrada) text-green-600 @else text-gray-300 @endif">
                                        {{ $c->hora_entrada ? date('H:i', strtotime($c->primera_checada)) : '--:--' }}
                                    </span>
                                    <span class="text-[10px] text-gray-300">|</span>
                                    <span class="text-xs font-mono font-bold @if($c->num_checadas > 1) text-[#13322B] dark:text-[#e6d194] @else text-gray-300 @endif">
                                        {{ $c->num_checadas > 1 ? date('H:i', strtotime($c->ultima_checada)) : '--:--' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($c->incidencias)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(explode(',', $c->incidencias) as $code)
                                        <span class="px-2 py-0.5 bg-[#9b2247] text-white text-[9px] font-black rounded uppercase">
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
                    <tr><td colspan="3" class="px-6 py-10 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Sin registros</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>