<div class="h-full">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 h-full flex flex-col justify-between transition-all hover:shadow-xl hover:border-oro/30 group">
        <div>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#13322B] rounded-xl flex items-center justify-center text-[#e6d194] shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-wider">Equipos Biométricos</h5>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.15em] mt-0.5">Sincronización de Relojes</p>
                    </div>
                </div>

                @if($isSyncing)
                    <div class="flex items-center gap-2">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest animate-pulse">Sincronizando...</span>
                    </div>
                @endif
            </div>

            @if($isSyncing)
                <div class="space-y-3 mt-4">
                    <div class="flex justify-between items-center text-[9px] font-black text-gray-500 uppercase tracking-widest">
                        <span class="truncate pr-4">{{ $message }}</span>
                        <span>{{ round($progress) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-[#13322B] h-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @elseif(!empty($results))
                <div class="mt-4 space-y-2 max-h-32 overflow-y-auto pr-1 custom-scrollbar">
                    @foreach($results as $res)
                        <div class="flex items-center justify-between p-2 rounded-lg {{ $res['status'] === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/20' : ($res['status'] === 'warning' ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-rose-50 dark:bg-rose-900/20') }} border {{ $res['status'] === 'success' ? 'border-emerald-100 dark:border-emerald-800/30' : ($res['status'] === 'warning' ? 'border-amber-100 dark:border-amber-800/30' : 'border-rose-100 dark:border-rose-800/30') }}">
                            <div class="flex flex-col">
                                <span class="text-[8px] font-black uppercase tracking-widest {{ $res['status'] === 'success' ? 'text-emerald-700 dark:text-emerald-400' : ($res['status'] === 'warning' ? 'text-amber-700 dark:text-amber-400' : 'text-rose-700 dark:text-rose-400') }}">
                                    {{ $res['location'] }}
                                </span>
                                <span class="text-[8px] font-bold text-gray-500 dark:text-gray-400 truncate">
                                    {{ $res['message'] }}
                                </span>
                            </div>
                            @if($res['status'] === 'success')
                                <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <svg class="w-3 h-3 {{ $res['status'] === 'warning' ? 'text-amber-500' : 'text-rose-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6 flex flex-col items-center justify-center py-4 bg-gray-50/50 dark:bg-gray-900/30 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest text-center px-4">Listo para descargar registros nuevos de la red</p>
                </div>
            @endif
        </div>

        <div class="mt-6">
            <button wire:click="sync" wire:loading.attr="disabled" 
                class="w-full py-3 bg-[#13322B] hover:bg-[#0a1f1a] disabled:opacity-50 text-[#e6d194] rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-[#13322B]/20 transition-all flex items-center justify-center gap-2 group/btn">
                <span wire:loading.remove wire:target="sync">Descargar Registros</span>
                <span wire:loading wire:target="sync" class="flex items-center gap-2">
                    <svg class="animate-spin h-3 w-3 text-[#e6d194]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
                <svg wire:loading.remove wire:target="sync" class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </button>
        </div>
    </div>
</div>
