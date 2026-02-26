<div>
    <div class="mb-6 flex flex-wrap items-end gap-4 bg-white p-6 shadow-sm rounded-xl border border-gray-100">
        <div class="w-32">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Año</label>
            <select wire:model.live="año_seleccionado" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($años as $año)
                    <option value="{{ $año }}">{{ $año }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Quincena</label>
            <select wire:model.live="quincena_seleccionada" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($quincenas as $q)
                    <option value="{{ $q['value'] }}">{{ $q['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[300px]">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-building mr-1 text-indigo-500"></i> Centro de Trabajo
            </label>
            <select wire:model.live="centro_seleccionado" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Seleccione un centro...</option>
                @foreach($centros as $centro)
                    <option value="{{ $centro->id }}">{{ $centro->code }} - {{ $centro->description }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <button wire:click="exportPdf" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-md flex items-center transition-all duration-200">
                <i class="far fa-file-pdf mr-2"></i> Exportar
            </button>
        </div>
    </div>

    @if($this->centro_seleccionado)
        <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-6">
            @forelse($empleados as $num_empleado => $registrosEmpleado)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                    <div class="bg-slate-50 p-4 border-b border-gray-200 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="bg-indigo-100 text-indigo-700 w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs">
                                {{ $num_empleado }}
                            </div>
                            <div>
                                <a href="{{ route('employees.incidencias', ['employeeId' => $registrosEmpleado->first()->employee_id]) }}" target="_blank" class="block hover:text-indigo-600 transition-colors">
                                    <h3 class="font-bold text-gray-800 text-sm leading-tight">
                                        {{ strtoupper($registrosEmpleado->first()->apellido_paterno) }} {{ strtoupper($registrosEmpleado->first()->apellido_materno) }}
                                        <br>
                                        <span class="font-medium text-gray-600 uppercase">{{ $registrosEmpleado->first()->nombre }}</span>
                                    </h3>
                                </a>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider">Horario</span>
                            <span class="text-xs font-semibold bg-white px-2 py-1 rounded border border-gray-200 text-gray-700">
                                {{ $registrosEmpleado->first()->horario_entrada ? substr($registrosEmpleado->first()->horario_entrada, 0, 5) . ' - ' . substr($registrosEmpleado->first()->horario_salida, 0, 5) : $registrosEmpleado->first()->horario }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-grow overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-xs">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">Fecha</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">Entrada</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">Salida</th>
                                    <th class="px-3 py-2 text-center text-[10px] font-bold text-gray-500 uppercase">
                                        Inc
                                        <i class="fas fa-info-circle ml-1 cursor-help" title="Click en la fila para capturar incidencia"></i>
                                    </th>
                                </tr>
                            </thead>
                            <body class="bg-white divide-y divide-gray-100">
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
                                        if ($registro->retardo && $registro->incidencia != '7') $rowClass = 'bg-red-50/50';
                                        if ($registro->incidencia && !in_array($registro->incidencia, $incidenciasSinColor)) $rowClass = 'bg-amber-50/50';
                                        
                                        $isWeekend = \Carbon\Carbon::parse($registro->fecha)->isWeekend();
                                        if ($isWeekend && empty($rowClass)) $rowClass = 'bg-slate-50/70 opacity-80';
                                    @endphp

                                    <tr 
                                        wire:click="openCaptureModal({{ $registro->employee_id }}, {{ $num_empleado }}, '{{ $registro->apellido_paterno }} {{ $registro->apellido_materno }} {{ $registro->nombre }}', '{{ $registro->fecha }}')"
                                        class="{{ $rowClass }} hover:bg-slate-100 transition-colors cursor-pointer group"
                                    >
                                        <td class="px-3 py-1.5 font-medium text-gray-500 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}
                                        </td>
        
                                        <td class="px-3 py-1.5 whitespace-nowrap">
                                            @if($esJornadaVespertina && $esLaPrimeraChecadaDelDia && $registro->hora_entrada)
                                                <div class="flex items-center">
                                                    <span class="{{ $registro->retardo && $registro->incidencia != '7' ? 'text-rose-600 font-bold' : 'text-gray-700' }}">
                                                        {{ substr($registro->hora_entrada, 0, 5) }}
                                                    </span>
                                                    @if($registro->retardo && $registro->incidencia != '7')
                                                        <span class="ml-1 bg-rose-500 text-white text-[8px] px-1 rounded">R</span>
                                                    @endif
                                                </div>
                                            @elseif($mostrarOmisionEntrada)
                                                <span class="text-rose-500 font-bold" title="Omisión de entrada">──:──</span>
                                            @elseif($registro->hora_entrada)
                                                <div class="flex items-center">
                                                    <span class="{{ $registro->retardo && $registro->incidencia != '7' ? 'text-rose-600 font-bold' : 'text-gray-700' }}">
                                                        {{ substr($registro->hora_entrada, 0, 5) }}
                                                    </span>
                                                    @if($registro->retardo && $registro->incidencia != '7')
                                                        <span class="ml-1 bg-rose-500 text-white text-[8px] px-1 rounded">R</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap">
                                            @if(($estaChecadaDespuesMedioDia && $esSalidaPorHorario) || ($registro->hora_salida && !$tieneUnaSolaChecada))
                                                <span class="text-gray-700">{{ substr($registro->hora_salida, 0, 5) }}</span>
                                            @elseif(($tieneUnaSolaChecada || ($registro->hora_entrada && !$registro->hora_salida)) && $jornadaTerminada)
                                                <span class="text-rose-400 opacity-60 italic text-[10px]">Sin salida</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-1.5 text-center">
                                            @if($registro->incidencia)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ in_array($registro->incidencia, $incidenciasSinColor) ? 'bg-gray-100 text-gray-500' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ $registro->incidencia }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </body>
                        </table>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 text-center rounded-xl border border-gray-200 shadow-sm text-gray-500">
                    <i class="fas fa-search fa-3x mb-4 text-gray-200"></i>
                    <p>No se encontraron registros para los filtros seleccionados.</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="bg-indigo-50 border border-indigo-100 p-8 text-center rounded-xl text-indigo-700">
            <i class="fas fa-arrow-up mb-4 opacity-50 block text-2xl"></i>
            <p class="font-medium">Seleccione un centro de trabajo para ver los registros biométricos.</p>
        </div>
    @endif

    <!-- Modal de Captura de Incidencias -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:min-h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">
                    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold">Capturar Incidencia</h3>
                        <button wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4 mb-6">
                            <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                                <p class="text-[10px] text-indigo-700 font-bold uppercase tracking-wider mb-1">{{ $selectedEmployeeNumEmpleado }}</p>
                                <p class="text-sm font-bold text-gray-800">{{ $selectedEmployeeName }}</p>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-lg border border-gray-100">
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Fecha a Reportar</p>
                                <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</p>
                                <input type="hidden" wire:model="fecha_inicio_inc">
                                <input type="hidden" wire:model="fecha_fin_inc">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Tipo de Incidencia / Justificación</label>
                            <select wire:model="incidencia_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Seleccione incidencia...</option>
                                @foreach($codigos as $cod)
                                    <option value="{{ $cod->id }}">{{ $cod->code }} - {{ $cod->description }}</option>
                                @endforeach
                            </select>
                            @error('incidencia_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3">
                            <button wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancelar
                            </button>
                            <button wire:click="saveIncidencia" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors flex items-center shadow-lg shadow-indigo-200">
                                <i class="fas fa-save mr-2"></i> Guardar Incidencia
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
