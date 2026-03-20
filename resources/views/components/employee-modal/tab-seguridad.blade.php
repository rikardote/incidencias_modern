<div x-show="tab === 'seguridad'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    
    <div class="space-y-6">
        <div class="bg-gray-50 dark:bg-gray-800/20 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
            <h4 class="text-[10px] font-black text-[#13322B] dark:text-[#e6d194] uppercase tracking-widest mb-6">Condiciones de Control</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" wire:model="exento" class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border border-gray-300 dark:border-gray-600 transition-all checked:bg-[#13322B] checked:border-[#13322B]">
                        <svg class="absolute h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">Exento de Biométrico</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" wire:model.live="comisionado" class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border border-gray-300 dark:border-gray-600 transition-all checked:bg-orange-500 checked:border-orange-500">
                        <svg class="absolute h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">Comisionado Externo</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" wire:model="active" class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border border-gray-300 dark:border-gray-600 transition-all checked:bg-emerald-500 checked:border-emerald-500">
                        <svg class="absolute h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Empleado Activo</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" wire:model.live="lactancia" class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border border-gray-300 dark:border-gray-600 transition-all checked:bg-pink-500 checked:border-pink-500">
                            <svg class="absolute h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">Periodo de Lactancia</span>
                    </label>
                    @if($this->lactancia)
                    <div class="grid grid-cols-2 gap-3 pl-8 animate-fadeIn">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase">Inicio</label>
                            <input type="date" wire:model="lactancia_inicio" class="w-full px-3 py-2 text-xs bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase">Fin</label>
                            <input type="date" wire:model="lactancia_fin" class="w-full px-3 py-2 text-xs bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl outline-none">
                        </div>
                    </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" wire:model.live="estancia" class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border border-gray-300 dark:border-gray-600 transition-all checked:bg-blue-500 checked:border-blue-500">
                            <svg class="absolute h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">Estancia Infantil</span>
                    </label>
                    @if($this->estancia)
                    <div class="grid grid-cols-2 gap-3 pl-8 animate-fadeIn">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase">Inicio</label>
                            <input type="date" wire:model="estancia_inicio" class="w-full px-3 py-2 text-xs bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase">Fin</label>
                            <input type="date" wire:model="estancia_fin" class="w-full px-3 py-2 text-xs bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl outline-none">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-blue-50/50 dark:bg-blue-900/10 p-5 rounded-2xl border border-blue-100 dark:border-blue-800/20 flex items-start gap-4">
            <div class="w-10 h-10 shrink-0 bg-blue-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17" /></svg>
            </div>
            <div>
                <h4 class="text-[10px] font-black text-blue-800 dark:text-blue-300 uppercase tracking-widest mb-1">Control Biométrico</h4>
                <p class="text-[11px] text-blue-600/80 dark:text-blue-400/60 leading-snug italic">Si activa "Exento", los reportes ignorarán las faltas de este empleado en el biométrico.</p>
            </div>
        </div>
    </div>
</div>