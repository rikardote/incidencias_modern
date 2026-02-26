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
                        <select wire:model.live="year"
                            class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                            <option value="">Año</option>
                            @foreach($years as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                        @error('year') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="min-w-[150px] w-auto">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">MES</label>
                        <select wire:model.live="month"
                            class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                            <option value="">Mes</option>
                            @foreach($months as $num => $mes)
                            <option value="{{ $num }}">{{ $mes }}</option>
                            @endforeach
                        </select>
                        @error('month') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">DEPARTAMENTO /
                            CENTRO</label>
                        <select wire:model="departmentId"
                            class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                            <option value="">Seleccione Centro de Trabajo</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->description }}</option>
                            @endforeach
                        </select>
                        @error('departmentId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="w-auto shrink-0">
                        <button wire:click="generate"
                            class="bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-1.5 rounded text-sm font-bold uppercase tracking-wider transition whitespace-nowrap">
                            Consultar
                        </button>
                    </div>
                </div>

                @if($results !== null)
                <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold uppercase tracking-wider text-oro">Personal Sin Derecho
                            (Previsualización)</h3>
                        <a href="{{ route('reports.sinderecho.pdf', ['year' => $year, 'month' => $month, 'departmentId' => $departmentId]) }}"
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
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">No. Emp</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Nombre</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Clave del
                                        puesto</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Denominación de
                                        puesto</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800">
                                @forelse($results as $index => $emp)
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors border-t-[0.5px] border-gray-200 dark:border-gray-700">
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
                                        <button wire:click="showDetails({{ $emp->id }})"
                                            class="text-oro hover:text-[#9b2247] transition-colors" title="Ver Motivo">
                                            <svg class="w-5 h-5 inline-block" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">No se encontraron
                                        empleados que ameriten pérdida de derecho a nota buena en este periodo.</td>
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
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-white/20">
                {{-- Header --}}
                <div
                    class="px-6 py-5 bg-gradient-to-r from-[#9b2247] to-[#7a1b38] text-white flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md text-[#e6d194]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-widest">
                            Detalle de Incidencias Críticas
                        </h3>
                    </div>
                    <button wire:click="closeModal"
                        class="relative z-10 text-white opacity-60 hover:opacity-100 hover:rotate-90 transition-all duration-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-8 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex flex-col">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Empleado Evaluado
                        </p>
                        <h4 class="font-bold text-[#13322B] dark:text-[#e6d194] text-2xl uppercase tracking-tighter">{{
                            $selectedEmployeeName }}</h4>
                    </div>

                    <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-2xl">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-gray-50/50 dark:bg-gray-800/50 backdrop-blur-sm">
                                <tr>
                                    <th
                                        class="px-5 py-2 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                        Código</th>
                                    <th
                                        class="px-5 py-2 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center">
                                        F. Inicio</th>
                                    <th
                                        class="px-5 py-2 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center">
                                        F. Fin</th>
                                    <th
                                        class="px-5 py-2 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center">
                                        Días</th>
                                    <th
                                        class="px-5 py-2 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                        Observaciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($selectedEmployeeIncidencias as $inc)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-5 py-2">
                                        <span class="font-bold text-gray-900 dark:text-gray-100 text-xs uppercase">
                                            {{ $inc['codigo']['code'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2 text-center text-xs text-gray-600 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($inc['fecha_inicio'])->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-2 text-center text-xs text-gray-600 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($inc['fecha_final'])->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-2 text-center">
                                        <span class="text-[#9b2247] dark:text-[#f87171] font-black text-sm">
                                            {{ $inc['total_dias'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2 text-xs text-gray-500 dark:text-gray-400 leading-snug">
                                        @if(!empty($inc['periodo']))
                                        <span class="font-bold text-[#13322B] dark:text-[#e6d194]">VAC:</span> {{
                                        $inc['periodo']['periodo'] }}-{{ $inc['periodo']['year'] }}<br>
                                        @endif
                                        @if(!empty($inc['num_licencia']))
                                        <span class="font-bold text-[#13322B] dark:text-[#e6d194]">LIC:</span> {{
                                        $inc['num_licencia'] }}<br>
                                        @endif
                                        @if(!empty($inc['diagnostico']))
                                        <span class="italic text-[10px] leading-tight">{{ $inc['diagnostico'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center text-sm text-gray-400 italic font-medium">
                                        No hay registros detallados disponibles.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-8 py-5 bg-gray-50/50 dark:bg-gray-900/50 flex justify-end rounded-b-2xl border-t dark:border-gray-700 backdrop-blur-md">
                    <button wire:click="closeModal" type="button"
                        class="px-10 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-sm">
                        Cerrar Detalle
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>