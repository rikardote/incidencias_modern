<div class="p-6 lg:p-8 bg-gray-50 dark:bg-gray-950 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <span class="p-2 bg-[#13322B] text-[#e6d194] rounded-xl shadow-lg shadow-[#13322B]/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.375 9L12 11.625 14.625 9M12 11.625V21m-1.652-2.348A9 9 0 105.261 7.087C13.9 12.063 12.428 12.063 21 16.038" />
                        </svg>
                    </span>
                    Monitoreo en Tiempo Real
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Estado de dispositivos y flujo de registros en vivo</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button wire:click="refreshStatus" wire:loading.attr="disabled"
                    class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-700 dark:text-gray-200 text-sm font-bold hover:bg-gray-50 dark:hover:bg-gray-750 transition-all flex items-center gap-2 shadow-sm">
                    <svg wire:loading.class="animate-spin" wire:target="refreshStatus" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Verificar Estado
                </button>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left: Device Status -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-wider text-xs">Dispositivos en Red</h3>
                    
                    <div class="space-y-4">
                        @foreach($devices as $device)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/50 group hover:border-oro/30 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-oro transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3V7.5a3 3 0 013-3h13.5a3 3 0 013 3v3.75a3 3 0 01-3 3m-13.5 0h13.5m-13.5 0v3.75m13.5-3.75v3.75m-13.5 0a3 3 0 003 3h13.5a3 3 0 003-3v-3.75" />
                                            </svg>
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white dark:border-gray-900 {{ $device['status'] === 'success' ? 'bg-emerald-500' : ($device['status'] === 'error' ? 'bg-rose-500' : 'bg-amber-500 animate-pulse') }}"></div>
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900 dark:text-gray-100 text-sm uppercase tracking-tight">{{ $device['location'] }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $device['ip'] }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $device['status'] === 'success' ? 'text-emerald-500' : ($device['status'] === 'error' ? 'text-rose-500' : 'text-amber-500') }}">
                                        {{ $device['status'] === 'success' ? 'En línea' : ($device['status'] === 'error' ? 'Desconectado' : 'Checando...') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Stats/Instructions -->
                <div class="bg-[#13322B] rounded-2xl p-6 text-[#e6d194] overflow-hidden relative group">
                    <div class="relative z-10">
                        <h4 class="font-black uppercase tracking-[0.2em] text-[10px] mb-2 opacity-80">Información del Sistema</h4>
                        <p class="text-sm font-medium leading-relaxed">
                            El sistema utiliza <span class="text-white font-bold">Laravel Reverb</span> para recibir registros en tiempo real sin necesidad de recargar la página.
                        </p>
                    </div>
                    <svg class="absolute -bottom-8 -right-8 w-32 h-32 opacity-10 group-hover:scale-110 transition-transform duration-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                </div>
            </div>

            <!-- Right: Real-time Feed -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none min-h-[600px] flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Últimos Registros</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">En Vivo</span>
                            </div>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">Mostrando últimos 20</span>
                        </div>
                    </div>

                    <div class="flex-1 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Empleado</th>
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha y Hora</th>
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ubicación / Equipo</th>
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Identificador</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                                    @forelse($recentChecadas as $idx => $checada)
                                        <tr class="group hover:bg-gray-50/80 dark:hover:bg-gray-800/30 transition-all animate-fadeIn" style="animation-delay: {{ $idx * 50 }}ms">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-black text-gray-400 text-[10px]">
                                                        {{ substr($checada['nombre'], 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100 leading-none mb-1">{{ $checada['nombre'] }}</p>
                                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">ID: {{ $checada['num_empleado'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ date('d/m/Y', strtotime($checada['fecha'])) }}</span>
                                                    <span class="text-[10px] font-black text-oro tracking-widest uppercase">{{ $checada['hora'] }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-oro/10 border border-oro/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-oro"></span>
                                                    <span class="text-[10px] font-black text-oro uppercase tracking-[0.1em]">{{ $checada['location'] ?? 'Manual / Sync' }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500 font-medium">#{{ substr($checada['chip'], -8) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-20 text-center">
                                                <div class="flex flex-col items-center gap-4">
                                                    <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-full text-gray-300 dark:text-gray-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Esperando registros...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            console.log('--- DEPURACIÓN BIOMÉTRICO ---');
            
            if (typeof Echo !== 'undefined') {
                const config = Echo.connector.options;
                console.log('Configuración de Echo:', {
                    host: config.wsHost,
                    port: config.wsPort,
                    key: config.key,
                    scheme: config.forceTLS ? 'https' : 'http'
                });
                
                console.log('Suscribiendo a biometrico-monitor...');
                
                Echo.channel('biometrico-monitor')
                    .listen('.ChecadaCreated', (e) => {
                        console.log('🔔 REGISTRO RECIBIDO (Sencillo):', e);
                    })
                    .listen('ChecadaCreated', (e) => {
                        console.log('🔔 REGISTRO RECIBIDO (Con Namespace):', e);
                    });
            } else {
                console.error('ERROR: Echo no está definido. Verifica resources/js/echo.js');
            }
        });
    </script>
</div>
