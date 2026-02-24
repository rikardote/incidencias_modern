<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b-2 border-oro">
                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Emp</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre Completo</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Departamento</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Puesto</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($employees as $employee)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-4 text-sm font-mono text-gray-500">{{ $employee->num_empleado }}</td>
                <td class="px-4 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $employee->fullname }}</div>
                </td>
                <td class="px-4 py-4 text-sm text-gray-600">
                    {{ $employee->department->description ?? 'N/A' }}
                </td>
                <td class="px-4 py-4 text-sm text-gray-600">
                    {{ $employee->puesto->puesto ?? 'N/A' }}
                </td>
                <td class="px-4 py-4 text-right text-sm">
                    <button class="text-guinda hover:text-guinda-dark font-black uppercase tracking-tighter text-xs">Ver</button>
                    <span class="mx-2 text-gray-300">|</span>
                    <button class="text-gray-500 hover:text-gray-900 font-bold uppercase text-xs">Editar</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-gray-500 text-sm italic">
                    No se encontraron empleados activos con ese criterio.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 pagination">
    {{ $employees->links() }}
</div>
