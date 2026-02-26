<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">

        <button wire:click="create" class="bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-2 rounded text-xs font-bold uppercase tracking-wider transition whitespace-nowrap hidden md:block">
            + Nueva QNA
        </button>
    </div>

    <div class="overflow-x-auto relative">
        @if(session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('message') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">Año</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">QNA</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">Cierre</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">Estado</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($this->qnas as $qna)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition" wire:key="qna-{{ $qna->id }}">
                    <td class="px-4 py-4 text-center text-sm font-medium text-gray-900 dark:text-gray-100">{{ $qna->year }}</td>
                    <td class="px-4 py-4 text-center font-mono text-sm text-gray-600 dark:text-gray-400">{{ str_pad($qna->qna, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $qna->description ?: 'N/A' }}
                    </td>
                    <td class="px-4 py-4 text-center text-sm font-bold text-[#9b2247] dark:text-[#e6d194]">
                        {{ $qna->cierre ? \Carbon\Carbon::parse($qna->cierre)->format('d/m/Y') : 'N/D' }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        <button wire:click="toggleActive({{ $qna->id }})" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-oro rounded-full">
                            @if($qna->active == '1')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 transition hover:bg-green-200">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                    Abierta
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 transition hover:bg-red-200">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                    Cerrada
                                </span>
                            @endif
                        </button>
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        <button wire:click="edit({{ $qna->id }})" class="text-[#9b2247] dark:text-[#e6d194] hover:underline font-bold uppercase tracking-wide text-xs">Editar</button>
                        @if($qna->active == '1')
                            <span class="mx-2 text-gray-300 dark:text-gray-700">|</span>
                            <button wire:click="delete({{ $qna->id }})" wire:confirm="¿Estás seguro que deseas eliminar esta Quincena? Esta acción no se puede deshacer." class="text-red-500 hover:text-red-400 font-medium whitespace-nowrap">Eliminar</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for Create / Edit -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded shadow-xl transform transition-all sm:w-full sm:max-w-lg">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wide mb-4" id="modal-title">
                            {{ $qna_id ? 'Editar Quincena' : 'Nueva Quincena' }}
                        </h3>
                        <form wire:submit.prevent="store">
                            <div class="mb-4">
                                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Año</label>
                                <input type="number" wire:model="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('year') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="qna" class="block text-sm font-medium text-gray-700 dark:text-gray-300">QNA (Número)</label>
                                <input type="number" wire:model="qna" id="qna" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm" min="1" max="24">
                                @error('qna') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción (Opcional)</label>
                                <input type="text" wire:model="description" id="description" spellcheck="false" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="active_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                <select wire:model="active_status" id="active_status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                    <option value="1">Abierta (Activa)</option>
                                    <option value="0">Cerrada (Inactiva)</option>
                                </select>
                                @error('active_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Cierre</label>
                                <div class="relative flex items-center" wire:ignore x-data="{ 
                                    flatpickrInstance: null,
                                    init() { 
                                        this.flatpickrInstance = window.flatpickr(this.$refs.input, { 
                                            dateFormat: 'd/m/Y', 
                                            defaultDate: '{{ $cierre ? \Carbon\Carbon::parse($cierre)->format('d/m/Y') : '' }}', 
                                            onChange: (selectedDates, dateStr, instance) => { 
                                                if(selectedDates.length > 0) { 
                                                    $wire.set('cierre', instance.formatDate(selectedDates[0], 'Y-m-d')) 
                                                } else {
                                                    $wire.set('cierre', null)
                                                }
                                            } 
                                        }); 
                                    } 
                                }">
                                    <input type="text" x-ref="input" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro sm:text-sm cursor-pointer pr-10" placeholder="Selecciona Fecha (Opcional)">
                                    <button type="button" x-on:click="flatpickrInstance.clear(); $wire.set('cierre', null)" class="absolute right-2 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                @error('cierre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="pt-4 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                                <button type="submit" class="w-full inline-flex justify-center rounded px-6 py-2 bg-[#13322B] text-xs font-bold text-white uppercase tracking-wider hover:bg-[#0a1f1a] focus:outline-none sm:ml-3 sm:w-auto">
                                    Guardar
                                </button>
                                <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none sm:mt-0 sm:w-auto">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
