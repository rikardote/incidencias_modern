<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen" x-data="{ 
        tempEmpId: '', 
        tempName: '', 
        tempDate: '',
        openYear: false,
        openQna: false,
        openDept: false,
        searchDept: '',
        openModal(employeeId, numEmpleado, name, date, displayDate) {
            this.tempEmpId = numEmpleado;
            this.tempName = name;
            this.tempDate = displayDate;
            $wire.isModalOpen = true;
            $wire.openCaptureModal(employeeId, numEmpleado, name, date);
        }
    }">
    {{-- Header del Reporte --}}
    <div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-gray-100 uppercase tracking-tight">Control <span
                    class="text-oro">Biométrico</span></h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Monitoreo y captura de incidencias desde checadas.
            </p>
        </div>

        <div
            class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
            {{-- Año --}}
            <div class="relative w-full lg:w-[120px]" @click.away="openYear = false">
                <button @click="openYear = !openYear" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    <span class="truncate">{{ $año_seleccionado ?? 'Año' }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openYear}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openYear"
                    class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($años as $año)
                        <div wire:click="$set('año_seleccionado', '{{ $año }}')" @click="openYear = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $año_seleccionado == $año ? 'bg-oro/10 text-oro' : '' }}">
                            {{ $año }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Quincena --}}
            <div class="relative w-full lg:w-[220px]" @click.away="openQna = false">
                <button @click="openQna = !openQna" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    @php
                    $qLabel = 'Quincena';
                    foreach($quincenas as $q) if($q['value'] == $quincena_seleccionada) $qLabel = $q['label'];
                    @endphp
                    <span class="truncate">{{ $qLabel }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openQna}" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openQna"
                    class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($quincenas as $q)
                        <div wire:click="$set('quincena_seleccionada', '{{ $q['value'] }}')" @click="openQna = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $quincena_seleccionada == $q['value'] ? 'bg-oro/10 text-oro' : '' }}">
                            {{ $q['label'] }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Centro de Trabajo --}}
            <div class="relative w-full lg:w-[250px]" @click.away="openDept = false; searchDept = ''">
                <button @click="openDept = !openDept" type="button"
                    class="flex items-center justify-between w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200">
                    @php
                    $cLabel = 'Centro de Trabajo';
                    foreach($centros as $c) if($c->id == $centro_seleccionado) $cLabel = $c->code . ' - ' .
                    $c->description;
                    @endphp
                    <span class="truncate">{{ $cLabel }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': openDept}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openDept"
                    class="absolute z-50 w-full lg:w-[350px] right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                    style="display: none;">
                    <div class="p-2 border-b border-gray-50 dark:border-gray-700">
                        <input type="text" x-model="searchDept" placeholder="Buscar centro..."
                            class="w-full px-3 py-1.5 text-[10px] bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-lg outline-none font-bold uppercase">
                    </div>
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                        @foreach($centros as $c)
                        <div wire:click="$set('centro_seleccionado', '{{ $c->id }}')"
                            x-show="'{{ strtolower($c->code . ' ' . $c->description) }}'.includes(searchDept.toLowerCase())"
                            @click="openDept = false"
                            class="px-3 py-2 rounded-lg cursor-pointer text-xs font-bold hover:bg-gray-50 dark:hover:bg-gray-700 {{ $centro_seleccionado == $c->id ? 'bg-oro/10 text-oro' : '' }}">
                            [{{ $c->code }}] {{ $c->description }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button wire:click="exportPdf" wire:loading.attr="disabled"
                class="h-[42px] px-6 bg-[#9b2247] hover:bg-[#7a1b38] text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all flex items-center gap-2">
                <span wire:loading.remove wire:target="exportPdf">Exportar PDF</span>
                <span wire:loading wire:target="exportPdf">...</span>
            </button>
        </div>
    </div>

    {{-- Vista Previa de Registros --}}
    @if($this->centro_seleccionado)
    <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-6">
        @forelse($empleados as $num_empleado => $registrosEmpleado)
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
            {{-- Header de la Card del Empleado (Layout de Expediente Premium) --}}
            <div
                class="bg-white dark:bg-gray-800 p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col justify-between h-40 shrink-0 relative hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-colors">

                {{-- Top: Identidad y Horario --}}
                <div class="flex gap-4 items-start justify-between">
                    {{-- Izquierda: Avatar y Nombre --}}
                    <div class="flex gap-4 items-start min-w-0 flex-1">
                        {{-- Avatar Cuadrado Institucional --}}
                        <div
                            class="w-12 h-12 rounded-xl bg-[#13322B] flex items-center justify-center shrink-0 shadow-md border border-[#1e5b4f]/30">
                            <span class="text-[13px] font-black text-[#e6d194] leading-none tracking-tighter">
                                {{ mb_strtoupper(mb_substr($registrosEmpleado->first()->nombre, 0, 1)) }}{{
                                mb_strtoupper(mb_substr($registrosEmpleado->first()->apellido_paterno, 0, 1)) }}
                            </span>
                        </div>

                        {{-- Nombre Amplio y Flexible --}}
                        <div class="min-w-0 flex-1 pt-0.5">
                            <a href="{{ route('employees.incidencias', ['employeeId' => $registrosEmpleado->first()->employee_id]) }}"
                                target="_blank" class="group block">
                                <h3
                                    class="font-black text-gray-900 dark:text-gray-100 text-[11px] leading-tight uppercase group-hover:text-[#9b2247] transition-colors line-clamp-2">
                                    {{ $registrosEmpleado->first()->apellido_paterno }} {{
                                    $registrosEmpleado->first()->apellido_materno }}
                                </h3>
                                <p
                                    class="text-[11px] font-bold text-[#1e5b4f] uppercase tracking-wider mt-0.5 truncate flex items-center gap-2">
                                    {{ $registrosEmpleado->first()->nombre }}
                                </p>
                                <div class="mt-1.5 flex items-center">
                                    <span
                                        class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-[8px] font-black rounded border border-gray-200 dark:border-gray-600 uppercase tracking-widest">
                                        ID: #{{ $num_empleado }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Derecha: Horario (Top Right) --}}
                    <div class="flex flex-col items-end shrink-0">
                        <div
                            class="flex items-center gap-1.5 px-2 py-1 bg-gray-50 dark:bg-gray-700/30 rounded border border-gray-100 dark:border-gray-600">
                            <svg class="w-3.5 h-3.5 text-[#9b2247]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span
                                class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest whitespace-nowrap">
                                {{ $registrosEmpleado->first()->horario_entrada ?
                                substr($registrosEmpleado->first()->horario_entrada, 0, 5) . ' - ' .
                                substr($registrosEmpleado->first()->horario_salida, 0, 5) :
                                $registrosEmpleado->first()->horario }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Bottom: Acción (Completo) --}}
                <div class="flex justify-end items-end mt-2">
                    {{-- Botón de Acción Limpio (Inferior Derecha) --}}
                    <div class="flex-shrink-0 h-8 flex items-end">
                        @if(!$registrosEmpleado->contains(fn($r) => !empty($r->incidencias)))
                        <button x-data @click.stop="Swal.fire({
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
                            class="group/btn flex items-center gap-1.5 px-3 py-1.5 bg-[#f8fafc] dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm hover:bg-[#1e5b4f] hover:border-[#1e5b4f] text-gray-600 dark:text-gray-300 hover:text-[#e6d194] text-[9px] font-black rounded-lg transition-all uppercase tracking-widest">
                            <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:scale-110 text-gray-400 group-hover/btn:text-[#e6d194]"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Completo</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TABLA DE CHECADAS (Ajustada para reducir espacio en blanco) --}}
            <div class="flex-grow pb-3">
                <table
                    class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-xs table-fixed border-collapse">
                    <thead
                        class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100/50 dark:border-gray-700/50">
                        <tr>
                            <th class="w-[90px] px-3 py-3 text-left">
                                <div
                                    class="flex items-center gap-1.5 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                    <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>Fecha</span>
                                </div>
                            </th>
                            <th class="w-[70px] px-3 py-3 text-left">
                                <div
                                    class="flex items-center gap-1 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                    <svg class="w-3 h-3 text-[#1e5b4f]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14"></path>
                                    </svg>
                                    <span>Ent</span>
                                </div>
                            </th>
                            <th class="w-[70px] px-3 py-3 text-left">
                                <div
                                    class="flex items-center gap-1 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                    <svg class="w-3 h-3 text-[#9b2247]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M13 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                    <span>Sal</span>
                                </div>
                            </th>
                            <th class="px-3 py-3 text-center">
                                <div
                                    class="flex items-center justify-center gap-1 font-black text-gray-400 uppercase tracking-widest text-[9px]">
                                    <span>Inc</span>
                                    <svg class="w-2.5 h-2.5 opacity-30" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($registrosEmpleado as $registro)
                        @php
                        // Lógica de procesamiento de checadas (heredada de legacy)
                        $horarioEntrada = $registrosEmpleado->first()->horario_entrada ?
                        strtotime($registrosEmpleado->first()->horario_entrada) : null;
                        $horarioSalida = $registrosEmpleado->first()->horario_salida ?
                        strtotime($registrosEmpleado->first()->horario_salida) : null;
                        $esJornadaVespertina = $registrosEmpleado->first()->es_jornada_vespertina == 1;

                        $horaMedia = ($esJornadaVespertina && $horarioEntrada && $horarioSalida)
                        ? $horarioEntrada + (($horarioSalida - $horarioEntrada) / 2)
                        : strtotime('12:00:00');

                        $medioDia = $horaMedia;
                        $horaEntrada = $registro->hora_entrada ? strtotime($registro->hora_entrada) : null;
                        $horaSalida = $registro->hora_salida ? strtotime($registro->hora_salida) : null;
                        $tieneUnaSolaChecada = $registro->hora_entrada && $registro->hora_entrada ===
                        $registro->hora_salida;
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
                        $primeraChecadaPostMediodia = $checadasDelDia->first(fn($c) => strtotime($c->hora_entrada) >
                        $medioDia);
                        $esPrimeraChecadaDespuesMediodia = ($primeraChecadaPostMediodia === $registro);
                        $esSalidaPorHorario = !$esPrimeraChecadaDespuesMediodia || $tieneUnaSolaChecada;
                        }

                        $mostrarOmisionEntrada = ($estaChecadaDespuesMedioDia && ($esLaPrimeraChecadaDelDia ||
                        (isset($esPrimeraChecadaDespuesMediodia) && $esPrimeraChecadaDespuesMediodia)));

                        if ($esJornadaVespertina && $esLaPrimeraChecadaDelDia && $horaEntrada) {
                        if (abs($horaEntrada - $horarioEntrada) / 60 <= 90) $mostrarOmisionEntrada=false; }
                            $jornadaTerminada=!\Carbon\Carbon::parse($registro->fecha)->isToday() ||
                            (\Carbon\Carbon::parse($registro->fecha)->isToday() && now()->format('H:i:s') >
                            $registrosEmpleado->first()->horario_salida);

                            $rowClass = '';
                            if ($registro->retardo && strpos($registro->incidencias ?? '', '7') === false) $rowClass =
                            'bg-red-50/50 dark:bg-red-900/20';

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

                            <tr @click="openModal({{ $registro->employee_id }}, {{ $num_empleado }}, '{{ trim(addslashes($registro->apellido_paterno . ' ' . $registro->apellido_materno . ' ' . $registro->nombre)) }}', '{{ $registro->fecha }}', '{{ \Carbon\Carbon::parse($registro->fecha)->translatedFormat('d \d\e F, Y') }}')"
                                class="{{ $rowClass }} hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors cursor-pointer group">
                                <td
                                    class="px-3 py-1.5 font-medium text-gray-500 dark:text-gray-300 whitespace-nowrap {{ $isWeekend ? 'opacity-40' : '' }}">
                                    {{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}
                                </td>

                                <td class="px-3 py-1.5 whitespace-nowrap">
                                    @if($esJornadaVespertina && $esLaPrimeraChecadaDelDia && $registro->hora_entrada)
                                    <div class="flex items-center">
                                        <span
                                            class="{{ $registro->retardo && strpos($registro->incidencias ?? '', '7') === false ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ substr($registro->hora_entrada, 0, 5) }}
                                        </span>
                                        @if($registro->retardo && strpos($registro->incidencias ?? '', '7') === false)
                                        <span
                                            class="ml-1 bg-rose-500 text-white text-[8px] font-black px-1 rounded flex items-center justify-center min-w-[14px]">R</span>
                                        @endif
                                    </div>
                                    @elseif($mostrarOmisionEntrada)
                                    <span class="text-rose-500 font-bold" title="Omisión de entrada">──:──</span>
                                    @elseif($registro->hora_entrada)
                                    <div class="flex items-center">
                                        <span
                                            class="{{ $registro->retardo && strpos($registro->incidencias ?? '', '7') === false ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ substr($registro->hora_entrada, 0, 5) }}
                                        </span>
                                        @if($registro->retardo && strpos($registro->incidencias ?? '', '7') === false)
                                        <span class="ml-1 bg-rose-500 text-white text-[8px] px-1 rounded">R</span>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                                <td class="px-3 py-1.5 whitespace-nowrap">
                                    @if(($estaChecadaDespuesMedioDia && $esSalidaPorHorario) || ($registro->hora_salida
                                    && !$tieneUnaSolaChecada))
                                    <span class="text-gray-700 dark:text-gray-300">{{ substr($registro->hora_salida, 0,
                                        5) }}</span>
                                    @elseif(($tieneUnaSolaChecada || ($registro->hora_entrada &&
                                    !$registro->hora_salida)) && $jornadaTerminada)
                                    <span class="text-rose-400 dark:text-rose-300/60 opacity-60 italic text-[10px]">Sin
                                        salida</span>
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
                                        <button x-data @click.stop="Swal.fire({
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
                                            title="Código {{ $inc }}. Click para eliminar">
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
        <div
            class="col-span-full py-20 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
            <div
                class="w-16 h-16 bg-gray-50 dark:bg-gray-900/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">No se encontraron registros para
                esta selección</p>
        </div>
        @endforelse
    </div>
    @else
    <div
        class="py-20 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 bg-[#1e5b4f]/5 rounded-3xl flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-[#1e5b4f] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h3 class="text-sm font-black text-gray-800 dark:text-gray-100 uppercase tracking-widest">Seleccionar Centro
        </h3>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Elija un centro de trabajo para
            visualizar las asistencias</p>
    </div>
    @endif

    {{-- MODAL DE CAPTURA (ESTILO PREMIUM INSTITUCIONAL) --}}
    <div x-show="$wire.isModalOpen" style="display: none;" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        {{-- Backdrop (Coherente con otros reportes) --}}
        <div x-show="$wire.isModalOpen" x-transition.opacity class="fixed inset-0 bg-[#13322B]/60 transition-opacity"
            aria-hidden="true" @click="$wire.isModalOpen = false; $wire.closeModal()"></div>

        {{-- Cuerpo del Modal (Equilibrado y con Alma) --}}
        <div x-show="$wire.isModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl flex flex-col z-10 border border-white/5">
            {{-- Header Sólido Institucional (#13322B) --}}
            <div style="border-top-left-radius: 1.5rem; border-top-right-radius: 1.5rem;"
                class="px-6 py-5 bg-[#13322B] text-white flex justify-between items-center relative shrink-0 border-b border-white/5">
                <div class="relative z-20 flex items-center gap-3">
                    <div class="p-2 bg-white/10 rounded-lg text-[#e6d194]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest">Captura Rápida</h3>
                        <p class="text-[9px] font-bold text-[#e6d194] uppercase tracking-widest opacity-80">
                            Justificación Biométrica</p>
                    </div>
                </div>
                <button @click="$wire.isModalOpen = false; $wire.closeModal()"
                    class="relative z-20 p-2 hover:bg-white/10 rounded-lg transition-colors text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-6">
                {{-- Info Empleado (Estilo Premium) --}}
                <div
                    class="p-5 bg-gray-50/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-3">
                        <span
                            class="px-2 py-0.5 bg-[#9b2247] text-white text-[9px] font-black rounded uppercase tracking-widest"
                            x-text="'ID: #' + tempEmpId">
                        </span>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="tempDate">
                        </span>
                    </div>
                    <p class="text-xs font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight leading-relaxed"
                        x-text="tempName">
                    </p>
                </div>

                {{-- Selector de Incidencia --}}
                <div class="px-1" x-data="{ 
                    open: false, 
                    selected_id: $wire.entangle('incidencia_id'),
                    get options() {
                        if(!this.$refs.optionsContainer) return [];
                        return Array.from(this.$refs.optionsContainer.children).map(el => ({
                            value: el.getAttribute('data-value'),
                            label: el.getAttribute('data-label')
                        }));
                    },
                    get selectedLabel() {
                        const option = this.options.find(opt => opt.value == this.selected_id);
                        return option && option.value !== '' ? option.label : 'SELECCIONE CÓDIGO...';
                    }
                }" @click.away="open = false">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">
                        Concepto de Incidencia / Código
                    </label>

                    <div class="relative group">
                        <button @click="open = !open" type="button"
                            class="flex items-center justify-between w-full py-3.5 pl-4 pr-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-[#13322B]/30 focus:border-[#13322B] transition-all shadow-sm outline-none uppercase">

                            <span class="truncate" x-text="selectedLabel">SELECCIONE CÓDIGO...</span>

                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden origin-top"
                            style="display: none;">

                            <div class="max-h-52 overflow-y-auto p-1.5 space-y-1" x-ref="optionsContainer">
                                <div @click="selected_id = ''; open = false" data-value=""
                                    data-label="SELECCIONE CÓDIGO..."
                                    class="px-3 py-3 rounded-xl cursor-pointer text-xs font-black uppercase transition-all flex items-center justify-between"
                                    :class="!selected_id ? 'bg-[#13322B] text-[#e6d194]' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#13322B]'">
                                    <span>SELECCIONE CÓDIGO...</span>
                                    <svg x-show="!selected_id" class="w-4 h-4 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                @foreach($codigos as $cod)
                                <div @click="selected_id = '{{ $cod->id }}'; open = false" data-value="{{ $cod->id }}"
                                    data-label="{{ $cod->code }} - {{ $cod->description }}"
                                    class="px-3 py-3 rounded-xl cursor-pointer text-xs font-black uppercase transition-all flex items-center justify-between"
                                    :class="selected_id == '{{ $cod->id }}' ? 'bg-[#13322B] text-[#e6d194]' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#13322B]'">
                                    <span class="truncate pr-2 w-full">{{ $cod->code }} - {{ $cod->description }}</span>
                                    <svg x-show="selected_id == '{{ $cod->id }}'" class="w-4 h-4 flex-shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @error('incidencia_id')
                    <span
                        class="text-[#9b2247] text-[10px] font-bold mt-2 block px-1 uppercase tracking-tight italic">{{
                        $message }}</span>
                    @enderror
                </div>

                {{-- Footer Modal --}}
                <div style="border-bottom-left-radius: 1.5rem; border-bottom-right-radius: 1.5rem;"
                    class="px-8 py-5 bg-gray-50/50 dark:bg-gray-900/50 flex justify-end gap-3 border-t dark:border-gray-700 rounded-b-3xl">
                    <button @click="$wire.isModalOpen = false; $wire.closeModal()"
                        class="px-6 py-3 text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="saveIncidencia" wire:loading.attr="disabled"
                        class="px-10 py-3 bg-[#13322B] text-[#e6d194] text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-[#13322B]/20 hover:scale-105 transition-all">
                        <span wire:loading.remove wire:target="saveIncidencia">Guardar Registro</span>
                        <span wire:loading wire:target="saveIncidencia">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>

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