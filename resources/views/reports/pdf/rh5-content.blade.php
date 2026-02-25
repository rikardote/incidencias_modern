<table class="content-table">
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
                    {{ $firstInGroup->employee->father_lastname }} {{ $firstInGroup->employee->mother_lastname }} {{ $firstInGroup->employee->name }}
                @endif

                @if($firstInGroup->otorgado)
                   <span class="comment-text">{{ $firstInGroup->otorgado }}</span>
                @endif
                @if($firstInGroup->becas_comments)
                   <span class="comment-text">{{ $firstInGroup->becas_comments }}</span>
                @endif
                @if($firstInGroup->horas_otorgadas)
                   <span class="comment-text">{{ $firstInGroup->horas_otorgadas }}</span>
                @endif
                @if($firstInGroup->codigo->code == 900 && $firstInGroup->autoriza_txt)
                   <span class="comment-text">{{ $firstInGroup->autoriza_txt }}</span>
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
