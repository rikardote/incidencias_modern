<table class="content-table" width="100%" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="10%">No. Emp</th>
            <th width="40%">Nombre</th>
            <th width="20%">Clave del puesto</th>
            <th width="30%">Denominación de puesto</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $index => $emp)
            <tr class="group-divider">
                <td class="text-center font-bold">{{ $emp->num_empleado }}</td>
                <td class="uppercase">{{ $emp->name }} {{ $emp->father_lastname }} {{ $emp->mother_lastname }}</td>
                <td class="uppercase text-center">{{ $emp->puesto->clave ?? 'N/A' }}</td>
                <td class="uppercase">{{ $emp->puesto->puesto ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center; font-style: italic; padding-top: 20px;">
                    No se encontraron empleados sin derecho a nota buena.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

