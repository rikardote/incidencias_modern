<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-950">
    {{-- Header del Reporte --}}
    {{-- Header del Reporte --}}
    <div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
        <div class="flex items-center gap-5">
            <div>
                <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Reporte
                    <span class="text-oro">Vacacional</span>
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
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}" wire:navigate
                class="h-[42px] px-6 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-oro transition-all flex items-center justify-center gap-2 text-xs font-black uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Lista de Periodos --}}
    <div class="space-y-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 divide-y divide-gray-50 dark:divide-gray-700">
            @forelse($resumen as $item)
            <div wire:click="showDetails('{{ $item['p_id'] }}', '{{ $item['nombre'] }}')"
                class="flex items-center gap-4 p-4 hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all cursor-pointer group">

                <div
                    class="w-14 h-14 rounded-xl bg-white dark:bg-gray-900 flex flex-col items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0 group-hover:border-[#9b2247] transition-colors">
                    <span class="text-lg font-black text-[#9b2247] leading-none">{{ $item['usados'] }}</span>
                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Días</span>
                </div>

                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Periodo
                        Evaluado</label>
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-black text-gray-900 dark:text-white uppercase truncate">
                            {{ $item['nombre'] }}
                        </span>
                        <div class="text-gray-300 dark:text-gray-700 group-hover:text-[#9b2247] transition-colors pr-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-xs font-black text-gray-400 uppercase tracking-widest">No hay registros
            </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL REPARADO (SIN DEGRADADO, FECHAS SEPARADAS) --}}
    @if($showDetailsModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-[#13322B]/60 transition-opacity" aria-hidden="true" wire:click="closeDetails">
        </div>

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
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-widest">{{ $selectedPeriod }}</h3>
                        <p class="text-[9px] font-bold text-[#e6d194] uppercase tracking-widest opacity-80">Desglose de
                            días utilizados por quincena</p>
                    </div>
                </div>
                <button wire:click="closeDetails"
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
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Quincena</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Inicio</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Fin</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Días</th>
                                    <th
                                        class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Código</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @forelse($selectedPeriodIncidencias as $inc)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-black text-gray-800 dark:text-gray-200 text-xs uppercase">{{
                                                $inc->qna->qna ?? '--' }}</span>
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Año
                                                {{ $inc->qna->year ?? '--' }}</span>
                                        </div>
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
                                        <span class="text-sm font-black text-[#9b2247] dark:text-[#e6d194] italic">{{
                                            $inc->total_dias }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="px-1.5 py-0.5 bg-[#13322B] text-white text-[9px] font-black rounded uppercase inline-block">{{
                                            $inc->codigo->code ?? '--' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5"
                                        class="py-12 text-center text-gray-400 italic text-xs uppercase font-black tracking-widest">
                                        Sin registros</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Vista Móvil (Tarjetas) --}}
                    <div class="md:hidden flex flex-col gap-3">
                        @forelse($selectedPeriodIncidencias as $inc)
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 shadow-sm flex flex-col gap-3">
                            <div
                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-3">
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-800 dark:text-gray-200 text-sm uppercase">QNA {{
                                        $inc->qna->qna ?? '--' }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Año {{
                                        $inc->qna->year ?? '--' }}</span>
                                </div>
                                <span
                                    class="px-2 py-1 bg-[#13322B] text-[#e6d194] text-[10px] font-black rounded-lg uppercase inline-block shadow-sm">
                                    Código: {{ $inc->codigo->code ?? '--' }}
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
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Días
                                    Descontados:</span>
                                <span class="text-sm font-black text-[#9b2247] dark:text-[#e6d194] italic">{{
                                    $inc->total_dias }} Días</span>
                            </div>
                        </div>
                        @empty
                        <div
                            class="py-12 text-center text-gray-400 italic text-xs uppercase font-black tracking-widest bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                            Sin registros</div>
                        @endforelse
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-5 bg-white dark:bg-gray-800 border-t dark:border-gray-700 shrink-0 flex justify-end">
                <button wire:click="closeDetails"
                    class="px-10 py-3 bg-[#13322B] text-[#e6d194] text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-[#13322B]/20 hover:scale-105 transition-all">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    @endif
</div>