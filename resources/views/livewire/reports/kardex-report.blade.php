<div class="py-6 px-4 md:px-8 max-w-7xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-950">
    {{-- Header con Info del Empleado --}}
    @if($employee)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#13322B] flex items-center justify-center shadow-lg shadow-[#13322B]/20 shrink-0">
                    <span class="text-lg font-black text-[#e6d194]">
                        {{ strtoupper(mb_substr($employee->name, 0, 1)) }}{{ strtoupper(mb_substr($employee->father_lastname, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">
                        {{ $employee->fullname }}
                    </h2>
                    <div class="flex items-center gap-3 mt-1 flex-wrap">
                        <span class="px-2.5 py-1 bg-[#9b2247]/10 text-[#9b2247] dark:text-[#e6d194] text-[10px] font-black rounded-lg uppercase tracking-wider border border-[#9b2247]/20 shadow-sm">
                            #{{ $employee->num_empleado }}
                        </span>
                        <span class="text-gray-400 dark:text-gray-500 text-[11px] uppercase font-black tracking-tight">
                            {{ $employee->department->description ?? 'Sin Depto' }}
                        </span>
                        <span class="text-gray-200 dark:text-gray-700">|</span>
                        <span class="text-[11px] text-[#13322B] dark:text-[#e6d194]/60 font-black uppercase tracking-tight whitespace-nowrap">
                            {{ $employee->puesto->puesto ?? 'Sin Puesto' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('employees.index') }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg shadow-sm transition-all text-xs font-bold uppercase tracking-tight">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Filtros de Kardex --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
        <div class="flex flex-col md:flex-row items-center gap-4">
            {{-- Contenedor de Inputs --}}
            <div class="flex-1 flex flex-col md:flex-row items-center gap-2 bg-gray-50 dark:bg-gray-900/50 p-2 rounded-xl border dark:border-gray-700 w-full">
                
                {{-- Fecha Inicio --}}
                <div class="flex-1 flex items-center gap-3 px-3 py-2 w-full">
                    <div class="shrink-0 w-8 h-8 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 cursor-pointer" onclick="this.nextElementSibling.querySelector('input').focus()">
                        <svg class="w-4 h-4 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex flex-col flex-1 min-w-0" wire:ignore x-data="{ 
                        init() { 
                            window.flatpickr(this.$refs.input, { 
                                dateFormat: 'd/m/Y', 
                                defaultDate: '{{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }}',
                                onChange: (selectedDates, dateStr, instance) => { 
                                    if(selectedDates.length > 0) { $wire.set('fecha_inicio', instance.formatDate(selectedDates[0], 'Y-m-d')) } 
                                } 
                            }); 
                        } 
                    }">
                        <label class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-none mb-1">Fecha Inicial</label>
                        <input type="text" x-ref="input"
                            class="block w-full p-0 bg-transparent border-none text-sm font-black text-gray-900 dark:text-white focus:ring-0 cursor-pointer min-h-[20px]">
                    </div>
                </div>

                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 hidden md:block"></div>

                {{-- Fecha Final --}}
                <div class="flex-1 flex items-center gap-3 px-3 py-2 w-full">
                    <div class="shrink-0 w-8 h-8 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 cursor-pointer" onclick="this.nextElementSibling.querySelector('input').focus()">
                        <svg class="w-4 h-4 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex flex-col flex-1 min-w-0" wire:ignore x-data="{ 
                        init() { 
                            window.flatpickr(this.$refs.input, { 
                                dateFormat: 'd/m/Y', 
                                defaultDate: '{{ \Carbon\Carbon::parse($fecha_final)->format('d/m/Y') }}',
                                onChange: (selectedDates, dateStr, instance) => { 
                                    if(selectedDates.length > 0) { $wire.set('fecha_final', instance.formatDate(selectedDates[0], 'Y-m-d')) } 
                                } 
                            }); 
                        } 
                    }">
                        <label class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-none mb-1">Fecha Final</label>
                        <input type="text" x-ref="input"
                            class="block w-full p-0 bg-transparent border-none text-sm font-black text-gray-900 dark:text-white focus:ring-0 cursor-pointer min-h-[20px]">
                    </div>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="flex gap-2 w-full md:w-auto shrink-0 self-stretch md:self-auto">
                <button wire:click="generate" 
                    class="flex-1 md:flex-none bg-[#13322B] hover:bg-[#0a1f1a] text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-md shadow-[#13322B]/20 flex items-center justify-center min-w-[140px]">
                    <span wire:loading.remove wire:target="generate">Consultar</span>
                    <span wire:loading wire:target="generate" class="flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>

                <button wire:click="generateAll"
                    class="flex-1 md:flex-none bg-oro hover:bg-[#a57f2c]/90 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-md shadow-oro/20 flex items-center justify-center min-w-[140px]">
                    <span wire:loading.remove wire:target="generateAll">Todo el Historial</span>
                    <span wire:loading wire:target="generateAll">...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Resultados del Kardex --}}
    @if($results !== null)
    <div class="mb-4 flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-[#9b2247] rounded-full"></div>
            <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest">
                Historial de Incidencias (Kardex)
            </h3>
        </div>
        
        <a href="{{ route('reports.kardex.pdf', ['num_empleado' => $employee->num_empleado, 'fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final]) }}"
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
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest w-24 text-center">Código</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Concepto / Comatario</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Periodo</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Rango de Fechas</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Días</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($results as $inc)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                        <td class="px-5 py-4 text-center">
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-xs font-black rounded-lg border border-gray-200 dark:border-gray-600">
                                {{ str_pad($inc->codigo->code, 2, "0", STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-100 uppercase italic">
                                    {{ $inc->codigo->description }}
                                </span>
                                @if($inc->otorgado)
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium uppercase mt-1 leading-tight">
                                    Obs: {{ $inc->otorgado }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                @if($inc->periodo)
                                    {{ $inc->periodo->periodo }}-{{ $inc->periodo->year }}
                                @else
                                    S/P
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 font-mono">
                                    {{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}
                                </span>
                                <span class="text-[9px] text-gray-400 uppercase font-black leading-none">-</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 font-mono">
                                    {{ \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#9b2247] text-white text-xs font-black shadow-sm">
                                {{ $inc->total_dias }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    No hay incidencias registradas en este rango
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista Móvil (Tarjetas) --}}
        <div class="md:hidden flex flex-col gap-3 p-4">
            @forelse($results as $inc)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-2xl p-4 flex flex-col gap-3 relative">
                <div class="flex items-start justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <div class="flex flex-col flex-1 pr-3">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-[10px] font-black rounded uppercase border border-gray-200 dark:border-gray-600 shadow-sm">
                                CÓDIGO: {{ str_pad($inc->codigo->code, 2, "0", STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <span class="text-xs font-bold text-gray-800 dark:text-gray-100 uppercase italic leading-tight">
                            {{ $inc->codigo->description }}
                        </span>
                    </div>
                    <div class="shrink-0 flex flex-col items-end">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Días</span>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#9b2247] text-white text-xs font-black shadow-sm">
                            {{ $inc->total_dias }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl p-3 border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Inicio</span>
                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">{{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Fin</span>
                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">{{ \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}</span>
                    </div>
                </div>
                
                @if($inc->periodo || $inc->otorgado)
                <div class="pt-1 flex flex-col gap-1.5">
                    @if($inc->periodo)
                    <div class="flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-[#9b2247]"></span>
                        <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                            Periodo: <span class="text-gray-700 dark:text-gray-300">{{ $inc->periodo->periodo }}-{{ $inc->periodo->year }}</span>
                        </span>
                    </div>
                    @endif
                    @if($inc->otorgado)
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase leading-tight italic pl-2 border-l-2 border-gray-200 dark:border-gray-700 mt-1">
                        Obs: {{ $inc->otorgado }}
                    </span>
                    @endif
                </div>
                @endif
            </div>
            @empty
            <div class="py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center gap-3">
                <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">No hay incidencias</span>
            </div>
            @endforelse
        </div>
    </div>
    @endif
</div>