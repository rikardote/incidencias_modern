<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-xl font-black text-guinda uppercase tracking-tighter">Listado de Quincenas</h3>
        <button wire:click="create" class="bg-verde hover:bg-verde-dark text-white px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition shadow-md whitespace-nowrap">
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
            <thead>
                <tr class="bg-gray-50 border-b-2 border-oro">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Año</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">QNA</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Estado</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($this->qnas as $qna)
                <tr class="hover:bg-gray-50 transition" wire:key="qna-{{ $qna->id }}">
                    <td class="px-4 py-4 text-center text-sm font-medium text-gray-900">{{ $qna->year }}</td>
                    <td class="px-4 py-4 text-center font-mono text-sm text-gray-600">{{ str_pad($qna->qna, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600">
                        {{ $qna->description ?: 'N/A' }}
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
                        <button wire:click="edit({{ $qna->id }})" class="text-guinda hover:text-guinda-dark font-black uppercase tracking-tighter text-xs">Editar</button>
                        @if($qna->active == '1')
                            <span class="mx-2 text-gray-300">|</span>
                            <button wire:click="delete({{ $qna->id }})" wire:confirm="¿Estás seguro que deseas eliminar esta Quincena? Esta acción no se puede deshacer." class="text-red-500 hover:text-red-700 font-medium whitespace-nowrap">Eliminar</button>
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
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-black text-guinda uppercase tracking-tighter mb-4" id="modal-title">
                            {{ $qna_id ? 'Editar Quincena' : 'Nueva Quincena' }}
                        </h3>
                        <form wire:submit.prevent="store">
                            <div class="mb-4">
                                <label for="year" class="block text-sm font-medium text-gray-700">Año</label>
                                <input type="number" wire:model="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('year') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="qna" class="block text-sm font-medium text-gray-700">QNA (Número)</label>
                                <input type="number" wire:model="qna" id="qna" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-oro focus:ring-oro sm:text-sm" min="1" max="24">
                                @error('qna') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                                <input type="text" wire:model="description" id="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-5">
                                <label for="active_status" class="block text-sm font-medium text-gray-700">Estado</label>
                                <select wire:model="active_status" id="active_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-oro focus:ring-oro sm:text-sm">
                                    <option value="1">Abierta (Activa)</option>
                                    <option value="0">Cerrada (Inactiva)</option>
                                </select>
                                @error('active_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse -mx-4 sm:-mx-6 mb--4 pb-0 items-center justify-end">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-md px-6 py-2 bg-verde text-xs font-black text-white uppercase tracking-widest hover:bg-verde-dark focus:outline-none sm:ml-3 sm:w-auto">
                                    Guardar
                                </button>
                                <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
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
