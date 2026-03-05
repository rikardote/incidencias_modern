<div wire:poll.15s="refreshStats" noprogress wire:ignore.self class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Widget Empleados Activos --}}
    <div class="relative group overflow-hidden bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between transition-all hover:shadow-2xl hover:-translate-y-1">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-16 h-16 text-[#13322B] dark:text-[#e6d194]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
            </svg>
        </div>
        <div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 block">Fuerza Laboral</span>
            <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ number_format($activeEmployeesCount) }}</h3>
            <p class="text-xs font-bold text-[#13322B] dark:text-[#e6d194] mt-1 flex items-center gap-2">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                Empleados Activos
            </p>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50">
            <div class="w-full bg-gray-100 dark:bg-gray-700 h-1 rounded-full overflow-hidden">
                <div class="bg-[#13322B] dark:bg-[#e6d194] h-full" style="width: 100%"></div>
            </div>
        </div>
    </div>

    {{-- Widget Incidencias del Día --}}
    <div class="relative group overflow-hidden bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between transition-all hover:shadow-2xl hover:-translate-y-1">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-16 h-16 text-[#9b2247]" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 block">Captura Diaria</span>
            <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ number_format($todayIncidenciasCount) }}</h3>
            <p class="text-xs font-bold text-[#9b2247] mt-1">Incidencias hoy</p>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between text-[10px] font-black text-gray-400 uppercase tracking-widest">
            <span>{{ now()->format('d M, Y') }}</span>
            <span class="text-gray-300">Live</span>
        </div>
    </div>

    {{-- Widget Estatus Técnico --}}
    <div class="relative group overflow-hidden bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between transition-all hover:shadow-2xl hover:-translate-y-1">
        <div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block">Estatus del Sistema</span>
            
            <div class="space-y-3">
                {{-- DB Main --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $systemStatus['db_main'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Base de Datos</span>
                    </div>
                    <span class="text-[9px] font-black {{ $systemStatus['db_main'] ? 'text-green-600' : 'text-red-600' }} uppercase">{{ $systemStatus['db_main'] ? 'Online' : 'Error' }}</span>
                </div>

                {{-- REVERB --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $systemStatus['reverb'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Broadcasting (Reverb)</span>
                    </div>
                    <span class="text-[9px] font-black {{ $systemStatus['reverb'] ? 'text-green-600' : 'text-red-600' }} uppercase">{{ $systemStatus['reverb'] ? 'UP' : 'Down' }}</span>
                </div>

                {{-- Mantenimiento --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $systemStatus['maintenance'] ? 'bg-amber-500' : 'bg-green-500' }}"></div>
                        <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Modo Captura</span>
                    </div>
                    <span class="text-[9px] font-black {{ $systemStatus['maintenance'] ? 'text-amber-600' : 'text-green-600' }} uppercase">{{ $systemStatus['maintenance'] ? 'Mantenimiento' : 'Habilitado' }}</span>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between">
            <div class="flex items-center gap-1.5">
                @if($systemStatus['db_main'] && $systemStatus['reverb'])
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[8px] font-black bg-green-100 text-green-800 uppercase tracking-tighter">Sistema Operativo</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-800 uppercase tracking-tighter">Atención Requerida</span>
                @endif
            </div>
            <span class="text-[8px] font-mono text-gray-400">{{ php_uname('n') }}</span>
        </div>
    </div>
</div>
