<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen"
    x-data="{ openYear: false, openQna: false, openDept: false, searchDept: '' }">
    {{-- Header del Reporte --}}
    <div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Reporte <span
                    class="text-oro">RH5</span></h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Concentrado quincenal por centro de trabajo.</p>
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

            {{-- Quincena --}}
            <div class="relative w-full lg:w-[220px]" @click.away="openQna = false">
                <button @click="openQna = !openQna" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    @php
                    $selectedQnaLabel = 'Quincena';
                    foreach($qnas as $q) {
                    if($q->id == $qnaId) {
                    $selectedQnaLabel = 'QNA ' . str_pad($q->qna, 2, "0", STR_PAD_LEFT) . ($q->description ? ' (' .
                    mb_strtoupper($q->description) . ')' : '');
                    }
                    }
                    @endphp
                    <span class="truncate">{{ $selectedQnaLabel }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openQna}" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openQna"
                    class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($qnas as $q)
                        <div wire:click="$set('qnaId', '{{ $q->id }}')" @click="openQna = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $qnaId == $q->id ? 'bg-oro/10 text-oro' : '' }}">
                            QNA {{ str_pad($q->qna, 2, "0", STR_PAD_LEFT) }} {{ $q->description ? '(' .
                            mb_strtoupper($q->description) . ')' : '' }}
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

    {{-- Vista Previa RH5 --}}
    @if($results !== null)
    <div class="mb-4 flex items-center justify-between px-2 pt-4 border-t dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-[#9b2247] rounded-full"></div>
            <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest">
                Vista Previa del Reporte
            </h3>
        </div>

        <a href="{{ route('reports.rh5.pdf', ['qnaId' => $qnaId, 'departmentId' => $departmentId]) }}" target="_blank"
            class="flex items-center gap-2 px-4 py-2 bg-[#9b2247] hover:bg-[#7a1b38] text-white rounded-lg shadow-sm transition-all text-xs font-bold uppercase tracking-tight">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Exportar PDF
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden"
        wire:loading.class="opacity-60">
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest w-16 text-center">
                            #</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Empleado /
                            Concepto</th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Código</th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Inicio</th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Fin</th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Periodo</th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Días</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($results as $num => $data)
                    @foreach($data['items'] as $index => $item)
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors {{ $index === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/30 dark:bg-gray-900/20' }}">
                        <td class="px-5 py-4 text-center font-mono text-xs font-black text-gray-400">
                            {{ $index === 0 ? $num : '' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                @if($index === 0)
                                <span
                                    class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight mb-1">
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
                                <span
                                    class="text-[10px] text-[#9b2247] dark:text-[#e6d194] font-bold uppercase italic leading-tight">
                                    {{ $obs }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span
                                class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-[10px] font-black rounded border border-gray-200 dark:border-gray-600">
                                @if($item['code'] == 901) OT
                                @elseif($item['code'] == 905) PS
                                @elseif($item['code'] == 900) TXT
                                @else {{ str_pad($item['code'], 2, '0', STR_PAD_LEFT) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span
                                class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                {{ \Carbon\Carbon::parse($item['fecha_inicio'])->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span
                                class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">
                                {{ \Carbon\Carbon::parse($item['fecha_final'])->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span
                                class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span class="text-xs font-black uppercase tracking-widest">No se encontraron
                                    incidencias</span>
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
                    <span
                        class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[10px] font-black text-gray-500">{{
                        $num }}</span>
                    <span class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight">{{
                        $data['name'] }}</span>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach($data['items'] as $item)
                    <div
                        class="bg-gray-50/50 dark:bg-gray-900/40 rounded-xl p-3 border border-gray-100 dark:border-gray-700/50 relative">
                        <div class="flex justify-between items-start mb-2">
                            <span
                                class="px-2 py-1 bg-gray-200 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-[10px] font-black rounded border border-gray-300 dark:border-gray-600">
                                @if($item['code'] == 901) OT
                                @elseif($item['code'] == 905) PS
                                @elseif($item['code'] == 900) TXT
                                @else CODIGO {{ str_pad($item['code'], 2, '0', STR_PAD_LEFT) }}
                                @endif
                            </span>
                            <span class="text-sm font-black text-[#13322B] dark:text-[#e6d194]">{{ $item['total'] }}
                                Días</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <div class="flex flex-col">
                                <span
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Inicio</span>
                                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono">{{
                                    \Carbon\Carbon::parse($item['fecha_inicio'])->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Fin</span>
                                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono">{{
                                    \Carbon\Carbon::parse($item['fecha_final'])->format('d/m/Y') }}</span>
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
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Periodo: <span
                                    class="text-gray-900 dark:text-gray-100">{{ $item['periodo'] }}</span></span>
                            @endif
                            @if($obs)
                            <span
                                class="text-[10px] text-[#9b2247] dark:text-[#e6d194] font-bold uppercase italic leading-tight">{{
                                $obs }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-gray-400 italic text-xs uppercase font-black tracking-widest">Sin
                incidencias registradas</div>
            @endforelse
        </div>
    </div>
    @endif
</div>