    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Reporte Sin Derecho a Nota Buena por Desempeño') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                <div class="flex items-end gap-3 mb-6 w-full">
                    <div class="min-w-[100px] w-auto">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">AÑO</label>
                        <select wire:model.live="year" class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                            <option value="">Año</option>
                            @foreach($years as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                        @error('year') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="min-w-[150px] w-auto">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">MES</label>
                        <select wire:model.live="month" class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                            <option value="">Mes</option>
                            @foreach($months as $num => $mes)
                                <option value="{{ $num }}">{{ $mes }}</option>
                            @endforeach
                        </select>
                        @error('month') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">DEPARTAMENTO / CENTRO</label>
                        <select wire:model="departmentId" class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                            <option value="">Seleccione Centro de Trabajo</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->description }}</option>
                            @endforeach
                        </select>
                        @error('departmentId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="w-auto shrink-0">
                        <button wire:click="generate" class="bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-1.5 rounded text-sm font-bold uppercase tracking-wider transition whitespace-nowrap">
                            Consultar
                        </button>
                    </div>
                </div>

                @if($results !== null)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold uppercase tracking-wider text-oro">Personal Sin Derecho (Previsualización)</h3>
                            <a href="{{ route('reports.sinderecho.pdf', ['year' => $year, 'month' => $month, 'departmentId' => $departmentId]) }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 bg-[#9b2247] hover:bg-[#7a1b38] text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wider transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                </svg>
                                Descargar PDF
                            </a>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">No. Emp</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Nombre</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Clave del puesto</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Denominación de puesto</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">
                                    @forelse($results as $index => $emp)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors border-t-[0.5px] border-gray-200 dark:border-gray-700">
                                            <td class="px-4 py-3 font-bold text-[#9b2247] dark:text-[#e6d194]">
                                                {{ $emp->num_empleado }}
                                            </td>
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100 uppercase">
                                                {{ $emp->name }} {{ $emp->father_lastname }} {{ $emp->mother_lastname }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300 uppercase">
                                                {{ $emp->puesto->clave ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300 uppercase">
                                                {{ $emp->puesto->puesto ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button wire:click="showDetails({{ $emp->id }})" class="text-oro hover:text-[#9b2247] transition-colors" title="Ver Motivo">
                                                    <svg class="w-5 h-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">No se encontraron empleados que ameriten pérdida de derecho a nota buena en este periodo.</td>
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

    {{-- Modal Detalle de Incidencias --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full border border-gray-200 dark:border-gray-700">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center relative">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                        Motivo de Pérdida de Derecho a Nota Buena
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition-colors">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="px-4 pt-5 pb-4 sm:p-6 bg-white dark:bg-gray-900">
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Empleado:</p>
                        <p class="font-bold text-[#13322B] dark:text-[#e6d194] text-lg uppercase">{{ $selectedEmployeeName }}</p>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded mb-4">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-300">Código</th>
                                    <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-300 text-center">Inició</th>
                                    <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-300 text-center">Terminó</th>
                                    <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-300 text-center">Días</th>
                                    <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-300">Descripción Extra</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($selectedEmployeeIncidencias as $inc)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-200">
                                        {{ $inc['codigo']['code'] }} - {{ $inc['codigo']['description'] ?? '' }}
                                    </td>
                                    <td class="px-3 py-2 text-center text-gray-900 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($inc['fecha_inicio'])->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-2 text-center text-gray-900 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($inc['fecha_final'])->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-2 text-center text-oro font-bold">
                                        {{ $inc['total_dias'] }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">
                                        @if(!empty($inc['periodo']))
                                            VAC {{ $inc['periodo']['periodo'] }}-{{ $inc['periodo']['year'] }}<br>
                                        @endif
                                        @if(!empty($inc['num_licencia'])) LIC: {{ $inc['num_licencia'] }}<br>@endif
                                        @if(!empty($inc['diagnostico'])) {{ $inc['diagnostico'] }}@endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-sm text-gray-500">No hay incidencias cargadas en la memoria.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

