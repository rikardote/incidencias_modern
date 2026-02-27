<x-slot name="header">
    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
        {{ __('Reporte Kardex por Empleado') }}
    </h2>
</x-slot>

<div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row items-start md:items-end gap-3 mb-6 w-full">
                        <div class="flex-1 w-full min-w-[250px] relative" wire:ignore x-data="{
                            query: '',
                            results: [],
                            open: false,
                            loading: false,
                            debounceTimer: null,
                            search() {
                                clearTimeout(this.debounceTimer);
                                if (this.query.length < 2) { this.results = []; this.open = false; return; }
                                this.loading = true;
                                this.debounceTimer = setTimeout(() => {
                                    fetch('/api/employees/search?q=' + encodeURIComponent(this.query), {
                                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                    })
                                    .then(r => r.json())
                                    .then(data => { this.results = data; this.open = data.length > 0; this.loading = false; })
                                    .catch(() => { this.loading = false; });
                                }, 300);
                            },
                            select(emp) {
                                this.open = false;
                                this.query = emp.label;
                                $wire.cambiarEmpleado(emp.id);
                            }
                         }" x-on:click.outside="open = false">
                            <label
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                BUSCAR EMPLEADO
                            </label>
                            <div class="relative">
                                <input type="text" x-model="query" x-on:input="search()"
                                    x-on:focus="if(query.length >= 2) open = true"
                                    placeholder="Nombre o No. de Empleado..." spellcheck="false"
                                    class="block w-full py-1.5 px-3 pr-10 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm font-bold uppercase transition">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                    <span x-show="loading" class="text-oro text-[10px] animate-pulse mr-1">...</span>
                                    <button x-show="query.length > 0"
                                        x-on:click="query = ''; results = []; open = false; $wire.cambiarEmpleado(null);"
                                        type="button"
                                        class="text-gray-300 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none transition-colors mt-0.5">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition
                                    class="absolute z-50 left-0 top-full mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-2xl max-h-56 overflow-y-auto">
                                    <template x-for="emp in results" :key="emp.id">
                                        <div x-on:click="select(emp)" x-text="emp.label"
                                            class="px-4 py-2 text-xs cursor-pointer hover:bg-guinda hover:text-white border-b border-gray-50 last:border-0 font-medium transition-colors">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="min-w-[140px] w-auto">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">FECHA
                                INICIO</label>
                            <div class="relative mt-1" wire:ignore
                                x-data="{ init() { window.flatpickr(this.$refs.input, { dateFormat: 'd/m/Y', defaultDate: '{{ $fecha_inicio ? \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') : '' }}', onChange: (selectedDates, dateStr, instance) => { if(selectedDates.length > 0) { $wire.set('fecha_inicio', instance.formatDate(selectedDates[0], 'Y-m-d')) } } }); } }">
                                <input type="text" x-ref="input"
                                    class="block w-full py-1.5 px-3 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm transition cursor-pointer text-center"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            @error('fecha_inicio') <span class="text-red-500 text-[10px]">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="min-w-[140px] w-auto">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">FECHA
                                FINAL</label>
                            <div class="relative mt-1" wire:ignore
                                x-data="{ init() { window.flatpickr(this.$refs.input, { dateFormat: 'd/m/Y', defaultDate: '{{ $fecha_final ? \Carbon\Carbon::parse($fecha_final)->format('d/m/Y') : '' }}', onChange: (selectedDates, dateStr, instance) => { if(selectedDates.length > 0) { $wire.set('fecha_final', instance.formatDate(selectedDates[0], 'Y-m-d')) } } }); } }">
                                <input type="text" x-ref="input"
                                    class="block w-full py-1.5 px-3 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm transition cursor-pointer text-center"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            @error('fecha_final') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-2 w-auto shrink-0 mt-3 md:mt-0 items-end">
                            <button wire:click="generate" 
                                @click="$dispatch('island-notif', { message: 'Generando Reporte...', type: 'info' })"
                                wire:loading.attr="disabled" @if(!$employee) disabled @endif
                                class="bg-[#13322B] hover:bg-[#0a1f1a] disabled:bg-gray-400 disabled:cursor-wait text-white px-6 py-1.5 rounded text-sm font-bold uppercase tracking-wider transition whitespace-nowrap h-[34px] mt-auto">
                                Consultar
                            </button>

                            <button wire:click="generateAll" 
                                @click="$dispatch('island-notif', { message: 'Generando Reporte...', type: 'info' })"
                                wire:loading.attr="disabled" @if(!$employee) disabled @endif
                                class="bg-oro hover:bg-yellow-600 disabled:bg-gray-400 disabled:cursor-wait text-white px-4 py-1.5 rounded text-sm font-bold uppercase tracking-wider transition whitespace-nowrap h-[34px] mt-auto"
                                title="Cargar y consultar todo el historial del empleado">
                                Todo el Historial
                            </button>
                        </div>
                    </div>

                    @if($results !== null)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold uppercase tracking-wider text-oro">Historial de Incidencias
                            </h3>
                            <a href="{{ route('reports.kardex.pdf', ['num_empleado' => $employee->num_empleado, 'fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 bg-[#9b2247] hover:bg-[#7a1b38] text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wider transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Descargar PDF
                            </a>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                    <tr>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Código</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Comentario
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Fecha Inicio</th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Fecha Fin</th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Días</th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Periodo</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">
                                    @forelse($results as $inc)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors border-t-[0.5px] border-gray-200 dark:border-gray-700 group">
                                        <td
                                            class="px-4 py-3 font-bold text-[#13322B] dark:text-[#e6d194] text-center w-16 whitespace-nowrap">
                                            {{ str_pad($inc->codigo->code, 2, "0", STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300 uppercase">
                                            {{ $inc->otorgado ?? '' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-900 dark:text-gray-200">
                                            {{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-900 dark:text-gray-200">
                                            {{ \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-[#9b2247] rounded-full">
                                                {{ $inc->total_dias }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 text-center uppercase font-bold">
                                            @if($inc->periodo)
                                            {{ $inc->periodo->periodo }}/{{ $inc->periodo->year }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">No se
                                            encontraron
                                            incidencias grabadas para este empleado en el rango de fechas seleccionado.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>