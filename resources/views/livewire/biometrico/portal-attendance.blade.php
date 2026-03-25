<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-[11px] font-black text-gray-500 uppercase tracking-widest">Día / Fecha</th>
                    <th class="px-5 py-3 text-[11px] font-black text-gray-500 uppercase tracking-widest text-center">Registros</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($checadas as $c)
                    @php
                        $fecha = \Carbon\Carbon::parse($c->fecha);
                        $esFinDeSemana = $fecha->isWeekend();
                    @endphp
                    <tr class="{{ $esFinDeSemana ? 'bg-gray-50/50' : '' }} hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase text-gray-400">
                                    {{ mb_strtoupper($fecha->translatedFormat('l')) }}
                                </span>
                                <span class="text-sm font-black text-gray-800 tracking-tighter">
                                    {{ $fecha->format('d/m/Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex items-center gap-3 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                                    <div class="flex flex-col items-center">
                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Entrada</span>
                                        <span class="text-sm font-mono font-bold {{ $c->hora_entrada ? 'text-emerald-600' : 'text-gray-300' }}">
                                            {{ $c->hora_entrada ? date('H:i', strtotime($c->primera_checada)) : '--:--' }}
                                        </span>
                                    </div>
                                    <div class="h-6 w-px bg-gray-200"></div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Salida</span>
                                        <span class="text-sm font-mono font-bold {{ $c->num_checadas > 1 ? 'text-[#13322b]' : 'text-gray-300' }}">
                                            {{ $c->num_checadas > 1 ? date('H:i', strtotime($c->ultima_checada)) : '--:--' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Sin registros biométricos recientes</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
