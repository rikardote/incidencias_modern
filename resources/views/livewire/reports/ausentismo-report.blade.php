<div class="py-12" x-data="{ openDept: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Reporte
                    de <span class="text-oro">Ausentismo</span></h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Resumen administrativo de inasistencias.</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col xl:flex-row items-stretch xl:items-center gap-2">
                <div class="relative w-full xl:w-[280px]" @click.away="openDept = false">
                    <button @click="openDept = !openDept" type="button"
                        class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                        <span class="truncate">{{ $this->selectedDeptLabel }}</span>
                        <svg class="w-4 h-4 text-gray-400" :class="{'rotate-180': openDept}" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openDept"
                        class="absolute z-50 w-full xl:w-[350px] right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                        style="display: none;">
                        <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                            <div wire:click="setDepartment('')" @click="openDept = false"
                                class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700">
                                Todos los departamentos</div>
                            @foreach($departments as $dept)
                            <div wire:click="setDepartment('{{ $dept->id }}')" @click="openDept = false"
                                class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700">
                                [{{ $dept->code }}] {{ $dept->description }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
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
                            class="h-[42px] w-[130px] px-3 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-[#13322B] cursor-pointer text-center"
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
                            class="h-[42px] w-[130px] px-3 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-[#13322B] cursor-pointer text-center"
                            placeholder="Fin">
                    </div>
                </div>
                <button wire:click="loadData"
                    class="h-[42px] px-5 bg-[#13322B] hover:bg-[#0a1b17] text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-colors flex items-center justify-center min-w-[120px]">
                    <span wire:loading.remove wire:target="loadData">Generar</span>
                    <span wire:loading wire:target="loadData">
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
        </div>

        @if($this->reportData)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div
                class="bg-gradient-to-br from-[#13322B] to-[#0a1b17] p-6 rounded-3xl shadow-lg relative overflow-hidden">
                <div class="relative z-10 font-black text-white">
                    <div class="text-[10px] uppercase tracking-widest text-oro mb-1 opacity-80">Total Días</div>
                    <div class="text-4xl leading-tight">{{ $this->reportData['totalDays'] }} <span
                            class="text-xs uppercase opacity-60">Días</span></div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-gray-400 font-black text-[10px] uppercase tracking-widest mb-1">Periodo Actual</div>
                <div class="text-sm font-black text-gray-600 dark:text-gray-300 mt-3">{{ $fechaInicio }} &rarr; {{
                    $fechaFinal }}</div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-gray-400 font-black text-[10px] uppercase tracking-widest mb-1">Departamento</div>
                <div class="text-xs font-black text-gray-600 dark:text-gray-300 mt-3 truncate">{{
                    $this->selectedDeptLabel }}</div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
            <div
                class="p-6 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center text-xs font-black uppercase tracking-widest">
                <span>Vista Institucional</span>
                <button onclick="window.print()" class="text-oro hover:underline">Imprimir</button>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="w-full border-collapse border border-gray-200 dark:border-gray-700 text-center">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            @foreach($this->reportData['classicStats'] as $code => $val)
                            <td class="border border-gray-200 dark:border-gray-700 px-1 py-2 text-[9px] font-black">{{
                                $code }}</td>
                            @endforeach
                            <td
                                class="border border-gray-200 dark:border-gray-700 px-1 py-1 text-[9px] font-black bg-oro/20">
                                TOTAL</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($this->reportData['classicStats'] as $code => $val)
                            <td
                                class="border border-gray-200 dark:border-gray-700 px-1 py-3 text-[11px] font-bold {{ $val > 0 ? 'text-[#9B2247] dark:text-oro' : 'text-gray-300' }}">
                                {{ $val }}</td>
                            @endforeach
                            <td
                                class="border border-gray-200 dark:border-gray-700 px-1 py-3 text-[11px] font-black bg-oro/10">
                                {{ $this->reportData['totalClassicStats'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-12">
            <div class="p-6 border-b border-gray-50 dark:border-gray-700 text-xs font-black uppercase tracking-widest">
                Desglose de Impacto</div>
            <div class="p-8 space-y-6">
                @foreach($this->reportData['chartData'] as $bar)
                @if($bar['value'] > 0)
                <div class="group">
                    <div class="flex justify-between mb-1 text-[10px] font-bold text-gray-500 uppercase">
                        <span>[{{ $bar['code'] }}] {{ $bar['description'] }}</span>
                        <span class="text-[#13322B] dark:text-oro">{{ $bar['value'] }} días</span>
                    </div>
                    <div class="w-full h-3 bg-gray-50 dark:bg-gray-900 rounded-full overflow-hidden">
                        <div class="h-full bg-oro" style="width:{{ $bar['percentage'] }}%"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @else
        <div
            class="bg-gray-50 dark:bg-gray-800/50 rounded-[40px] border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center py-32 opacity-80">
            <h3 class="text-sm font-black uppercase text-gray-500">Monitor de Ausentismo</h3>
            <p class="text-xs font-semibold text-gray-400 mt-2">Usa los filtros superiores para generar el reporte.</p>
        </div>
        @endif
    </div>
    <style>
        @media print {

            nav,
            button,
            .mb-8,
            .grid,
            .mb-12 {
                display: none !important;
            }
        }
    </style>
</div>