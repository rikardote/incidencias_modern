<div class="py-12 px-4 md:px-8 max-w-7xl mx-auto min-h-screen">
    {{-- Header con Info del Empleado --}}
    @if($employee)
    {{-- Header del Reporte --}}
    <div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
        <div class="flex items-center gap-5">
            <div>
                <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Kárdex
                    <span class="text-oro">Individual</span>
                </h1>
                <div class="flex items-center gap-3 mt-1 flex-wrap">
                    <span
                        class="px-2.5 py-1 bg-[#9b2247]/10 text-[#9B2247] dark:text-oro text-[10px] font-black rounded-lg uppercase tracking-wider border border-[#9b2247]/20 shadow-sm">
                        #{{ $employee->num_empleado }}
                    </span>
                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">
                        {{ $employee->fullname }}
                    </span>
                </div>
                {{-- Status indicators --}}
                <div class="flex items-center gap-2 mt-2 flex-wrap">
                    @if($employee->lactancia)
                    <span class="px-2 py-0.5 bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] font-black rounded-md border border-green-500/20 uppercase">
                        C92 LACTANCIA ({{ \Carbon\Carbon::parse($employee->lactancia_inicio)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($employee->lactancia_fin)->format('d/m/y') }})
                    </span>
                    @endif
                    @if($employee->estancia)
                    <span class="px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-black rounded-md border border-blue-500/20 uppercase">
                        C93 ESTANCIA ({{ \Carbon\Carbon::parse($employee->estancia_inicio)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($employee->estancia_fin)->format('d/m/y') }})
                    </span>
                    @endif
                    @if($employee->exento)
                    <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black rounded-md border border-amber-500/20 uppercase">
                        C94 PERSONAL EXENTO
                    </span>
                    @endif
                    @if($employee->comisionado)
                    <span class="px-2 py-0.5 bg-purple-500/10 text-purple-600 dark:text-purple-400 text-[10px] font-black rounded-md border border-purple-500/20 uppercase">
                        COMISIONADO
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- Buscador si no hay empleado --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Kárdex <span
                    class="text-oro">Institucional</span></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-bold uppercase tracking-tight">Ingrese el
                número de empleado para consultar</p>
        </div>

        <div
            class="flex items-center gap-2 bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <input type="text" wire:model="num_empleado" placeholder="ID Empleado"
                class="h-[42px] w-[150px] px-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-[#13322B] outline-none transition-all">
            <button wire:click="cambiarEmpleadoByNum"
                class="h-[42px] px-6 bg-[#13322B] text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#0a1b17] transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Buscar
            </button>
        </div>
    </div>
    @endif

    <div
        class="bg-white dark:bg-gray-800 p-2.5 rounded-[20px] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col lg:flex-row items-stretch lg:items-center gap-4">
        {{-- Filtros de Fecha --}}
        <div class="flex items-center gap-2 bg-gray-50/50 dark:bg-gray-900/40 p-1.5 rounded-xl border border-gray-100/50 dark:border-gray-700/50">
            <div wire:ignore class="relative" x-data="{ 
                    flatpickrInstance: null,
                    init() { 
                        this.flatpickrInstance = window.flatpickr(this.$refs.input, { 
                            dateFormat: 'd/m/Y', 
                            defaultDate: '{{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }}', 
                            onChange: (selectedDates, dateStr, instance) => { 
                                if(selectedDates.length > 0) { $wire.set('fecha_inicio', instance.formatDate(selectedDates[0], 'Y-m-d')) }
                            } 
                        }); 
                        this.$watch('$wire.fecha_inicio', (val) => {
                            this.flatpickrInstance.setDate(val, false, 'Y-m-d');
                        });
                    } 
                }">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <input type="text" x-ref="input"
                    class="h-[38px] w-[125px] pl-8 pr-2 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg text-[11px] font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-[#13322B]/20 outline-none cursor-pointer text-center transition-all shadow-sm"
                    placeholder="Inicio">
            </div>
            
            <div class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-200/50 dark:bg-gray-700/50">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </div>

            <div wire:ignore class="relative" x-data="{ 
                    flatpickrInstance: null,
                    init() { 
                        this.flatpickrInstance = window.flatpickr(this.$refs.input, { 
                            dateFormat: 'd/m/Y', 
                            defaultDate: '{{ \Carbon\Carbon::parse($fecha_final)->format('d/m/Y') }}', 
                            onChange: (selectedDates, dateStr, instance) => { 
                                if(selectedDates.length > 0) { $wire.set('fecha_final', instance.formatDate(selectedDates[0], 'Y-m-d')) }
                            } 
                        }); 
                        this.$watch('$wire.fecha_final', (val) => {
                            this.flatpickrInstance.setDate(val, false, 'Y-m-d');
                        });
                    } 
                }">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <input type="text" x-ref="input"
                    class="h-[38px] w-[125px] pl-8 pr-2 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg text-[11px] font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-[#13322B]/20 outline-none cursor-pointer text-center transition-all shadow-sm"
                    placeholder="Fin">
            </div>
        </div>

        <div class="flex items-center gap-2 flex-1">
            <button wire:click="generate"
                class="h-[40px] px-6 bg-[#13322B] hover:bg-[#0a1b17] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-[#13322B]/10 transition-all flex items-center justify-center gap-2 min-w-[130px] group active:scale-95">
                <span wire:loading.remove wire:target="generate" class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Consultar
                </span>
                <span wire:loading wire:target="generate">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
            </button>
            <button wire:click="generateAll"
                class="h-[40px] px-5 bg-oro hover:bg-[#a57f2c] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-oro/10 transition-all flex items-center gap-2 active:scale-95 group">
                <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9l-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Todo
            </button>
            
            <div class="h-6 w-px bg-gray-100 dark:bg-gray-700 mx-1 hidden lg:block"></div>

            @if($employee)
            <a href="{{ route('reports.kardex.pdf', ['num_empleado' => $employee->num_empleado, 'fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final]) }}"
                target="_blank"
                class="h-[40px] px-5 bg-[#9b2247] hover:bg-[#7a1b38] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-[#9b2247]/10 transition-all flex items-center gap-2 ml-auto active:scale-95 group">
                <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar PDF
            </a>
            @endif
        </div>
    </div>

{{-- Resultados del Kardex --}}
@if($results !== null)
<div class="mb-4 flex items-center gap-3 px-2">
    <div class="w-1.5 h-6 bg-[#9b2247] rounded-full"></div>
    <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest">
        Historial de Incidencias (Kardex)
    </h3>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden"
    wire:loading.class="opacity-60">
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                    <th
                        class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest w-24 text-center">
                        Código</th>
                    <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Concepto /
                        Comatario</th>
                    <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                        Periodo</th>
                    <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                        Rango de Fechas</th>
                    <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                        Días</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($results as $inc)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                    <td class="px-5 py-4 text-center">
                        <span
                            class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-xs font-black rounded-lg border border-gray-200 dark:border-gray-600">
                            {{ $inc->codigo->code ?? '??' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-100 uppercase italic">
                                {{ $inc->codigo->description ?? 'SIN DESCRIPCIÓN' }}
                            </span>
                            @if($inc->otorgado)
                            <span
                                class="text-[10px] text-gray-400 dark:text-gray-500 font-medium uppercase mt-1 leading-tight">
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
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#9b2247] text-white text-xs font-black shadow-sm">
                            {{ $inc->total_dias }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
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
        <div
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-2xl p-4 flex flex-col gap-3 relative">
            <div class="flex items-start justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                <div class="flex flex-col flex-1 pr-3">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span
                            class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-[#13322B] dark:text-[#e6d194] text-[10px] font-black rounded uppercase border border-gray-200 dark:border-gray-600 shadow-sm">
                            CÓDIGO: {{ $inc->codigo->code ?? '??' }}
                        </span>
                    </div>
                    <span class="text-xs font-bold text-gray-800 dark:text-gray-100 uppercase italic leading-tight">
                        {{ $inc->codigo->description ?? 'SIN DESCRIPCIÓN' }}
                    </span>
                </div>
                <div class="shrink-0 flex flex-col items-end">
                    <span
                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Días</span>
                    <span
                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#9b2247] text-white text-xs font-black shadow-sm">
                        {{ $inc->total_dias }}
                    </span>
                </div>
            </div>

            <div
                class="grid grid-cols-2 gap-2 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl p-3 border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Inicio</span>
                    <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">{{
                        \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Fin</span>
                    <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 font-mono tracking-tighter">{{
                        \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}</span>
                </div>
            </div>

            @if($inc->periodo || $inc->otorgado)
            <div class="pt-1 flex flex-col gap-1.5">
                @if($inc->periodo)
                <div class="flex items-center gap-1.5">
                    <span class="w-1 h-1 rounded-full bg-[#9b2247]"></span>
                    <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        Periodo: <span class="text-gray-700 dark:text-gray-300">{{ $inc->periodo->periodo }}-{{
                            $inc->periodo->year }}</span>
                    </span>
                </div>
                @endif
                @if($inc->otorgado)
                <span
                    class="text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase leading-tight italic pl-2 border-l-2 border-gray-200 dark:border-gray-700 mt-1">
                    Obs: {{ $inc->otorgado }}
                </span>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div
            class="py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center gap-3">
            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">No hay
                incidencias</span>
        </div>
        @endforelse
    </div>
</div>
@endif
</div>