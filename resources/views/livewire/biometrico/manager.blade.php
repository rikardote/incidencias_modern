<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Gestión de <span class="text-oro">Biométricos</span></h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Seleccione los equipos para sincronizar registros de asistencia.</p>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="openCreateModal"
                class="px-4 py-2 bg-[#9b2247] hover:bg-[#7a1b38] text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Agregar Equipo
            </button>
            <button wire:click="fetchDeviceTimes" wire:loading.attr="disabled" wire:target="fetchDeviceTimes"
                class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-black uppercase tracking-widest border border-gray-100 dark:border-gray-700 hover:border-emerald-400 hover:text-emerald-600 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="fetchDeviceTimes" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span wire:loading.remove wire:target="fetchDeviceTimes">Hora Equipos</span>
                <span wire:loading wire:target="fetchDeviceTimes">Consultando...</span>
            </button>
            <button wire:click="toggleAll" 
                class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-black uppercase tracking-widest border border-gray-100 dark:border-gray-700 hover:border-oro transition-all shadow-sm">
                {{ count($selectedIds) === count($dispositivos) ? 'Desmarcar Todos' : 'Marcar Todos' }}
            </button>
            
            <button wire:click="sync" wire:loading.attr="disabled"
                class="h-[42px] px-8 bg-[#13322B] hover:bg-[#0a1f1a] text-[#e6d194] rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-[#13322B]/20 transition-all flex items-center gap-2">
                <span wire:loading.remove wire:target="sync">Descargar Registros</span>
                <span wire:loading wire:target="sync" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Listado de Equipos --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($dispositivos as $disp)
                    <div wire:click="$toggle('selectedIds.{{ $loop->index }}')" 
                        class="relative bg-white dark:bg-gray-800 p-5 rounded-2xl border transition-all cursor-pointer group {{ in_array($disp['id'], $selectedIds) ? 'border-oro ring-1 ring-oro shadow-md' : 'border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-oro/30' }}">
                        
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 {{ in_array($disp['id'], $selectedIds) ? 'bg-[#13322B] text-[#e6d194]' : 'bg-gray-50 dark:bg-gray-900 text-gray-400 group-hover:text-oro' }} rounded-xl flex items-center justify-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-wider">{{ $disp['location'] }}</h3>
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 tracking-widest mt-0.5">{{ $disp['ip'] }}</p>
                                    @if(isset($deviceTimes[$disp['id']]))
                                        <div class="flex items-center gap-1.5 mt-2">
                                            <span class="w-2 h-2 rounded-full {{ $deviceTimes[$disp['id']]['status'] === 'online' ? 'bg-emerald-500' : 'bg-rose-400' }} inline-block"></span>
                                            <svg class="w-4 h-4 {{ $deviceTimes[$disp['id']]['status'] === 'online' ? 'text-emerald-600' : 'text-rose-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-xs font-black tracking-wider {{ $deviceTimes[$disp['id']]['status'] === 'online' ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-500' }}">
                                                {{ $deviceTimes[$disp['id']]['time'] }}
                                            </span>
                                            
                                            @if($deviceTimes[$disp['id']]['status'] === 'online')
                                                <button wire:click.stop="syncDeviceTime({{ $disp['id'] }})" 
                                                    class="ml-2 text-gray-500 hover:text-[#9b2247] hover:bg-[#9b2247]/10 p-1.5 rounded-lg transition-all opacity-0 group-hover:opacity-100 flex items-center justify-center relative group/btn"
                                                    title="Sincronizar hora del equipo con servidor central">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    <span class="absolute -top-7 left-1/2 -translate-x-1/2 min-w-max bg-gray-900 text-white text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded shadow-lg opacity-0 group-hover/btn:opacity-100 transition-opacity pointer-events-none">
                                                        Sync Hora
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mt-1 flex items-center gap-3">
                                <button wire:click.stop="editEquipo({{ $disp['id'] }})" 
                                    class="p-1.5 text-gray-400 hover:text-oro hover:bg-oro/5 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click.stop="deleteEquipo({{ $disp['id'] }})" 
                                    wire:confirm="¿Estás seguro de eliminar este equipo?"
                                    class="p-1.5 text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <input type="checkbox" value="{{ $disp['id'] }}" wire:model.live="selectedIds" 
                                    @click.stop
                                    class="w-5 h-5 text-[#13322B] border-gray-200 dark:border-gray-600 rounded-md focus:ring-oro transition-all cursor-pointer">
                            </div>
                        </div>

                        {{-- Resultado Local --}}
                        @if(isset($results[$disp['id']]))
                            <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase tracking-widest {{ $results[$disp['id']]['status'] === 'success' ? 'text-emerald-600' : ($results[$disp['id']]['status'] === 'warning' ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $results[$disp['id']]['status'] === 'success' ? 'Éxito' : ($results[$disp['id']]['status'] === 'warning' ? 'Aviso' : 'Error') }}
                                </span>
                                <span class="text-[9px] font-bold text-gray-400 truncate ml-4">{{ $results[$disp['id']]['message'] }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Panel de Estado --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h4 class="text-xs font-black text-[#9b2247] dark:text-[#e6d194] uppercase tracking-[0.2em] mb-6 border-b border-gray-50 dark:border-gray-700 pb-3">Estado del Proceso</h4>
                
                @if($isSyncing)
                    <div class="space-y-6">
                        <div class="flex flex-col items-center">
                            <div class="relative w-24 h-24 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-100 dark:text-gray-700" />
                                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $progress / 100) }}" class="text-[#13322B] transition-all duration-500" />
                                </svg>
                                <span class="absolute text-lg font-black text-[#13322B] dark:text-gray-100">{{ round($progress) }}%</span>
                            </div>
                            <p class="mt-4 text-[10px] font-black text-gray-500 uppercase tracking-widest animate-pulse">Sincronizando equipo:</p>
                            <p class="mt-1 text-xs font-black text-[#13322B] dark:text-oro uppercase">{{ $currentDevice }}</p>
                        </div>
                    </div>
                @elseif(!empty($results))
                    <div class="text-center">
                        <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h5 class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-wider">Sincronización Terminada</h5>
                        <p class="mt-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Se procesaron {{ count($results) }} equipos seleccionados.</p>
                        
                        <button wire:click="$set('results', [])" class="mt-6 text-[10px] font-black text-[#9b2247] hover:underline uppercase tracking-widest">Limpiar resultados</button>
                    </div>
                @else
                    <div class="text-center py-10 opacity-40">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-[10px] font-black uppercase tracking-widest px-4 leading-loose">Seleccione equipos y presione el botón para iniciar la descarga</p>
                    </div>
                @endif
            </div>

            {{-- Info Box --}}
            <div class="bg-[#13322B] rounded-3xl p-6 text-white relative overflow-hidden group shadow-lg shadow-[#13322B]/10">
                <div class="absolute right-0 top-0 -mr-4 -mt-4 opacity-10 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                </div>
                <h5 class="text-xs font-black text-[#e6d194] uppercase tracking-widest mb-3">Información del Sistema</h5>
                <p class="text-[10px] font-bold text-gray-300 leading-relaxed uppercase">
                    Este proceso descarga registros directamente de la red. Si un equipo no responde, verifique su conexión a la Intranet de la Delegación.
                </p>
            </div>
        </div>
    </div>

    {{-- Modal para agregar/editar equipo --}}
    <div x-show="$wire.isCreateModalOpen" style="display: none;" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <div x-show="$wire.isCreateModalOpen" x-transition.opacity class="fixed inset-0 bg-[#13322B]/60 transition-opacity"
            aria-hidden="true" @click="$wire.isCreateModalOpen = false"></div>

        <div x-show="$wire.isCreateModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl flex flex-col z-10 border border-white/5">
            
            <div class="px-6 py-5 bg-[#9b2247] text-white flex justify-between items-center relative shrink-0 rounded-t-3xl">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/10 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">{{ $editingEquipoId ? 'Editar Equipo' : 'Nuevo Equipo' }}</h3>
                        <p class="text-[9px] font-bold text-white/70 uppercase tracking-widest">{{ $editingEquipoId ? 'Modificar datos del biométrico' : 'Registrar biométrico' }}</p>
                    </div>
                </div>
                <button @click="$wire.isCreateModalOpen = false" class="p-2 hover:bg-white/10 rounded-lg transition-colors text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Ubicación / Nombre</label>
                    <input type="text" wire:model="newLocation" placeholder="EJ: DELEGACIÓN PRINCIPAL"
                        class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-[#9b2247]/30 focus:border-[#9b2247] transition-all outline-none uppercase">
                    @error('newLocation') <span class="text-[9px] font-bold text-rose-500 mt-1 block px-1 uppercase tracking-widest">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Dirección IP</label>
                    <input type="text" wire:model="newIp" placeholder="EJ: 192.168.1.100"
                        class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-[#9b2247]/30 focus:border-[#9b2247] transition-all outline-none">
                    @error('newIp') <span class="text-[9px] font-bold text-rose-500 mt-1 block px-1 uppercase tracking-widest">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button @click="$wire.isCreateModalOpen = false" type="button"
                        class="px-6 py-3 text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="saveEquipo" wire:loading.attr="disabled"
                        class="px-10 py-3 bg-[#9b2247] text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-[#9b2247]/20 hover:scale-105 transition-all">
                        <span wire:loading.remove wire:target="saveEquipo">{{ $editingEquipoId ? 'Actualizar Equipo' : 'Guardar Equipo' }}</span>
                        <span wire:loading wire:target="saveEquipo">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
