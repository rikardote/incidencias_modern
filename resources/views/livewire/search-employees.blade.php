<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="w-full md:w-1/2">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                    placeholder="Buscar por número de empleado o nombre..." 
                    class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-[#13322B] focus:border-[#13322B] text-sm shadow-sm transition"
                    autocomplete="off">
                <div class="absolute left-3 top-2.5 text-oro">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <div wire:loading wire:target="search" class="text-oro mr-1">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    @if(strlen($search) > 0)
                    <button 
                        wire:click="$set('search', '')"
                        type="button"
                        class="text-gray-300 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none transition-colors mt-0.5">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        <button class="bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-2 rounded text-xs font-bold uppercase tracking-wider transition whitespace-nowrap hidden md:block">
            + Nuevo Empleado
        </button>
    </div>

    <div class="overflow-x-auto relative">
        <div wire:loading.class="opacity-50" wire:target="search" class="transition">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No. Emp</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Departamento</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Puesto</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition" wire:key="employee-{{ $employee->id }}">
                        <td class="px-4 py-4 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $employee->num_empleado }}</td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $employee->fullname }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $employee->department->description ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $employee->puesto->puesto ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <a href="{{ route('employees.incidencias', $employee->id) }}" wire:navigate class="text-[#9b2247] dark:text-[#e6d194] hover:underline font-bold uppercase tracking-wide text-xs">Ver Incidencias</a>
                            <span class="mx-2 text-gray-300 dark:text-gray-700">|</span>
                            <button class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 font-bold text-xs uppercase">Editar</button>
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
