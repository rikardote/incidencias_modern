<div>
    {{-- Toggle de Mantenimiento --}}
    <div
        class="flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $isMaintenanceMode ? 'border-red-500 bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-700' }} transition-all group">
        <div>
            <h5
                class="text-lg font-bold {{ $isMaintenanceMode ? 'text-red-700 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }} transition-colors">
                Modo Mantenimiento</h5>
            <p
                class="text-sm {{ $isMaintenanceMode ? 'text-red-600 dark:text-red-500' : 'text-gray-500 dark:text-gray-400' }} mt-1">
                {{ $isMaintenanceMode ? 'Bloqueando la captura.' : 'Permitiendo captura normal.' }}
            </p>
        </div>

        <div class="flex items-center">
            <button wire:click="toggle"
                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $isMaintenanceMode ? 'bg-red-600 focus:ring-red-600' : 'bg-gray-200 dark:bg-gray-600 focus:ring-gray-500' }}"
                role="switch" aria-checked="{{ $isMaintenanceMode ? 'true' : 'false' }}">
                <span class="sr-only">Toggle maintenance mode</span>
                <span aria-hidden="true"
                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $isMaintenanceMode ? 'translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </div>
    </div>
</div>