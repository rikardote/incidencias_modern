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
    @php $lastEmp = null; @endphp
    @foreach($incidencias as $token => $group)
        @php 
            $firstInGroup = $group->first();
            $empId = $firstInGroup->employee->num_empleado;
            $totalDias = $group->sum('total_dias');
            $isNewEmp = ($empId !== $lastEmp);
        @endphp
        <tr class="{{ $isNewEmp && $lastEmp !== null ? 'group-divider' : '' }}">
            <td class="text-center">{{ $isNewEmp ? $empId : '' }}</td>
            <td>
                @if($isNewEmp)
                    <div style="font-size: 10pt; margin-bottom: 2px;">
                        {{ $firstInGroup->employee->father_lastname }} {{ $firstInGroup->employee->mother_lastname }} {{ $firstInGroup->employee->name }}
                    </div>
                @endif

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
            </td>
            <td class="text-center">
                @if($firstInGroup->codigo->code == 901) OT
                @elseif($firstInGroup->codigo->code == 905) PS
                @elseif($firstInGroup->codigo->code == 900) TXT
                @else {{ str_pad($firstInGroup->codigo->code, 2, '0', STR_PAD_LEFT) }}
                @endif
            </td>
            <td class="text-center">{{ \Carbon\Carbon::parse($firstInGroup->fecha_inicio)->format('d/m/Y') }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($firstInGroup->fecha_final)->format('d/m/Y') }}</td>
            <td class="text-center">
                @if($firstInGroup->periodo)
                    {{ $firstInGroup->periodo->periodo }}/{{ $firstInGroup->periodo->year }}
                @else
                    -
                @endif
            </td>
            <td class="text-center">{{ $totalDias }}</td>
        </tr>
        @php $lastEmp = $empId; @endphp
    @endforeach
    </tbody>
</table>
