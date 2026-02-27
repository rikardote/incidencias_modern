<div class="space-y-4">
    {{-- Toggle de Mantenimiento --}}
    <div class="flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $isMaintenanceMode ? 'border-red-500 bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-700' }} transition-all group">
        <div>
            <h5 class="text-lg font-bold {{ $isMaintenanceMode ? 'text-red-700 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }} transition-colors">Modo Mantenimiento</h5>
            <p class="text-sm {{ $isMaintenanceMode ? 'text-red-600 dark:text-red-500' : 'text-gray-500 dark:text-gray-400' }} mt-1">
                {{ $isMaintenanceMode ? 'Bloqueando la captura de incidencias.' : 'Permitiendo captura normal.' }}
            </p>
        </div>
        
        <div class="flex items-center">
            <button wire:click="toggle" class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $isMaintenanceMode ? 'bg-red-600 focus:ring-red-600' : 'bg-gray-200 dark:bg-gray-600 focus:ring-gray-500' }}" role="switch" aria-checked="{{ $isMaintenanceMode ? 'true' : 'false' }}">
                <span class="sr-only">Toggle maintenance mode</span>
                <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $isMaintenanceMode ? 'translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </div>
    </div>

    {{-- Selector de Widget Style --}}
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h5 class="text-sm font-black text-gray-400 dark:text-gray-500 mb-4 nothing-font uppercase tracking-[0.3em]">Style Widget</h5>
        
        <div class="flex flex-wrap gap-3">
            @foreach(['classic' => 'Gesto ASCII + Texto. El balance perfecto.', 
                      'progress' => 'Barra de proceso técnica. Ideal para capturas.', 
                      'minimal' => 'Solo el rostro ASCII. Rápido y limpio.', 
                      'glass' => 'Icono + Blur. Estética refinada y moderna.',
                      'cyberpunk' => 'Neón + Glitch. Estética futurista y agresiva.'] as $style => $desc)
                <button wire:click="$set('islandStyle', '{{ $style }}')" 
                        title="{{ $desc }}"
                        class="px-6 py-2 rounded-full border-2 nothing-font text-[10px] font-black uppercase tracking-widest transition-all
                        {{ $islandStyle === $style 
                            ? ($style === 'cyberpunk' ? 'border-[#ff00ff] bg-[#ff00ff]/10 text-[#ff00ff] shadow-[0_0_15px_rgba(255,0,255,0.4)]' : 'border-oro bg-oro/10 text-oro shadow-[0_0_15px_rgba(212,175,55,0.2)]') 
                            : 'border-gray-100 dark:border-gray-700 text-gray-400 dark:text-gray-600 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    {{ $style }}
                </button>
            @endforeach
        </div>
    </div>
</div>
