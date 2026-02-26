<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">

        <button wire:click="create"
            class="bg-[#13322B] hover:bg-[#0a1f1a] text-white px-6 py-2 rounded text-xs font-bold uppercase tracking-wider transition whitespace-nowrap hidden md:block">
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
                    <th
                        class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">
                        Año</th>
                    <th
                        class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">
                        QNA</th>
                    <th
                        class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        Descripción</th>
                    <th
                        class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">
                        Cierre</th>
                    <th
                        class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center">
                        Estado</th>
                    <th
                        class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-right">
                        Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($this->qnas as $qna)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition" wire:key="qna-{{ $qna->id }}">
                    <td class="px-4 py-4 text-center text-sm font-medium text-gray-900 dark:text-gray-100">{{ $qna->year
                        }}</td>
                    <td class="px-4 py-4 text-center font-mono text-sm text-gray-600 dark:text-gray-400">{{
                        str_pad($qna->qna, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $qna->description ?: 'N/A' }}
                    </td>
                    <td class="px-4 py-4 text-center text-sm font-bold text-[#9b2247] dark:text-[#e6d194]">
                        {{ $qna->cierre ? \Carbon\Carbon::parse($qna->cierre)->format('d/m/Y') : 'N/D' }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        <button wire:click="toggleActive({{ $qna->id }})"
                            class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-oro rounded-full">
                            @if($qna->active == '1')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 transition hover:bg-green-200">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor"
                                    viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Abierta
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 transition hover:bg-red-200">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Cerrada
                            </span>
                            @endif
                        </button>
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        @if($qna->active == '1')
                        <button wire:click="edit({{ $qna->id }})"
                            class="text-[#9b2247] dark:text-[#e6d194] hover:underline font-bold uppercase tracking-wide text-xs">Editar</button>
                        <span class="mx-2 text-gray-300 dark:text-gray-700">|</span>
                        <button type="button" x-on:click="
                                Swal.fire({
                                    title: '¿Estás seguro?',
                                    text: 'Esta acción no se puede deshacer y borrará la quincena permanentemente.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#9b2247',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Sí, eliminar',
                                    cancelButtonText: 'Cancelar',
                                    reverseButtons: true,
                                    backdrop: true,
                                    customClass: {
                                        popup: 'rounded-2xl border-0 dark:bg-gray-800 dark:text-gray-100',
                                        title: 'text-xl font-black uppercase tracking-tight',
                                        confirmButton: 'rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-widest',
                                        cancelButton: 'rounded-xl px-5 py-2.5 text-xs font-black uppercase tracking-widest'
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $wire.delete({{ $qna->id }})
                                    }
                                })
                            " class="text-red-500 hover:text-red-400 font-medium whitespace-nowrap">Eliminar</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL DE REGISTRO / EDICION --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" aria-hidden="true"
                wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-white/20">
                <div
                    class="px-6 py-5 bg-gradient-to-r from-[#13322B] to-[#1e463d] text-white flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md">
                            <svg class="w-5 h-5 text-[#e6d194]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-black uppercase tracking-widest">
                            {{ $qna_id ? 'Configurar Quincena' : 'Nueva Apertura de QNA' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal"
                        class="relative z-10 text-white opacity-60 hover:opacity-100 hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="store" class="flex flex-col">
                    <div class="px-8 py-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="year"
                                    class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1">Año
                                    Fiscal</label>
                                <input type="number" wire:model="year" id="year"
                                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all font-mono">
                                @error('year') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label for="qna"
                                    class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1">No.
                                    Quincena</label>
                                <input type="number" wire:model="qna" id="qna" min="1" max="24"
                                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all font-mono">
                                @error('qna') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="description"
                                class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1">Descripción /
                                Notas</label>
                            <input type="text" wire:model="description" id="description" spellcheck="false"
                                placeholder="Ej. Primer quincena de Enero..."
                                class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all">
                            @error('description') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1">Fecha
                                de Cierre Limite</label>
                            <div class="relative group" wire:ignore x-data="{ 
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
                                <input type="text" x-ref="input"
                                    class="w-full pl-4 pr-10 py-2 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all text-sm cursor-pointer"
                                    placeholder="dd/mm/aaaa">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                    <button type="button"
                                        x-on:click="flatpickrInstance.clear(); $wire.set('cierre', null)"
                                        class="text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @error('cierre') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div
                        class="px-8 py-6 bg-gray-50 dark:bg-gray-900/80 flex flex-row-reverse rounded-b-2xl border-t dark:border-gray-700 backdrop-blur-md gap-4">
                        <button type="submit"
                            class="relative group bg-gradient-to-r from-[#13322B] to-[#1e463d] hover:from-[#1a4038] hover:to-[#245348] text-white px-10 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-[#13322B]/20 hover:shadow-[#13322B]/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 overflow-hidden">
                            <span class="relative z-10">Guardar Cambios</span>
                            <div
                                class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>
                        </button>
                        <button wire:click="closeModal" type="button"
                            class="px-5 py-2 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-gray-800 dark:hover:text-gray-300 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>