<table class="content-table" autosize="1">
    <thead>
    <tr>
        <th style="width: 10%;">Num Empleado</th>
        <th style="width: 35%;">Empleado</th>
        <th style="width: 8%;">Codigo</th>
        <th style="width: 12%;">Fecha Inicial</th>
        <th style="width: 12%;">Fecha Final</th>
        <th style="width: 13%;">Periodo</th>
        <th style="width: 10%;" class="text-center">Total</th>
    </tr>
    </thead>
    <tbody>
    @php 
        $lastPrintedEmp = null; 
    @endphp
    @foreach($incidencias as $token => $group)
        @php 
            $firstInGroup = $group->first();
            $emp = $firstInGroup->employee;
            $empId = $emp->num_empleado;
            $isVirtual = in_array($firstInGroup->codigo->code, [92, 93, 94]);
            $totalDias = $group->sum('total_dias');
            
            // Buscar si el empleado tiene incidencias reales en este reporte
            // Esto es para decidir si mostramos un placeholder si solo tiene virtual
            $hasReal = $incidencias->contains(function($g) use ($empId) {
                return $g->first()->employee->num_empleado == $empId && !in_array($g->first()->codigo->code, [92, 93, 94]);
            });

            $isFirstTime = ($empId !== $lastPrintedEmp);
            $shouldRenderRow = !$isVirtual || (!$hasReal && $isFirstTime);
        @endphp

        @if($shouldRenderRow)
            <tr class="{{ ($isFirstTime && $lastPrintedEmp !== null) ? 'group-divider' : '' }}">
                <td class="text-center">{{ $isFirstTime ? $empId : '' }}</td>
                <td>
                    @if($isFirstTime)
                        <div style="font-size: 9.5pt; font-weight: bold; margin-bottom: 2px; color: #111;">
                            {{ $emp->father_lastname }} {{ $emp->mother_lastname }} {{ $emp->name }}
                        </div>
                        
                        <div style="font-size: 8pt; color: #6b7280; font-weight: normal; margin-bottom: 3px;">
                            HORARIO: {{ $emp->horario->horario ?? 'N/A' }}
                        </div>

                        {{-- Avisos de estado --}}
                        @if($emp->lactancia || $emp->estancia || $emp->exento)
                            <div style="margin-top: 2px; margin-bottom: 4px;">
                                @if($emp->lactancia)
                                    <span style="background-color: #10b981; color: white; padding: 1px 4px; border-radius: 2px; font-size: 6.5pt; margin-right: 4px; font-weight: bold;">C92 - LACTANCIA</span>
                                @endif
                                @if($emp->estancia)
                                    <span style="background-color: #3b82f6; color: white; padding: 1px 4px; border-radius: 2px; font-size: 6.5pt; margin-right: 4px; font-weight: bold;">C93 - ESTANCIA</span>
                                @endif
                                @if($emp->exento)
                                    <span style="background-color: #d4af37; color: white; padding: 1px 4px; border-radius: 2px; font-size: 6.5pt; font-weight: bold;">C94 - EXENTO</span>
                                @endif
                            </div>
                        @endif
                        @php $lastPrintedEmp = $empId; @endphp
                    @endif

                    @if(!$isVirtual)
                        @if($firstInGroup->otorgado)
                           <div class="comment-text">{{ $firstInGroup->otorgado }}</div>
                        @endif
                        @if($firstInGroup->becas_comments)
                           <div class="comment-text">{{ $firstInGroup->becas_comments }}</div>
                        @endif
                        @if($firstInGroup->horas_otorgadas)
                           <div class="comment-text">{{ $firstInGroup->horas_otorgadas }}</div>
                        @endif
                        @if($firstInGroup->codigo->code == 900 && $firstInGroup->autoriza_txt)
                           <div class="comment-text">{{ $firstInGroup->autoriza_txt }}</div>
                        @endif
                    @endif
                </td>
                
                <td class="text-center">
                    @if(!$isVirtual)
                        @if($firstInGroup->codigo->code == 901) OT
                        @elseif($firstInGroup->codigo->code == 905) PS
                        @elseif($firstInGroup->codigo->code == 900) TXT
                        @else {{ str_pad($firstInGroup->codigo->code, 2, '0', STR_PAD_LEFT) }}
                        @endif
                    @endif
                </td>
                <td class="text-center">
                    @if(!$isVirtual)
                        {{ \Carbon\Carbon::parse($firstInGroup->fecha_inicio)->format('d/m/Y') }}
                    @endif
                </td>
                <td class="text-center">
                    @if(!$isVirtual)
                        {{ \Carbon\Carbon::parse($firstInGroup->fecha_final)->format('d/m/Y') }}
                    @endif
                </td>
                <td class="text-center">
                    @if(!$isVirtual && $firstInGroup->periodo)
                        {{ $firstInGroup->periodo->periodo }}/{{ $firstInGroup->periodo->year }}
                    @elseif(!$isVirtual)
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if(!$isVirtual)
                        {{ $totalDias }}
                    @endif
                </td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
