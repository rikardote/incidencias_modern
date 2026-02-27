<x-slot name="header">
    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
        {{ __('Reporte General de Incidencias (RH5)') }}
    </h2>
</x-slot>

<div>

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

                        <div class="min-w-[200px] w-auto">
                            <label
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">QUINCENA</label>
                            <select wire:model="qnaId"
                                class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                                <option value="">Seleccione Quincena</option>
                                @foreach($qnas as $qna)
                                <option value="{{ $qna->id }}">Qna {{ str_pad($qna->qna, 2, '0', STR_PAD_LEFT) }} ({{
                                    $qna->description }})</option>
                                @endforeach
                            </select>
                            @error('qnaId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex-1 min-w-[300px]">
                            <label
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">DEPARTAMENTO /
                                CENTRO</label>
                            <select wire:model="departmentId"
                                class="block w-full py-1.5 pl-3 pr-8 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                                <option value="">Seleccione Centro de Trabajo</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->description }}</option>
                                @endforeach
                            </select>
                            @error('departmentId') <span class="text-red-500 text-[10px]">{{ $message }}</span>
                            @enderror
                        </div>

                                    <button wire:click="generate" 
                                            @click="$dispatch('island-notif', { message: 'Generando Reporte...', type: 'info' })"
                                            wire:loading.attr="disabled"
                                            class="bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-1.5 rounded text-sm font-bold uppercase tracking-wider transition whitespace-nowrap disabled:opacity-50 disabled:cursor-wait">
                                        Consultar
                                    </button>
                        </div>
                    </div>

                    @if($results !== null)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold uppercase tracking-wider text-oro">Vista Previa</h3>
                            <a href="{{ route('reports.rh5.pdf', ['qnaId' => $qnaId, 'departmentId' => $departmentId]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 bg-[#9b2247] hover:bg-[#7a1b38] text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wider transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Descargar PDF (RH5)
                            </a>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Num</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Empleado
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Código</th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Desde</th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Hasta</th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">
                                            Periodo</th>
                                        <th
                                            class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center text-oro">
                                            Días</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">
                                    @forelse($results as $num => $data)
                                    @foreach($data['items'] as $index => $item)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors {{ $index === 0 ? 'border-t-[1.5px] border-gray-400 dark:border-gray-500' : 'border-t-[0.5px] border-gray-200 dark:border-gray-700' }}">
                                        <td
                                            class="px-4 py-3 font-mono text-xs text-center text-gray-900 dark:text-gray-100">
                                            {{ $index === 0 ? $num : '' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($index === 0)
                                            <div class="font-medium text-[13px] text-gray-900 dark:text-gray-100 mb-1">
                                                {{ $data['name'] }}
                                            </div>
                                            @endif

                                            @if($item['otorgado']) <div
                                                class="text-[10px] font-normal text-gray-500 dark:text-gray-400 uppercase mt-0.5">
                                                {{ $item['otorgado'] }}</div> @endif
                                            @if($item['becas_comments']) <div
                                                class="text-[10px] font-normal text-gray-500 dark:text-gray-400 uppercase mt-0.5">
                                                {{ $item['becas_comments'] }}</div> @endif
                                            @if($item['horas_otorgadas']) <div
                                                class="text-[10px] font-normal text-gray-500 dark:text-gray-400 uppercase mt-0.5">
                                                {{ $item['horas_otorgadas'] }}</div> @endif
                                            @if($item['code'] == 900 && $item['autoriza_txt']) <div
                                                class="text-[10px] font-normal text-gray-500 dark:text-gray-400 uppercase mt-0.5">
                                                {{ $item['autoriza_txt'] }}</div> @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-900 dark:text-gray-200">
                                            @if($item['code'] == 901) OT
                                            @elseif($item['code'] == 905) PS
                                            @elseif($item['code'] == 900) TXT
                                            @else {{ str_pad($item['code'], 2, '0', STR_PAD_LEFT) }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-900 dark:text-gray-200">{{
                                            \Carbon\Carbon::parse($item['fecha_inicio'])->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-center text-gray-900 dark:text-gray-200">{{
                                            \Carbon\Carbon::parse($item['fecha_final'])->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-center text-gray-900 dark:text-gray-200">{{
                                            $item['periodo'] ?: '-' }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-oro">{{ $item['total'] }}</td>
                                    </tr>
                                    @endforeach
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">No se
                                            encontraron
                                            incidencias para los criterios seleccionados.</td>
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