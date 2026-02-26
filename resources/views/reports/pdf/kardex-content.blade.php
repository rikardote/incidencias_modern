<table class="content-table" width="100%" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="10%" class="text-center">Código</th>
            <th width="35%">Comentario</th>
            <th width="15%" class="text-center">Fecha Inicial</th>
            <th width="15%" class="text-center">Fecha Final</th>
            <th width="10%" class="text-center">Días</th>
            <th width="15%" class="text-center">Periodo</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $inc)
            <tr class="group-divider">
                <td class="text-center font-bold">
                    {{ str_pad($inc->codigo->code, 2, "0", STR_PAD_LEFT) }}
                </td>
                <td class="uppercase" style="font-size: 8pt;">
                    {{ $inc->otorgado }}
                </td>
                <td class="text-center">{{ \Carbon\Carbon::parse($inc->fecha_inicio)->format('d/m/Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($inc->fecha_final)->format('d/m/Y') }}</td>
                <td class="text-center font-bold" style="color: #9b2247;">{{ $inc->total_dias }}</td>
                <td class="text-center uppercase font-bold">
                    @if($inc->periodo)
                        {{ $inc->periodo->periodo }}/{{ $inc->periodo->year }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic; padding-top: 20px;">
                    No se encontraron incidencias grabadas para este empleado en el rango de fechas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
