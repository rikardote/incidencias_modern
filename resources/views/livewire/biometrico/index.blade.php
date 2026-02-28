<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-950">
    {{-- Header del Reporte --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#1e5b4f] flex items-center justify-center shadow-lg shadow-[#1e5b4f]/20 shrink-0">
                <svg class="w-6 h-6 text-[#e6d194]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0112 3a10.003 10.003 0 014.139 18.442l.054.09M12 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">
                    Control Biométrico de Asistencia
                </h2>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">
                    Monitoreo y Captura de Incidencias desde Checadas
                </p>
            </div>
        </div>
    </div>

    {{-- Filtros del Reporte (Estilo Vertical Stacked) --}}
    <div class="space-y-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 divide-y divide-gray-50 dark:divide-gray-700">
            
            {{-- Año --}}
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#1e5b4f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Año</label>
                    <div x-data="{ open: false }" @click.away="open = false" class="relative group">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full py-2 pl-3 pr-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1e5b4f]/30 focus:border-[#1e5b4f] transition-all shadow-sm outline-none">
                            <span class="truncate uppercase">{{ $año_seleccionado ?? 'Seleccione Año' }}</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#1e5b4f] transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden origin-top" 
                            style="display: none;">
                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                @foreach($años as $año)
                                <div @click="$wire.set('año_seleccionado', '{{ $año }}'); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $año_seleccionado == $año ? 'bg-[#1e5b4f] text-[#e6d194]' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#1e5b4f]' }}">
                                    <span>{{ $año }}</span>
                                    @if($año_seleccionado == $año)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quincena --}}
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#1e5b4f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Quincena Evaluada</label>
                    @php
                        $qLabel = 'Seleccione Quincena';
                        foreach($quincenas as $q) {
                            if($q['value'] == $quincena_seleccionada) {
                                $qLabel = $q['label']; break;
                            }
                        }
                    @endphp
                    <div x-data="{ open: false }" @click.away="open = false" class="relative group">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full py-2 pl-3 pr-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1e5b4f]/30 focus:border-[#1e5b4f] transition-all shadow-sm outline-none">
                            <span class="truncate uppercase">{{ $qLabel }}</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#1e5b4f] transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden origin-top" 
                            style="display: none;">
                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                @foreach($quincenas as $q)
                                <div @click="$wire.set('quincena_seleccionada', '{{ $q['value'] }}'); open = false"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $quincena_seleccionada == $q['value'] ? 'bg-[#1e5b4f] text-[#e6d194]' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#1e5b4f]' }}">
                                    <span>{{ $q['label'] }}</span>
                                    @if($quincena_seleccionada == $q['value'])
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Centro de Trabajo --}}
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-[#1e5b4f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Centro de Trabajo</label>
                    @php
                        $cLabel = 'Seleccione Centro de Trabajo';
                        foreach($centros as $c) {
                            if($c->id == $centro_seleccionado) {
                                $cLabel = $c->code . ' - ' . $c->description; break;
                            }
                        }
                    @endphp
                    <div x-data="{ open: false, search: '' }" @click.away="open = false; search = ''" class="relative group">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full py-2 pl-3 pr-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1e5b4f]/30 focus:border-[#1e5b4f] transition-all shadow-sm outline-none">
                            <span class="truncate uppercase text-left">{{ $cLabel }}</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#1e5b4f] transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden origin-top" 
                            style="display: none;">
                            
                            {{-- Buscador Integrado --}}
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                                <input type="text" x-model="search" placeholder="BUSCAR CENTRO..." 
                                    class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-black uppercase text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-[#1e5b4f]/30 focus:border-[#1e5b4f] outline-none placeholder-gray-400 transition-all shadow-sm">
                            </div>

                            <div class="max-h-52 overflow-y-auto p-1.5 space-y-1">
                                <div @click="$wire.set('centro_seleccionado', ''); open = false; search = ''"
                                    x-show="'seleccione centro de trabajo'.includes(search.toLowerCase()) || search === ''"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ empty($centro_seleccionado) ? 'bg-[#1e5b4f] text-[#e6d194]' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#1e5b4f]' }}">
                                    <span>Seleccione Centro</span>
                                    @if(empty($centro_seleccionado))
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @foreach($centros as $centro)
                                <div @click="$wire.set('centro_seleccionado', '{{ $centro->id }}'); open = false; search = ''"
                                    x-show="'{{ strtolower($centro->code . ' ' . $centro->description) }}'.includes(search.toLowerCase())"
                                    class="px-3 py-2.5 rounded-xl cursor-pointer text-sm font-black uppercase transition-all flex items-center justify-between {{ $centro_seleccionado == $centro->id ? 'bg-[#1e5b4f] text-[#e6d194]' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#1e5b4f]' }}">
                                    <span class="truncate pr-2 w-full">{{ $centro->code }} - {{ $centro->description }}</span>
                                    @if($centro_seleccionado == $centro->id)
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón de Exportación (Full Width) --}}
        <button wire:click="exportPdf" 
            class="w-full bg-[#9b2247] hover:bg-[#7a1b38] text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all shadow-xl shadow-[#9b2247]/20 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Exportar Biométrico PDF
        </button>
    </div>

    {{-- Vista Previa de Registros --}}
    @if($this->centro_seleccionado)
        <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-6">
            @forelse($empleados as $num_empleado => $registrosEmpleado)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                    {{-- Header de la Card del Empleado (Layout de Expediente Premium) --}}
                    <div class="bg-white dark:bg-gray-800 p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col justify-between h-40 shrink-0 relative hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-colors">
                        
                        {{-- Top: Identidad y Horario --}}
                        <div class="flex gap-4 items-start justify-between">
                            {{-- Izquierda: Avatar y Nombre --}}
                            <div class="flex gap-4 items-start min-w-0 flex-1">
                                {{-- Avatar Cuadrado Institucional --}}
                                <div class="w-12 h-12 rounded-xl bg-[#13322B] flex items-center justify-center shrink-0 shadow-md border border-[#1e5b4f]/30">
                                    <span class="text-[13px] font-black text-[#e6d194] leading-none tracking-tighter">
                                        {{ mb_strtoupper(mb_substr($registrosEmpleado->first()->nombre, 0, 1)) }}{{ mb_strtoupper(mb_substr($registrosEmpleado->first()->apellido_paterno, 0, 1)) }}
                                    </span>
                                </div>
                                
                                {{-- Nombre Amplio y Flexible --}}
                                <div class="min-w-0 flex-1 pt-0.5">
                                    <a href="{{ route('employees.incidencias', ['employeeId' => $registrosEmpleado->first()->employee_id]) }}" target="_blank" class="group block">
                                        <h3 class="font-black text-gray-900 dark:text-gray-100 text-[11px] leading-tight uppercase group-hover:text-[#9b2247] transition-colors line-clamp-2">
                                            {{ $registrosEmpleado->first()->apellido_paterno }} {{ $registrosEmpleado->first()->apellido_materno }}
                                        </h3>
                                        <p class="text-[11px] font-bold text-[#1e5b4f] uppercase tracking-wider mt-0.5 truncate flex items-center gap-2">
                                            {{ $registrosEmpleado->first()->nombre }}
                                        </p>
                                        <div class="mt-1.5 flex items-center">
                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-[8px] font-black rounded border border-gray-200 dark:border-gray-600 uppercase tracking-widest">
                                                ID: #{{ $num_empleado }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            {{-- Derecha: Horario (Top Right) --}}
                            <div class="flex flex-col items-end shrink-0">
                                <div class="flex items-center gap-1.5 px-2 py-1 bg-gray-50 dark:bg-gray-700/30 rounded border border-gray-100 dark:border-gray-600">
                                    <svg class="w-3.5 h-3.5 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest whitespace-nowrap">
                                        {{ $registrosEmpleado->first()->horario_entrada ? substr($registrosEmpleado->first()->horario_entrada, 0, 5) . ' - ' . substr($registrosEmpleado->first()->horario_salida, 0, 5) : $registrosEmpleado->first()->horario }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom: Acción (Completo) --}}
                        <div class="flex justify-end items-end mt-2">
                            {{-- Botón de Acción Limpio (Inferior Derecha) --}}
                            <div class="flex-shrink-0 h-8 flex items-end">
                                @if(!$registrosEmpleado->contains(fn($r) => !empty($r->incidencias)))
                                <button 
                                    x-data
                                    @click.stop="Swal.fire({
                                        title: '¿Marcar sin incidencias?',
                                        text: 'Se capturará el código 77 para toda la quincena.',
                                        icon: 'info',
                                        showCancelButton: true,
                                        confirmButtonColor: '#1e5b4f',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, procesar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.captureSinIncidencias({{ $registrosEmpleado->first()->employee_id }});
                                        }
                                    })"
                                    class="group/btn flex items-center gap-1.5 px-3 py-1.5 bg-[#f8fafc] dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm hover:bg-[#1e5b4f] hover:border-[#1e5b4f] text-gray-600 dark:text-gray-300 hover:text-[#e6d194] text-[9px] font-black rounded-lg transition-all uppercase tracking-widest"
                                >
                                    <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:scale-110 text-gray-400 group-hover/btn:text-[#e6d194]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Completo</span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TABLA DE CHECADAS (Ajustada para reducir espacio en blanco) --}}
                    <div class="flex-grow pb-3">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-xs table-fixed border-collapse">
                            <thead class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100/50 dark:border-gray-700/50">
                                <tr>
                                    <th class="w-[90px] px-3 py-3 text-left">
                                        <div class="flex items-center gap-1.5 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                            <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>Fecha</span>
                                        </div>
                                    </th>
                                    <th class="w-[70px] px-3 py-3 text-left">
                                        <div class="flex items-center gap-1 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                            <svg class="w-3 h-3 text-[#1e5b4f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path></svg>
                                            <span>Ent</span>
                                        </div>
                                    </th>
                                    <th class="w-[70px] px-3 py-3 text-left">
                                        <div class="flex items-center gap-1 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                            <svg class="w-3 h-3 text-[#9b2247]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                            <span>Sal</span>
                                        </div>
                                    </th>
                                    <th class="px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                            <span>Inc</span>
                                            <svg class="w-2.5 h-2.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($registrosEmpleado as $registro)
                                    @php
                                        // Lógica de procesamiento de checadas (heredada de legacy)
                                        $horarioEntrada = $registrosEmpleado->first()->horario_entrada ? strtotime($registrosEmpleado->first()->horario_entrada) : null;
                                        $horarioSalida = $registrosEmpleado->first()->horario_salida ? strtotime($registrosEmpleado->first()->horario_salida) : null;
                                        $esJornadaVespertina = $registrosEmpleado->first()->es_jornada_vespertina == 1;

                                        $horaMedia = ($esJornadaVespertina && $horarioEntrada && $horarioSalida) 
                                            ? $horarioEntrada + (($horarioSalida - $horarioEntrada) / 2)
                                            : strtotime('12:00:00');

                                        $medioDia = $horaMedia;
                                        $horaEntrada = $registro->hora_entrada ? strtotime($registro->hora_entrada) : null;
                                        $horaSalida = $registro->hora_salida ? strtotime($registro->hora_salida) : null;
                                        $tieneUnaSolaChecada = $registro->hora_entrada && $registro->hora_entrada === $registro->hora_salida;
                                        $estaChecadaDespuesMedioDia = $horaEntrada && $horaEntrada > $medioDia;

                                        $checadasDelDia = $registrosEmpleado->where('fecha', $registro->fecha)->sortBy('hora_entrada');
                                        $tieneMasDeUnaChecada = $checadasDelDia->count() > 1;
                                        $esLaPrimeraChecadaDelDia = $checadasDelDia->first() === $registro;
                                        $esLaUltimaChecadaDelDia = $checadasDelDia->last() === $registro;

                                        $conteoChecadasDespuesMedioDia = 0;
                                        foreach ($checadasDelDia as $c) {
                                            $checadaHora = $c->hora_entrada ? strtotime($c->hora_entrada) : null;
                                            if ($checadaHora && $checadaHora > $medioDia) $conteoChecadasDespuesMedioDia++;
                                        }

                                        $esSalidaPorHorario = $estaChecadaDespuesMedioDia;
                                        if ($estaChecadaDespuesMedioDia && $conteoChecadasDespuesMedioDia > 1) {
                                            $primeraChecadaPostMediodia = $checadasDelDia->first(fn($c) => strtotime($c->hora_entrada) > $medioDia);
                                            $esPrimeraChecadaDespuesMediodia = ($primeraChecadaPostMediodia === $registro);
                                            $esSalidaPorHorario = !$esPrimeraChecadaDespuesMediodia || $tieneUnaSolaChecada;
                                        }

                                        $mostrarOmisionEntrada = ($estaChecadaDespuesMedioDia && ($esLaPrimeraChecadaDelDia || (isset($esPrimeraChecadaDespuesMediodia) && $esPrimeraChecadaDespuesMediodia)));
                                        
                                        if ($esJornadaVespertina && $esLaPrimeraChecadaDelDia && $horaEntrada) {
                                            if (abs($horaEntrada - $horarioEntrada) / 60 <= 90) $mostrarOmisionEntrada = false;
                                        }

                                        $jornadaTerminada = !\Carbon\Carbon::parse($registro->fecha)->isToday() || (\Carbon\Carbon::parse($registro->fecha)->isToday() && now()->format('H:i:s') > $registrosEmpleado->first()->horario_salida);
                                        
                                        $rowClass = '';
                                        if ($registro->retardo && strpos($registro->incidencias ?? '', '7') === false) $rowClass = 'bg-red-50/50 dark:bg-red-900/20';
                                        
                                        $hasColoredInc = false;
                                        if ($registro->incidencias) {
                                            $incList = explode(',', $registro->incidencias);
                                            foreach($incList as $inc) {
                                                if (!in_array($inc, $incidenciasSinColor)) {
                                                    $hasColoredInc = true;
                                                    break;
                                                }
                                            }
                                        }
                                        if ($hasColoredInc) $rowClass = 'bg-amber-50/50 dark:bg-amber-900/20';
                                        
                                        $isWeekend = \Carbon\Carbon::parse($registro->fecha)->isWeekend();
                                        if ($isWeekend && empty($rowClass)) $rowClass = 'bg-slate-50/40 dark:bg-black/20';
                                    @endphp

                                    <tr 
                                        wire:click="openCaptureModal({{ $registro->employee_id }}, {{ $num_empleado }}, '{{ $registro->apellido_paterno }} {{ $registro->apellido_materno }} {{ $registro->nombre }}', '{{ $registro->fecha }}')"
                                        class="{{ $rowClass }} hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors cursor-pointer group"
                                    >
                                        <td class="px-3 py-1.5 font-medium text-gray-500 dark:text-gray-300 whitespace-nowrap {{ $isWeekend ? 'opacity-40' : '' }}">
                                            {{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}
                                        </td>
        
                                        <td class="px-3 py-1.5 whitespace-nowrap">
                                            @if($esJornadaVespertina && $esLaPrimeraChecadaDelDia && $registro->hora_entrada)
                                                <div class="flex items-center">
                                                    <span class="{{ $registro->retardo && strpos($registro->incidencias ?? '', '7') === false ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                                        {{ substr($registro->hora_entrada, 0, 5) }}
                                                    </span>
                                                    @if($registro->retardo && strpos($registro->incidencias ?? '', '7') === false)
                                                        <span class="ml-1 bg-rose-500 text-white text-[8px] font-black px-1 rounded flex items-center justify-center min-w-[14px]">R</span>
                                                    @endif
                                                </div>
                                            @elseif($mostrarOmisionEntrada)
                                                <span class="text-rose-500 font-bold" title="Omisión de entrada">──:──</span>
                                            @elseif($registro->hora_entrada)
                                                <div class="flex items-center">
                                                    <span class="{{ $registro->retardo && strpos($registro->incidencias ?? '', '7') === false ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                                        {{ substr($registro->hora_entrada, 0, 5) }}
                                                    </span>
                                                    @if($registro->retardo && strpos($registro->incidencias ?? '', '7') === false)
                                                        <span class="ml-1 bg-rose-500 text-white text-[8px] px-1 rounded">R</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap">
                                            @if(($estaChecadaDespuesMedioDia && $esSalidaPorHorario) || ($registro->hora_salida && !$tieneUnaSolaChecada))
                                                <span class="text-gray-700 dark:text-gray-300">{{ substr($registro->hora_salida, 0, 5) }}</span>
                                            @elseif(($tieneUnaSolaChecada || ($registro->hora_entrada && !$registro->hora_salida)) && $jornadaTerminada)
                                                <span class="text-rose-400 dark:text-rose-300/60 opacity-60 italic text-[10px]">Sin salida</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-1.5 text-center">
                                            @if($registro->incidencias)
                                                <div class="flex flex-wrap justify-center gap-1">
                                                    @php
                                                        $incs = explode(',', $registro->incidencias);
                                                        $tokens = explode(',', $registro->incidencias_tokens);
                                                    @endphp
                                                    @foreach($incs as $idx => $inc)
                                                        @php $token = $tokens[$idx] ?? ''; @endphp
                                                        <button 
                                                            x-data 
                                                            @click.stop="Swal.fire({
                                                                title: '¿Confirmar eliminación?',
                                                                text: 'Esta acción borrará el registro de la incidencia código {{ $inc }}',
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#9b2247',
                                                                cancelButtonColor: '#6b7280',
                                                                confirmButtonText: 'Sí, eliminar',
                                                                cancelButtonText: 'Cancelar'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    $wire.deleteIncidencia('{{ $token }}');
                                                                }
                                                            })"
                                                            class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ in_array($inc, $incidenciasSinColor) ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' : 'bg-amber-100 dark:bg-amber-400 text-amber-900 dark:text-amber-950 hover:bg-amber-200 dark:hover:bg-amber-300' }} transition-colors"
                                                            title="Código {{ $inc }}. Click para eliminar"
                                                        >
                                                            {{ $inc }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">No se encontraron registros para esta selección</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="py-20 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-[#1e5b4f]/5 rounded-3xl flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-[#1e5b4f] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-sm font-black text-gray-800 dark:text-gray-100 uppercase tracking-widest">Seleccionar Centro</h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Elija un centro de trabajo para visualizar las asistencias</p>
        </div>
    @endif

    {{-- MODAL DE CAPTURA (ESTILO PREMIUM INSTITUCIONAL) --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            {{-- Backdrop (Coherente con otros reportes) --}}
            <div class="fixed inset-0 bg-[#13322B]/60 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            {{-- Cuerpo del Modal (Equilibrado y con Alma) --}}
            <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl flex flex-col z-10 transition-all transform animate-in zoom-in-95 duration-200 border border-white/5 overflow-hidden">
                {{-- Header Sólido Institucional (#13322B) --}}
                <div class="px-6 py-5 bg-[#13322B] text-white flex justify-between items-center relative shrink-0 border-b border-white/5">
                    <div class="relative z-20 flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg text-[#e6d194]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest">Captura Rápida</h3>
                            <p class="text-[9px] font-bold text-[#e6d194] uppercase tracking-widest opacity-80">Justificación Biométrica</p>
                        </div>
                    </div>
                    <button wire:click="closeModal" class="relative z-20 p-2 hover:bg-white/10 rounded-lg transition-colors text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-8 space-y-6">
                    {{-- Info Empleado (Estilo Premium) --}}
                    <div class="p-5 bg-gray-50/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-2 py-0.5 bg-[#9b2247] text-white text-[9px] font-black rounded uppercase tracking-widest">
                                ID: #{{ $selectedEmployeeNumEmpleado }}
                            </span>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d \d\e F, Y') }}
                            </span>
                        </div>
                        <p class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight leading-relaxed">
                            {{ $selectedEmployeeName }}
                        </p>
                    </div>

                    {{-- Selector de Incidencia --}}
                    <div class="px-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Concepto de Incidencia / Código</label>
                        <select wire:model="incidencia_id" 
                            class="w-full bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-800 dark:text-gray-200 rounded-xl focus:ring-[#13322B] focus:border-[#13322B] py-3.5 shadow-sm uppercase">
                            <option value="">Seleccione una opción...</option>
                            @foreach($codigos as $cod)
                                <option value="{{ $cod->id }}">{{ $cod->code }} - {{ $cod->description }}</option>
                            @endforeach
                        </select>
                        @error('incidencia_id') 
                            <span class="text-[#9b2247] text-[10px] font-bold mt-2 block px-1 uppercase tracking-tight italic">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="px-8 py-5 bg-gray-50/50 dark:bg-gray-900/50 flex justify-end gap-3 border-t dark:border-gray-700">
                    <button wire:click="closeModal" class="px-6 py-3 text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="saveIncidencia" class="px-10 py-3 bg-[#13322B] text-[#e6d194] text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-[#13322B]/20 hover:scale-105 transition-all">
                        Guardar Registro
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script shadow>
        document.addEventListener('livewire:init', () => {
            window.addEventListener('storage', (event) => {
                if (event.key === 'biometrico_refresh') {
                    Livewire.dispatch('refreshBiometrico');
                }
            });
        });
    </script>
</div>
