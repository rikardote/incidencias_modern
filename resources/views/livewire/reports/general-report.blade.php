<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-950">
    {{-- Header del Reporte --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#13322B] flex items-center justify-center shadow-lg shadow-[#13322B]/20 shrink-0">
                <svg class="w-6 h-6 text-[#e6d194]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">
                    Reporte General de Incidencias (RH5)
                </h2>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">
                    Concentrado quincenal por centro de trabajo
                </p>
            </div>
        </div>
    </div>

    {{-- Filtros del Reporte (Estilo Vertical Stacked) --}}
    <div class="space-y-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 divide-y divide-gray-50 dark:divide-gray-700">
            
            {{-- Año --}}
            <div class="flex items-center gap-4 p-4 group">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Año</label>
                    <div x-data="{ open: false }" @click.away="open = false" class="relative group">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full py-2 pl-3 pr-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#9b2247]/30 focus:border-[#9b2247] transition-all shadow-sm outline-none">
                            <span class="truncate uppercase">{{ empty($year) ? 'Seleccione Año' : $year }}</span>
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
                                <div @click="$wire.set('year', ''); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ empty($year) ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span>Seleccione Año</span>
                                    @if(empty($year))
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @foreach($years as $yr)
                                <div @click="$wire.set('year', '{{ $yr }}'); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $year == $yr ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span>{{ $yr }}</span>
                                    @if($year == $yr)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quincena --}}
            <div class="flex items-center gap-4 p-4 group">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Quincena</label>
                    @php
                        $qLabel = 'Seleccione Quincena';
                        foreach($qnas as $qna) {
                            if($qna->id == $qnaId) {
                                $qLabel = 'QNA ' . str_pad($qna->qna, 2, "0", STR_PAD_LEFT); break;
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
                                <div @click="$wire.set('qnaId', ''); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ empty($qnaId) ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span>Seleccione Quincena</span>
                                    @if(empty($qnaId))
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @foreach($qnas as $qna)
                                <div @click="$wire.set('qnaId', '{{ $qna->id }}'); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $qnaId == $qna->id ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span>QNA {{ str_pad($qna->qna, 2, "0", STR_PAD_LEFT) }}</span>
                                    @if($qnaId == $qna->id)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Centro de Trabajo --}}
            <div class="flex items-center gap-4 p-4 group">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Centro de Trabajo</label>
                    @php
                        $cLabel = 'Seleccione Centro';
                        foreach($departments as $dept) {
                            if($dept->id == $departmentId) {
                                $cLabel = $dept->code . ' - ' . $dept->description; break;
                            }
                        }
                    @endphp
                    <div x-data="{ open: false, search: '' }" @click.away="open = false; search = ''" class="relative group">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full py-2 pl-3 pr-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#9b2247]/30 focus:border-[#9b2247] transition-all shadow-sm outline-none">
                            <span class="truncate uppercase text-left">{{ $cLabel }}</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#9b2247] transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            
                            {{-- Buscador Integrado --}}
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                                <input type="text" x-model="search" placeholder="BUSCAR CENTRO..." 
                                    class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-black uppercase text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-[#9b2247]/30 focus:border-[#9b2247] outline-none placeholder-gray-400 transition-all shadow-sm">
                            </div>

                            <div class="max-h-52 overflow-y-auto p-1.5 space-y-1">
                                <div @click="$wire.set('departmentId', ''); open = false; search = ''"
                                    x-show="'seleccione centro'.includes(search.toLowerCase()) || search === ''"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ empty($departmentId) ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span>Seleccione Centro</span>
                                    @if(empty($departmentId))
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @foreach($departments as $dept)
                                <div @click="$wire.set('departmentId', '{{ $dept->id }}'); open = false; search = ''"
                                    x-show="'{{ strtolower($dept->code . ' ' . $dept->description) }}'.includes(search.toLowerCase())"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $departmentId == $dept->id ? 'bg-[#9b2247] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#9b2247]' }}">
                                    <span class="truncate pr-2 w-full">{{ $dept->code }} - {{ $dept->description }}</span>
                                    @if($departmentId == $dept->id)
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
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
        <button wire:click="generate" 
            class="w-full bg-[#13322B] hover:bg-[#0a1f1a] text-white py-4 rounded-2xl text-xs font-black uppercase tracking-[0.3em] transition-all shadow-xl shadow-[#13322B]/20 flex items-center justify-center gap-2 disabled:opacity-50">
            <span wire:loading.remove wire:target="generate">Consultar RH5</span>
            <span wire:loading wire:target="generate" class="flex items-center gap-2">
                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>
    </div>

    {{-- Vista Previa RH5 --}}
    @if($results !== null)
    <div class="mb-4 flex items-center justify-between px-2 pt-4 border-t dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-[#9b2247] rounded-full"></div>
            <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest">
                Vista Previa del Reporte
            </h3>
        </div>
        
        <a href="{{ route('reports.rh5.pdf', ['qnaId' => $qnaId, 'departmentId' => $departmentId]) }}"
           target="_blank"
           class="flex items-center gap-2 px-4 py-2 bg-[#9b2247] hover:bg-[#7a1b38] text-white rounded-lg shadow-sm transition-all text-xs font-bold uppercase tracking-tight">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Exportar PDF
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" wire:loading.class="opacity-60">
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest w-16 text-center">#</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Empleado / Concepto</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Código</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Inicio</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Fin</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Periodo</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Días</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($results as $num => $data)
                        @foreach($data['items'] as $index => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors {{ $index === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/30 dark:bg-gray-900/20' }}">
                            <td class="px-5 py-4 text-center font-mono text-xs font-black text-gray-400">
                                {{ $index === 0 ? $num : '' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    @if($index === 0)
                                        <span class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight mb-1">
                                            {{ $data['name'] }}
                                        </span>
                                    @endif
                                    @php
                                        $obs = collect([
                                            $item['otorgado'],
                                            $item['becas_comments'],
                                            $item['horas_otorgadas'],
                                            ($item['code'] == 900 && $item['autoriza_txt']) ? $item['autoriza_txt'] : null
                                        ])->filter()->first();
                                    @endphp
                                    @if($obs)
                                        <span class="text-[10px] text-[#9b2247] dark:text-[#e6d194] font-bold uppercase italic leading-tight">
                                            {{ $obs }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-[10px] font-black rounded border border-gray-200 dark:border-gray-600">
                                    @if($item['code'] == 901) OT
                                    @elseif($item['code'] == 905) PS
                                    @elseif($item['code'] == 900) TXT
                                    @else {{ str_pad($item['code'], 2, '0', STR_PAD_LEFT) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                    {{ \Carbon\Carbon::parse($item['fecha_inicio'])->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                    {{ \Carbon\Carbon::parse($item['fecha_final'])->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    {{ $item['periodo'] ?: '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-xs font-black text-[#13322B] dark:text-[#e6d194]">
                                    {{ $item['total'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="w-8 h-8 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span class="text-xs font-black uppercase tracking-widest">No se encontraron incidencias</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista Móvil (Tarjetas) --}}
        <div class="md:hidden flex flex-col divide-y divide-gray-100 dark:divide-gray-700/50">
            @forelse($results as $num => $data)
                <div class="p-4 bg-white dark:bg-gray-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[10px] font-black text-gray-500">{{ $num }}</span>
                        <span class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight">{{ $data['name'] }}</span>
                    </div>
                    <div class="flex flex-col gap-3">
                        @foreach($data['items'] as $item)
                            <div class="bg-gray-50/50 dark:bg-gray-900/40 rounded-xl p-3 border border-gray-100 dark:border-gray-700/50 relative">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-[10px] font-black rounded border border-gray-300 dark:border-gray-600">
                                        @if($item['code'] == 901) OT
                                        @elseif($item['code'] == 905) PS
                                        @elseif($item['code'] == 900) TXT
                                        @else CODIGO {{ str_pad($item['code'], 2, '0', STR_PAD_LEFT) }}
                                        @endif
                                    </span>
                                    <span class="text-sm font-black text-[#13322B] dark:text-[#e6d194]">{{ $item['total'] }} Días</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Inicio</span>
                                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono">{{ \Carbon\Carbon::parse($item['fecha_inicio'])->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Fin</span>
                                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono">{{ \Carbon\Carbon::parse($item['fecha_final'])->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                @php
                                    $obs = collect([
                                        $item['otorgado'],
                                        $item['becas_comments'],
                                        $item['horas_otorgadas'],
                                        ($item['code'] == 900 && $item['autoriza_txt']) ? $item['autoriza_txt'] : null
                                    ])->filter()->first();
                                @endphp
                                @if($obs || $item['periodo'])
                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700 flex flex-col gap-1">
                                    @if($item['periodo'])
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Periodo: <span class="text-gray-900 dark:text-gray-100">{{ $item['periodo'] }}</span></span>
                                    @endif
                                    @if($obs)
                                    <span class="text-[10px] text-[#9b2247] dark:text-[#e6d194] font-bold uppercase italic leading-tight">{{ $obs }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-gray-400 italic text-xs uppercase font-black tracking-widest">Sin incidencias registradas</div>
            @endforelse
        </div>
    </div>
    @endif
</div>