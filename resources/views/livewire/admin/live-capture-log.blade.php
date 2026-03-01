<div x-data="{ 
    open: false,
    newItems: 0
}" x-on:toggle-live-log.window="open = !open; newItems = 0" x-on:live-log-new.window="if(!open) newItems++"
    class="relative">

    <!-- Token de Acceso Flotante -->
    <div class="fixed bottom-6 right-24 z-[60] flex flex-col gap-3">
        <button @click="open = !open; newItems = 0"
            class="group relative w-12 h-12 bg-[#13322B] hover:bg-[#0a1f1a] text-oro rounded-full shadow-2xl border border-oro/20 flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95">

            <!-- Badge -->
            <template x-if="newItems > 0">
                <span class="absolute -top-1 -right-1 flex h-5 w-5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span
                        class="relative inline-flex rounded-full h-5 w-5 bg-red-500 text-[10px] text-white font-black flex items-center justify-center"
                        x-text="newItems"></span>
                </span>
            </template>

            <svg class="w-5 h-5 transition-transform duration-500" :class="open ? 'rotate-90' : ''" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-[70]"></div>

    <!-- Sidebar Content -->
    <div x-show="open" x-transition:enter="transition ease-out duration-500 cubic-bezier(0.4, 0, 0.2, 1)"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-400" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 bottom-0 w-full max-w-[550px] bg-[#0a1f1a] z-[80] shadow-2xl border-l border-white/10 flex flex-col">

        <!-- Header -->
        <div class="p-4 border-b border-white/5 flex items-center justify-between bg-[#0d2a23]">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <h2 class="text-sm font-black text-white tracking-[0.2em] uppercase">Bitácora de Captura</h2>
            </div>
            <button @click="open = false" class="text-gray-500 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Table Content -->
        <div class="flex-1 overflow-x-auto overflow-y-auto custom-scrollbar bg-black/20">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr class="bg-black/40 sticky top-0 z-10">
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/5">
                            Qna/Per</th>
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/5">
                            Empleado / Incidencia</th>
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/5">
                            Fechas</th>
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/5">
                            Captura</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                    @php
                    $details = $log['details'] ?? [];
                    $fecha_inicio = isset($details['fecha_inicio']) ?
                    \Carbon\Carbon::parse($details['fecha_inicio'])->format('d/m/y') : '--';
                    $fecha_final = isset($details['fecha_final']) ?
                    \Carbon\Carbon::parse($details['fecha_final'])->format('d/m/y') : '--';
                    $total_dias = $details['total_dias'] ?? 0;
                    $qnas = $details['qnas'] ?? 'S/Q';
                    $periodo = $details['periodo'] ?? '--';
                    @endphp
                    <tr class="hover:bg-white/[0.03] transition-colors group">
                        <!-- QNA / PERIODO -->
                        <td class="p-3 align-top whitespace-nowrap">
                            <div class="text-[10px] font-black text-oro mb-1">{{ $qnas }}</div>
                            <div class="text-[9px] font-bold text-gray-500">{{ $periodo !== 'N/A' ? "Periodo: $periodo"
                                : '--' }}</div>
                        </td>

                        <!-- EMPLEADO / TIPO -->
                        <td class="p-3 align-top">
                            <div
                                class="text-[11px] font-black text-gray-100 uppercase tracking-tight group-hover:text-oro transition-colors leading-tight">
                                {{ $log['employee_name'] }}
                            </div>
                            <div class="mt-1 flex items-center gap-2">
                                <span
                                    class="text-[9px] font-black bg-white/5 text-gray-400 px-1.5 py-0.5 rounded border border-white/10">
                                    C{{ $log['type'] }}
                                </span>
                                <span class="text-[10px] font-black text-emerald-500">{{ $total_dias }} Días</span>
                            </div>
                        </td>

                        <!-- FECHAS -->
                        <td class="p-3 align-top whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[8px] font-black text-gray-600 uppercase w-4">IN</span>
                                    <span class="text-[10px] font-bold text-gray-300">{{ $fecha_inicio }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[8px] font-black text-gray-600 uppercase w-4">FI</span>
                                    <span class="text-[10px] font-bold text-gray-300">{{ $fecha_final }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- CAPTURADO POR -->
                        <td class="p-3 align-top whitespace-nowrap">
                            <div class="text-[10px] font-bold text-gray-200">{{ $log['user_name'] }}</div>
                            <div class="text-[9px] font-medium text-gray-500 mt-1 uppercase">
                                {{ \Carbon\Carbon::parse($log['created_at'])->diffForHumans(null, true) }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4"
                            class="p-10 text-center text-gray-600 text-[10px] font-black uppercase tracking-widest italic opacity-50">
                            No hay capturas recientes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="p-3 bg-[#0d2a23] border-t border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Sincronizado vía
                    Reverb</span>
            </div>
            <span class="text-[8px] font-bold text-gray-600 uppercase">Snapshot {{ date('H:i:s') }}</span>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Echo.join('chat')
                .listen('.NewIncidenciaBatchCreated', (e) => {
                    console.log('BATCH RECEPTION:', e);
                    // Dispatch to increment badge
                    window.dispatchEvent(new CustomEvent('live-log-new'));
                    // Force Livewire refresh immediately as backup
                    @this.$refresh();
                });
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
    </style>
</div>