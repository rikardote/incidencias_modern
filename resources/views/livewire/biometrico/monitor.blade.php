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
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Flujo de registros en vivo</p>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 gap-8">

            <!-- Real-time Feed -->
            <div>
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
                            <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">Mostrando últimos 50</span>
                        </div>
                    </div>

                    <div class="flex-1 overflow-hidden">
                        <!-- Desktop View: Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Empleado</th>
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Hora</th>
                                        <th class="pb-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ubicación / Equipo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                                    @forelse($recentChecadas as $idx => $checada)
                                        <tr wire:key="checada-desktop-{{ $checada['id'] }}" class="group hover:bg-gray-50/80 dark:hover:bg-gray-800/30 transition-all {{ $idx === 0 ? 'animate-highlight' : '' }}">
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
                                                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ date('d/m/Y', strtotime($checada['fecha'])) }}</span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="text-sm font-black text-oro tracking-widest uppercase">{{ $checada['hora'] }}</span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-oro/10 border border-oro/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-oro"></span>
                                                    <span class="text-[10px] font-black text-oro uppercase tracking-[0.1em]">{{ $checada['location'] ?? 'Manual / Sync' }}</span>
                                                </div>
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

                        <!-- Mobile View: Cards -->
                        <div class="md:hidden space-y-4">
                            @forelse($recentChecadas as $idx => $checada)
                                <div wire:key="checada-mobile-{{ $checada['id'] }}" 
                                     class="p-4 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 {{ $idx === 0 ? 'animate-highlight' : '' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-[#13322B] text-[#e6d194] flex items-center justify-center font-black text-xs shadow-md">
                                                {{ substr($checada['nombre'], 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-gray-900 dark:text-white leading-none mb-1">{{ $checada['nombre'] }}</p>
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-tight">ID: {{ $checada['num_empleado'] }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-black text-oro uppercase tracking-widest">{{ $checada['hora'] }}</p>
                                            <p class="text-[9px] font-bold text-gray-400">{{ date('d/m/Y', strtotime($checada['fecha'])) }}</p>
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700/50">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-oro"></span>
                                            <span class="text-[9px] font-black text-oro uppercase tracking-widest">{{ $checada['location'] ?? 'Manual / Sync' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 text-center">
                                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Esperando registros...</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes highlight {
            0% { background-color: rgba(230, 209, 148, 0.3); } /* Color oro con opacidad */
            100% { background-color: transparent; }
        }
        .animate-highlight {
            animation: highlight 2s ease-out;
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
