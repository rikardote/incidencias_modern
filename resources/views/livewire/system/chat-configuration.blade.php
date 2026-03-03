<div>
    {{-- Configuración de Chat --}}
    <div
        class="flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $allToAll ? 'border-oro/30 bg-oro/5' : 'border-gray-100 dark:border-gray-700' }} transition-all group">
        <div class="flex-1 pr-4">
            <h5
                class="text-lg font-black {{ $allToAll ? 'text-[#9b2247] dark:text-oro' : 'text-gray-900 dark:text-gray-100' }} transition-colors uppercase tracking-tight">
                Interacción de Chat</h5>
            <p
                class="text-[11px] font-medium leading-tight {{ $allToAll ? 'text-gray-700 dark:text-gray-400' : 'text-gray-500 dark:text-gray-400' }} mt-1 italic">
                {{ $allToAll ? 'COMUNICACIÓN ABIERTA: Todos los usuarios pueden escribirse entre sí.' : 'COMUNICACIÓN RESTRINGIDA: Usuarios solo contactan a administradores.' }}
            </p>
        </div>

        <div class="flex items-center">
            <button wire:click="toggleAllToAll"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-oro focus:ring-offset-2 {{ $allToAll ? 'bg-[#13322b]' : 'bg-gray-200 dark:bg-gray-600' }}"
                role="switch" aria-checked="{{ $allToAll ? 'true' : 'false' }}">
                <span class="sr-only">Toggle all-to-all chat</span>
                <span aria-hidden="true"
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $allToAll ? 'translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </div>
    </div>
</div>
