<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div
            class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-200 dark:border-gray-800">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span
                        class="px-2 py-0.5 bg-[#9b2247]/10 text-[#9b2247] dark:text-[#e6d194] text-[10px] font-black uppercase tracking-widest rounded shadow-sm">
                        PERFIL ANALÍTICO
                    </span>
                    <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter italic">ID: {{
                        $this->employee->num_empleado }}</span>
                </div>
                <h1 class="text-4xl font-black text-[#13322B] dark:text-gray-100 uppercase tracking-tight">
                    {{ $this->employee->fullname }}
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 font-medium">Consolidado histórico y estadística
                    de desempeño institucional.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('employees.incidencias', $this->employee->id) }}" wire:navigate
                    class="px-4 py-2 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-black uppercase tracking-widest border border-gray-100 dark:border-gray-700 hover:border-oro transition-all shadow-sm">
                    Ver Incidencias
                </a>
                <button onclick="window.print()"
                    class="px-5 py-2 bg-[#13322B] text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#0a1b17] transition-all shadow-md">
                    Imprimir Perfil
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Sidebar: Personal Profile -->
            <div class="space-y-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transform hover:scale-[1.01] transition-transform duration-300">
                    <div class="h-24 bg-gradient-to-br from-[#13322B] to-[#0a1b17] relative">
                        <div class="absolute -bottom-8 left-8">
                            <div
                                class="w-20 h-20 rounded-2xl bg-white dark:bg-gray-700 border-4 border-white dark:border-gray-800 shadow-xl flex items-center justify-center overflow-hidden">
                                <span class="text-2xl font-black text-[#13322B] dark:text-[#e6d194]">
                                    {{ strtoupper(mb_substr($this->employee->name, 0, 1)) }}{{
                                    strtoupper(mb_substr($this->employee->father_lastname, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-12 p-8 space-y-6">
                        <div>
                            <h3 class="text-xs font-black text-oro uppercase tracking-[0.2em] mb-4">Información Laboral
                            </h3>
                            <div class="space-y-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-gray-400 uppercase">Centro de Trabajo</span>
                                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{
                                        $this->employee->department->description ?? 'NO ASIGNADO' }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-gray-400 uppercase">Puesto Oficial</span>
                                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{
                                        $this->employee->puesto->puesto ?? 'SIN PUESTO' }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-gray-400 uppercase">Jornada / Horario</span>
                                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200">
                                        {{ $this->employee->jornada->jornada ?? '—' }} | {{
                                        $this->employee->horario->horario ?? '—'
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50 dark:border-gray-700/50">
                            <h3 class="text-xs font-black text-oro uppercase tracking-[0.2em] mb-4">Datos de Control
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-gray-400 uppercase">No. Plaza</span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 font-mono">{{
                                        $this->employee->num_plaza ?? 'N/A' }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-gray-400 uppercase">NS Seguros</span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 font-mono">{{
                                        $this->employee->num_seguro ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                @if($this->employee->comisionado)
                                <span
                                    class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase rounded shadow-sm scale-95 origin-left">Comisionado</span>
                                @endif
                                @if($this->employee->estancia)
                                <span
                                    class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[10px] font-black uppercase rounded shadow-sm scale-95 origin-left">Estancia</span>
                                @endif
                                @if($this->employee->lactancia)
                                <span
                                    class="px-2 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-[10px] font-black uppercase rounded shadow-sm scale-95 origin-left">Lactancia</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dashboard Cards --}}
                <div class="grid grid-cols-1 gap-4">
                    <div
                        class="bg-gradient-to-br from-[#13322B] to-[#0a1b17] p-6 rounded-3xl shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-oro/10 rounded-full blur-2xl group-hover:scale-150 transition-all duration-700">
                        </div>
                        <div class="relative z-10 flex flex-col">
                            <span
                                class="text-oro font-black text-[10px] uppercase tracking-widest mb-1 opacity-70">Total
                                Histórico</span>
                            <span class="text-4xl font-black text-white leading-none mb-1">{{ $this->stats['totalDays']
                                }}</span>
                            <span class="text-[10px] font-bold text-white/50 uppercase tracking-tighter">Días
                                Ausente</span>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all">
                        <div class="flex flex-col">
                            <span
                                class="text-gray-400 font-black text-[10px] uppercase tracking-widest mb-1">Registros</span>
                            <span class="text-3xl font-black text-[#13322B] dark:text-oro leading-none mb-1">{{
                                $this->stats['totalIncidencias'] }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter italic">Eventos
                                Capturados</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Stats: Charts and Lists -->
            <div class="lg:col-span-2 space-y-8">

                {{-- Global Impact Algorithm Analysis --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Impacto por
                                Código</h3>
                            <p class="text-[10px] font-bold text-gray-500 uppercase">Desglose acumulado de incidencias
                                institucionales</p>
                        </div>
                        <div class="text-[#13322B] dark:text-oro opacity-20">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach($this->stats['byCode'] as $code)
                        <div class="group">
                            <div class="flex justify-between items-end mb-2">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-oro uppercase tracking-widest">Código {{
                                        $code->codigo->code }}</span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{
                                        $code->codigo->description }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-xs font-black text-[#13322B] dark:text-oro">{{ $code->days }}
                                            Días</div>
                                        <div class="text-[9px] font-bold text-gray-400 uppercase">{{ $code->total }}
                                            Eventos</div>
                                    </div>
                                </div>
                            </div>
                            @php
                            $percent = $this->stats['totalDays'] > 0 ? ($code->days / $this->stats['totalDays']) * 100 :
                            0;
                            @endphp
                            <div class="w-full h-2 bg-gray-50 dark:bg-gray-900 rounded-full overflow-hidden">
                                <div class="h-full bg-oro transition-all duration-1000 origin-left"
                                    style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        @endforeach

                        @if($this->stats['byCode']->isEmpty())
                        <div class="py-12 text-center text-gray-400 italic text-sm">Sin historial de incidencias
                            disponible.</div>
                        @endif
                    </div>
                </div>

                {{-- Trend: Activity Current Year --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Tendencia
                                Mensual ({{ $year }})</h3>
                            <p class="text-[10px] font-bold text-gray-500 uppercase">Frecuencia de ausentismo por
                                periodo</p>
                        </div>
                    </div>

                    <div class="flex items-end justify-between h-40 gap-1.5 md:gap-4 px-2">
                        @php
                        $monthNames = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                        $maxDaysTrend = collect($this->stats['trendData'])->max('days') ?: 1;
                        @endphp
                        @foreach($this->stats['trendData'] as $month => $data)
                        <div class="flex-1 flex flex-col items-center group relative">
                            <div class="w-full bg-[#13322B]/5 dark:bg-white/5 rounded-t-lg transition-all duration-700 hover:bg-oro/30 relative"
                                style="height: {{ (float)($data->days / $maxDaysTrend) * 100 }}%">
                                <div
                                    class="absolute -top-6 left-1/2 -translate-x-1/2 text-[9px] font-black text-[#13322B] dark:text-oro opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ $data->days }}
                                </div>
                            </div>
                            <span class="text-[9px] font-black text-gray-400 uppercase mt-3 tracking-tighter">{{
                                $monthNames[$month-1] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/10 flex justify-between items-center text-xs font-black uppercase tracking-widest text-gray-500">
                        Últimos Registros
                        <span class="text-[9px] lowercase italic text-gray-400 font-normal">Mostrando los 5 más
                            recientes</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr
                                    class="bg-gray-50/50 dark:bg-gray-900/50 text-gray-400 font-black uppercase tracking-wider">
                                    <th class="px-8 py-4">Fecha</th>
                                    <th class="px-4 py-4">Código / Concepto</th>
                                    <th class="px-4 py-4">Días</th>
                                    <th class="px-8 py-4 text-right">Estatus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                @forelse($this->stats['recent'] as $inc)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-8 py-4 font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}
                                        <span class="text-[9px] block text-gray-400 font-normal">{{ $inc->qna->qna ??
                                            'N/A' }} / {{ $inc->qna->year ?? '' }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="font-black text-[#9b2247] dark:text-[#e6d194] leading-tight block">[{{
                                            $inc->codigo->code }}]</span>
                                        <span class="text-[10px] text-gray-500 font-medium line-clamp-1 uppercase">{{
                                            $inc->codigo->description }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="px-2 py-0.5 bg-gray-100 dark:bg-gray-900 rounded text-xs font-black text-gray-600 dark:text-gray-400">{{
                                            $inc->total_dias }}</span>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <span
                                            class="inline-flex w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-10 text-center text-gray-400 italic">No hay registros
                                        recientes.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        @media print {

            nav,
            .pb-6,
            button,
            a,
            .mb-10,
            .py-12>div>div:first-child {
                display: none !important;
            }

            .max-w-7xl {
                max-width: 100% !important;
                border: none !important;
                padding: 0 !important;
            }

            .bg-white {
                background: transparent !important;
            }

            .shadow-sm,
            .shadow-lg,
            .shadow-xl {
                box-shadow: none !important;
            }

            .border {
                border: 1px solid #eee !important;
            }

            body {
                background: white !important;
                color: black !important;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</div>