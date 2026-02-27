<div class="py-6 px-4 md:px-8 max-w-7xl mx-auto">
    {{-- Header con Info del Empleado --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-[#13322B] flex items-center justify-center shadow-lg shadow-[#13322B]/20 shrink-0">
                    <span class="text-lg font-black text-[#e6d194]">
                        {{ strtoupper(mb_substr($employee->name, 0, 1)) }}{{
                        strtoupper(mb_substr($employee->father_lastname, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">
                        {{ $employee->fullname }}
                    </h2>
                    <div class="flex items-center gap-3 mt-1 flex-wrap">
                        <span
                            class="px-2.5 py-1 bg-[#9b2247]/10 text-[#9b2247] dark:text-[#e6d194] text-xs font-black rounded uppercase tracking-wider border border-[#9b2247]/20">
                            #{{ $employee->num_empleado }}
                        </span>
                        <span class="text-gray-400 dark:text-gray-500 text-sm uppercase font-semibold">
                            {{ $employee->department->description ?? 'Sin Depto' }}
                        </span>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <span class="text-sm text-[#13322B] dark:text-[#e6d194]/60 font-semibold whitespace-nowrap">
                            {{ $employee->puesto->puesto ?? 'Sin Puesto' }}
                        </span>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <span
                            class="text-sm font-black text-[#9b2247] dark:text-[#e6d194] uppercase tracking-tighter whitespace-nowrap">
                            HORARIO: {{ $employee->horario->horario ?? 'SIN ASIGNAR' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Selectores de Periodo --}}
            <div
                class="flex items-center gap-3 w-full md:w-auto bg-gray-50 dark:bg-gray-900/50 p-2 rounded-xl border dark:border-gray-700">
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Año</label>
                    <select wire:model.live="año"
                        class="bg-transparent border-none text-sm font-bold text-gray-700 dark:text-gray-300 focus:ring-0 py-0 h-7">
                        @foreach($años as $a) <option value="{{ $a }}">{{ $a }}</option> @endforeach
                    </select>
                </div>
                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex flex-col gap-0.5 flex-1 md:w-64">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Periodo /
                        Quincena</label>
                    <select wire:model.live="quincena"
                        class="bg-transparent border-none text-sm font-bold text-gray-700 dark:text-gray-300 focus:ring-0 py-0 h-7 uppercase w-full">
                        @foreach($quincenas as $q) <option value="{{ $q['value'] }}">{{ $q['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                <div>
                    <a href="{{ route('biometrico.individual.pdf', ['employeeId' => $employee->id, 'year' => $año, 'quincena' => $quincena]) }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-sm transition-all text-xs font-bold uppercase tracking-tight">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Checadas --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest w-48">Día /
                            Fecha</th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Entrada</th>
                        <th
                            class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Salida</th>
                        <th class="px-5 py-3 text-[11px] font-black text-gray-400 uppercase tracking-widest">Incidencia
                            /
                            Observaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($checadas as $c)
                    @php
                    $fecha = \Carbon\Carbon::parse($c->fecha);
                    $esFinDeSemana = $fecha->isWeekend();
                    $hoy = $fecha->isToday();
                    @endphp
                    <tr
                        class="{{ $esFinDeSemana ? 'bg-gray-50/30 dark:bg-gray-900/10' : '' }} {{ $hoy ? 'bg-[#13322B]/5 dark:bg-[#13322B]/10 ring-1 ring-inset ring-[#13322B]/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-5 py-2.5">
                            <span
                                class="text-xs font-black uppercase {{ $esFinDeSemana ? 'text-gray-400' : 'text-gray-700 dark:text-gray-300' }} font-mono whitespace-nowrap">
                                {{ mb_strtoupper(str_replace('.', '', $fecha->translatedFormat('D'))) }}. {{
                                $fecha->format('d') }} {{ mb_strtoupper(str_replace('.', '',
                                $fecha->translatedFormat('M'))) }}. {{ $fecha->format('Y') }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 text-center">
                            @if($c->hora_entrada)
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold font-mono text-gray-800 dark:text-gray-100 italic">
                                    {{ date('H:i', strtotime($c->primera_checada)) }}
                                </span>
                                <span
                                    class="text-[10px] text-green-600 dark:text-green-500 font-black uppercase tracking-tighter">Entrada</span>
                            </div>
                            @else
                            <span
                                class="text-xs text-gray-300 dark:text-gray-600 font-black uppercase tracking-tighter">--
                                : --</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5 text-center">
                            @if($c->num_checadas > 1)
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold font-mono text-gray-800 dark:text-gray-100 italic">
                                    {{ date('H:i', strtotime($c->ultima_checada)) }}
                                </span>
                                <span
                                    class="text-[10px] text-[#13322B] dark:text-[#e6d194] font-black uppercase tracking-tighter">Salida</span>
                            </div>
                            @else
                            <span
                                class="text-xs text-gray-300 dark:text-gray-600 font-black uppercase tracking-tighter">--
                                : --</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5">
                            @if($c->incidencias)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(explode(',', $c->incidencias) as $code)
                                <span
                                    class="px-2.5 py-1 bg-[#9b2247] text-white text-[10px] font-black rounded uppercase shadow-xs">
                                    {{ $code }}
                                </span>
                                @endforeach
                            </div>
                            @elseif(!$c->hora_entrada && !$esFinDeSemana && $fecha->isPast())
                            <span class="text-xs font-black text-red-500/50 uppercase italic tracking-tighter">Sin
                                Registro</span>
                            @else
                            <span
                                class="text-xs text-gray-300 dark:text-gray-600 uppercase font-medium italic opacity-50">--</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-full">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-400 italic">No hay registros para este periodo.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="mt-3 flex justify-between items-center text-[9px] text-gray-400 px-2 opacity-60">
        <div class="flex gap-4 uppercase font-bold tracking-widest">
            <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Entrada</span>
            <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-[#13322B]"></span>
                Salida</span>
        </div>

    </div>
</div>