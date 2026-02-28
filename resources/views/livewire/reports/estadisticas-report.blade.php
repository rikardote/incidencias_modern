<div class="py-12" x-data="{ 
    selectedCodeName: 'Seleccionar Incidencia',
    selectedDepartmentName: 'Todos los departamentos',
    openCode: false,
    openDept: false,
    startLoading() {
        window.dispatchEvent(new CustomEvent('island-notif', { 
            detail: { message: 'Consultando Estadísticas...', type: 'info' } 
        }));
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 tracking-tight">
                    Reporte <span class="text-oro">Estadístico</span>
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Estadística concentrada y detallada de un código de incidencia por fechas y áreas.
                </p>
            </div>

            <!-- Controls Box -->
            <div
                class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col xl:flex-row items-stretch xl:items-center gap-2">

                <!-- Codigo Selector -->
                <div class="relative w-full xl:w-[260px]" @click.away="openCode = false">
                    @php
                    $codeFound = collect($codigos)->firstWhere('id', $selectedCode);
                    $selectedCodeName = $codeFound ? '[' . $codeFound->code . '] ' . $codeFound->description : 'Código
                    de Incidencia';
                    @endphp
                    <button @click="openCode = !openCode; openDept = false" type="button"
                        class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-[#13322B] transition-all">
                        <span class="truncate">{{ $selectedCodeName }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openCode}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Dropdown -->
                    <div x-show="openCode" x-transition.opacity
                        class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                        style="display: none;">
                        <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                            @foreach($codigos as $cod)
                            <div wire:click="setCode('{{ $cod->id }}')" @click="openCode = false"
                                class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold {{ $selectedCode == $cod->id ? 'bg-[#13322B]/10 text-[#13322B] dark:bg-oro/20 dark:text-oro' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                [{{ $cod->code }}] {{ $cod->description }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Dept Selector -->
                <div class="relative w-full xl:w-[260px]" @click.away="openDept = false">
                    @php
                    $deptFound = collect($departments)->firstWhere('id', $selectedDepartment);
                    $selectedDepartmentName = $deptFound ? '[' . $deptFound->code . '] ' . $deptFound->description :
                    'Todos los departamentos';
                    @endphp
                    <button @click="openDept = !openDept; openCode = false" type="button"
                        class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-[#13322B] transition-all">
                        <span class="truncate">{{ $selectedDepartmentName }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openDept}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Dropdown -->
                    <div x-show="openDept" x-transition.opacity
                        class="absolute z-50 w-full xl:w-[350px] right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                        style="display: none;">
                        <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                            <div wire:click="setDepartment('')" @click="openDept = false"
                                class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold {{ !$selectedDepartment ? 'bg-[#13322B]/10 text-[#13322B] dark:bg-oro/20 dark:text-oro' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                Todos los departamentos
                            </div>
                            @foreach($departments as $dept)
                            <div wire:click="setDepartment('{{ $dept->id }}')" @click="openDept = false"
                                class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold {{ $selectedDepartment == $dept->id ? 'bg-[#13322B]/10 text-[#13322B] dark:bg-oro/20 dark:text-oro' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                <span class="truncate block">[{{ $dept->code }}] {{ $dept->description }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Date Pickers -->
                <div class="flex items-center gap-2">
                    <div wire:ignore class="relative" x-data="{ 
                                flatpickrInstance: null,
                                init() { 
                                    this.flatpickrInstance = window.flatpickr(this.$refs.input, { 
                                        dateFormat: 'd/m/Y', 
                                        defaultDate: '{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}', 
                                        onChange: (selectedDates, dateStr, instance) => { 
                                            if(selectedDates.length > 0) { 
                                                $wire.set('fechaInicio', instance.formatDate(selectedDates[0], 'Y-m-d')) 
                                            }
                                        } 
                                    }); 
                                } 
                            }">
                        <input type="text" x-ref="input"
                            class="h-[42px] w-[110px] sm:w-[130px] px-3 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-[#13322B] border cursor-pointer text-center"
                            placeholder="Inicio">
                    </div>
                    <span class="text-gray-400 font-bold">-</span>
                    <div wire:ignore class="relative" x-data="{ 
                                flatpickrInstance: null,
                                init() { 
                                    this.flatpickrInstance = window.flatpickr(this.$refs.input, { 
                                        dateFormat: 'd/m/Y', 
                                        defaultDate: '{{ \Carbon\Carbon::parse($fechaFinal)->format('d/m/Y') }}', 
                                        onChange: (selectedDates, dateStr, instance) => { 
                                            if(selectedDates.length > 0) { 
                                                $wire.set('fechaFinal', instance.formatDate(selectedDates[0], 'Y-m-d')) 
                                            }
                                        } 
                                    }); 
                                } 
                            }">
                        <input type="text" x-ref="input"
                            class="h-[42px] w-[110px] sm:w-[130px] px-3 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-[#13322B] border cursor-pointer text-center"
                            placeholder="Fin">
                    </div>
                </div>

                <!-- Build Button -->
                <button wire:click="loadData" @click="startLoading()" wire:loading.attr="disabled"
                    class="h-[42px] px-5 bg-[#13322B] hover:bg-[#0a1b17] disabled:opacity-50 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-colors flex items-center justify-center min-w-[120px]">
                    <span wire:loading.remove wire:target="loadData">Generar</span>
                    <span wire:loading wire:target="loadData" class="flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </button>
            </div>
            @error('selectedCode') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span>
            @enderror
            @error('fechaFinal') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        @if($this->reportData)

        <!-- Global Dashboard Summary -->
        <div
            class="mb-8 p-6 bg-gradient-to-br from-[#13322B] to-[#1e463d] rounded-3xl shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex-1">
                <div class="text-oro font-black text-xs uppercase tracking-[0.2em] mb-1">CONCENTRADO DE ESTADÍSTICA
                </div>
                <div class="text-white text-2xl font-bold font-mono">
                    {{ $codeFound ? $codeFound->code . ' - ' . $codeFound->description : '' }}
                </div>
            </div>

            <div
                class="relative z-10 shrink-0 bg-white/10 px-8 py-4 rounded-2xl backdrop-blur-sm text-center border border-white/10">
                <div class="text-white/60 text-[10px] font-black uppercase tracking-widest mb-1">Total Periodo</div>
                <div class="text-5xl font-black text-[#e6d194] leading-none">{{ $this->reportData['totalDays'] ?? 0 }}
                    <span class="text-lg">días</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Left Column: Jornadas Stats -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3
                    class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Días por Jornada
                </h3>

                <div class="space-y-3">
                    @forelse($this->reportData['statsByJornada'] ?? [] as $jornada => $days)
                    <div
                        class="p-3 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-between border border-gray-100 dark:border-gray-800">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $jornada }}</span>
                        <span
                            class="text-lg font-black text-[#13322B] dark:text-[#e6d194] bg-white dark:bg-gray-800 px-3 py-1 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">{{
                            $days }}</span>
                    </div>
                    @empty
                    <div class="text-center py-6 text-xs font-bold text-gray-400 uppercase">Sin resultados.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Detail Table -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest">
                        Detalle de Incidencias Registradas
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                <th class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Empleado</th>
                                <th
                                    class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Fechas</th>
                                <th
                                    class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Jornada</th>
                                <th
                                    class="px-5 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                    Días</th </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50" x-data="{ expandedId: null }">
                            @forelse($this->reportData['groupedByEmployee'] ?? [] as $empId => $data)
                            <!-- Fila Principal (Agrupada) -->
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/80 transition-colors cursor-pointer group"
                                @click="expandedId === '{{ $empId }}' ? expandedId = null : expandedId = '{{ $empId }}'">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <!-- Chevron animado -->
                                        <div class="text-gray-300 dark:text-gray-600 transition-transform duration-300"
                                            :class="{ 'rotate-90': expandedId === '{{ $empId }}' }">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div
                                                class="text-xs font-bold text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                                {{ $data['employee']->father_lastname }} {{
                                                $data['employee']->mother_lastname }} {{ $data['employee']->name }}
                                            </div>
                                            <div
                                                class="text-[9px] font-semibold text-gray-400 flex items-center gap-1 mt-0.5">
                                                <span>#{{ $data['employee']->num_empleado }}</span> &bull;
                                                <span class="truncate max-w-[150px] inline-block">{{
                                                    $data['employee']->puesto->puesto ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="text-[10px] font-bold text-gray-400">
                                        {{ count($data['details']) }} Registro(s)
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="text-xs font-bold text-gray-500">{{ $data['employee']->jornada->jornada
                                        ?? 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="text-sm font-black text-[#9b2247] dark:text-[#e6d194]">{{
                                        $data['total_dias'] }}</div>
                                </td>
                            </tr>

                            <!-- Detalles (Desplegable) -->
                            <tr x-show="expandedId === '{{ $empId }}'" x-collapse style="display: none;">
                                <td colspan="4" class="px-0 py-0">
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-900/50 border-t border-b border-gray-100 dark:border-gray-800 p-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($data['details'] as $inc)
                                            <div
                                                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-3 flex justify-between items-center shadow-sm">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-0.5">Periodo</span>
                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                        {{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/y') }}
                                                        &rarr; {{
                                                        \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/y') }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span
                                                        class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-0.5">Tomó</span>
                                                    <span class="text-xs font-black text-oro">
                                                        {{ $inc->total_dias }} {{ $inc->total_dias == 1 ? 'Día' : 'Días'
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-gray-400">
                                    No se encontraron incidencias en los parámetros establecidos.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @else
        <!-- Placeholder -->
        <div
            class="bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center py-24">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="text-sm font-black uppercase text-gray-400 tracking-widest">Listo para procesar</h3>
            <p class="text-xs font-semibold text-gray-400 mt-1 max-w-sm text-center">Selecciona un código de incidencia
                y fechas para consultar las estadísticas.</p>
        </div>
        @endif
    </div>
</div>