<div class="relative antialiased">
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(212, 175, 55, 0.3);
            border-radius: 20px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(212, 175, 55, 0.6);
        }

        /* Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(212, 175, 55, 0.3) rgba(0, 0, 0, 0.1) !important;
        }
    </style>
    <div x-show="$store.island.logIsOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="$store.island.toggleLog()"
        class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-[70]" x-cloak></div>

    <div x-show="$store.island.logIsOpen"
        x-transition:enter="transition ease-out duration-500 cubic-bezier(0.4, 0, 0.2, 1)"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-400" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 bottom-0 w-full max-w-[550px] bg-[#0a1f1a] z-[80] shadow-2xl border-l border-white/10 flex flex-col"
        x-cloak>

        <div class="p-4 border-b border-white/5 flex items-center justify-between bg-[#0d2a23]">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.6)]">
                </div>
                <h2 class="text-sm font-black text-white tracking-[0.2em] uppercase">Bitácora de Captura</h2>
            </div>
            <button @click="$store.island.toggleLog()" class="p-1 text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-x-auto overflow-y-auto custom-scrollbar bg-black/20">
            <table class="w-full text-left border-separate border-spacing-0 min-w-[500px]">
                <thead>
                    <tr class="bg-[#0d2a23] sticky top-0 z-10">
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/10">
                            Qna/Per</th>
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/10">
                            Empleado / Incidencia</th>
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/10">
                            Fechas</th>
                        <th
                            class="p-3 text-[9px] font-black text-gray-500 uppercase tracking-widest border-b border-white/10">
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
                    @endphp
                    <tr class="hover:bg-white/[0.03] transition-colors group text-gray-300">
                        <td class="p-4 align-top">
                            <div class="text-[10px] font-black text-[#D4AF37] mb-1">{{ $details['qnas'] ?? 'S/Q' }}
                            </div>
                            <div class="text-[9px] font-bold text-gray-500 italic">{{ $details['periodo'] ?? '--' }}
                            </div>
                        </td>
                        <td class="p-4 align-top">
                            <div
                                class="text-[11px] font-black uppercase tracking-tight group-hover:text-[#D4AF37] transition-colors">
                                {{ $log['employee_name'] }}</div>
                            <div class="mt-2 flex items-center gap-2">
                                <span
                                    class="text-[9px] font-black bg-white/5 text-gray-400 px-2 py-0.5 rounded border border-white/10">C{{
                                    $log['type'] }}</span>
                                <span class="text-[10px] font-black text-emerald-500">{{ $total_dias }} Días</span>
                            </div>
                        </td>
                        <td class="p-4 align-top whitespace-nowrap">
                            <div class="flex flex-col gap-1 text-[10px] font-bold">
                                <div class="flex items-center gap-2 text-gray-400"><span
                                        class="text-[8px] font-black w-4">IN</span> {{ $fecha_inicio }}</div>
                                <div class="flex items-center gap-2 text-gray-400"><span
                                        class="text-[8px] font-black w-4">FI</span> {{ $fecha_final }}</div>
                            </div>
                        </td>
                        <td class="p-4 align-top whitespace-nowrap text-right">
                            <div class="text-[10px] font-bold text-gray-200">{{ $log['user_name'] }}</div>
                            <div class="text-[9px] font-medium text-gray-500 mt-1 uppercase">{{
                                \Carbon\Carbon::parse($log['created_at'])->diffForHumans(null, true) }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4"
                            class="p-20 text-center opacity-30 text-[10px] font-black tracking-widest uppercase">Sin
                            registros</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-[#0d2a23] border-t border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Live Sync Reverb</span>
            </div>
            <span class="text-[9px] font-bold text-gray-600 uppercase tabular-nums">{{ date('H:i:s') }}</span>
        </div>
    </div>
</div>