<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="w-full md:w-1/2">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                    placeholder="Buscar por número de empleado o nombre..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-oro focus:border-oro text-sm shadow-sm transition"
                    autocomplete="off">
                <div class="absolute left-3 top-2.5 text-oro">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div wire:loading wire:target="search" class="absolute right-3 top-2.5 text-oro">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <button class="bg-verde hover:bg-verde-dark text-white px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition shadow-md whitespace-nowrap">
            + Nuevo Empleado
        </button>
    </div>

    <div class="overflow-x-auto relative">
        <div wire:loading.class="opacity-50" wire:target="search" class="transition">
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
                    <tr class="hover:bg-gray-50 transition" wire:key="employee-{{ $employee->id }}">
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
                            <a href="{{ route('employees.incidencias', $employee->id) }}" wire:navigate class="text-guinda hover:text-guinda-dark font-black uppercase tracking-tighter text-xs">Ver Incidencias</a>
                            <span class="mx-2 text-gray-300">|</span>
                            <button class="text-gray-500 hover:text-gray-900 font-bold text-xs uppercase">Editar</button>
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
    </div>

    <div class="mt-6">
        {{ $employees->links() }}
    </div>
</div>
