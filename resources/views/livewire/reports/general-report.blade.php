<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Reporte General de Incidencias (RH5)
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quincena</label>
                        <select wire:model="qnaId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            <option value="">Seleccione una Quincena</option>
                            @foreach($qnas as $qna)
                                <option value="{{ $qna->id }}">{{ $qna->year }} - Qna {{ str_pad($qna->qna, 2, '0', STR_PAD_LEFT) }} ({{ $qna->description }})</option>
                            @endforeach
                        </select>
                        @error('qnaId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departamento / Centro</label>
                        <select wire:model="departmentId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                            <option value="">Seleccione un Centro</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->description }}</option>
                            @endforeach
                        </select>
                        @error('departmentId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-end text-sm">
                        <button wire:click="generate" class="w-full bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-2 rounded font-bold uppercase tracking-wider transition">
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
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                </svg>
                                Descargar PDF (RH5)
                            </a>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Num</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Empleado</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">Código</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">Desde</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">Hasta</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">Periodo</th>
                                        <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center text-oro">Días</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($results as $num => $data)
                                        @foreach($data['items'] as $index => $item)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                                @if($index === 0)
                                                    <td class="px-4 py-3 font-mono text-xs" rowspan="{{ count($data['items']) }}">{{ $num }}</td>
                                                    <td class="px-4 py-3 font-medium" rowspan="{{ count($data['items']) }}">
                                                        {{ $data['name'] }}
                                                        @foreach($data['items'] as $it)
                                                            @if($it['otorgado']) <div class="text-[10px] font-bold text-gray-500 mt-1 uppercase">{{ $it['otorgado'] }}</div> @endif
                                                            @if($it['becas_comments']) <div class="text-[10px] font-bold text-gray-500 mt-1 uppercase">{{ $it['becas_comments'] }}</div> @endif
                                                            @if($it['horas_otorgadas']) <div class="text-[10px] font-bold text-gray-500 mt-1 uppercase">{{ $it['horas_otorgadas'] }}</div> @endif
                                                            @if($it['code'] == 900 && $it['autoriza_txt']) <div class="text-[10px] font-bold text-gray-500 mt-1 uppercase">{{ $it['autoriza_txt'] }}</div> @endif
                                                        @endforeach
                                                    </td>
                                                @endif
                                                <td class="px-4 py-3 text-center font-mono">{{ str_pad($item['code'], 2, '0', STR_PAD_LEFT) }}</td>
                                                <td class="px-4 py-3 text-center">{{ \Carbon\Carbon::parse($item['fecha_inicio'])->format('d/m/Y') }}</td>
                                                <td class="px-4 py-3 text-center">{{ \Carbon\Carbon::parse($item['fecha_final'])->format('d/m/Y') }}</td>
                                                <td class="px-4 py-3 text-center">{{ $item['periodo'] }}</td>
                                                <td class="px-4 py-3 text-center font-bold text-oro">{{ $item['total'] }}</td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">No se encontraron incidencias para los criterios seleccionados.</td>
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
