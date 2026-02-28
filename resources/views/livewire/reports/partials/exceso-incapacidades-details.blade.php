<div class="max-w-4xl">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-1 h-5 bg-guinda rounded-full"></div>
        <h3 class="text-xs md:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
            Desglose de Incapacidades</h3>
    </div>

    <div class="grid grid-cols-1 gap-3">
        @foreach($info['incapacidades'] as $inc)
        <div
            class="bg-gray-50/80 dark:bg-gray-800/80 p-3 md:p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4 group/item hover:border-guinda/30 transition-colors">
            <div class="flex flex-wrap items-center gap-4 md:gap-6">
                <div class="flex flex-col">
                    <span class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Inicio</span>
                    <span class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Fin</span>
                    <span class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="hidden md:block h-8 w-[1px] bg-gray-200 dark:bg-gray-600"></div>
                <div class="flex flex-col min-w-0 flex-1 md:max-w-xs mt-1 md:mt-0">
                    <span class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Diagnóstico</span>
                    <span
                        class="text-[10px] md:text-xs text-gray-600 dark:text-gray-400 italic break-words line-clamp-2 md:line-clamp-1"
                        title="{{ $inc->diagnostico }}">
                        {{ $inc->diagnostico ?: 'Sin diagnóstico registrado' }}
                    </span>
                </div>
            </div>

            <div
                class="flex items-center justify-between md:justify-end gap-3 border-t md:border-t-0 border-gray-100 dark:border-gray-700 pt-3 md:pt-0 shrink-0">
                <div class="md:hidden text-[9px] font-bold text-gray-400 uppercase">Total</div>
                <div class="text-right">
                    <div class="text-base md:text-lg font-black text-guinda leading-none">{{ $inc->total_dias }}</div>
                    <div class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase mt-0.5">Días</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 flex justify-end">
        <a href="{{ route('employees.incidencias', $info['empleado']->id) }}"
            class="w-full md:w-auto text-center inline-flex items-center justify-center gap-2 px-4 py-2.5 text-[10px] md:text-xs font-bold text-guinda hover:text-white hover:bg-guinda rounded-lg border border-guinda/20 transition-all">
            Gestionar Incidencias
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                </path>
            </svg>
        </a>
    </div>
</div>