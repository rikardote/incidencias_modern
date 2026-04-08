<div>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Periodos Vacacionales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div
                    class="px-6 py-5 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-gray-100 dark:border-gray-700/50">
                    <div class="w-full md:w-1/2 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400 group-focus-within:text-oro transition-colors" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar periodo o año..."
                            class="w-full pl-10 h-11 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all text-sm">
                    </div>

                    <button wire:click="create"
                        class="w-full md:w-auto h-11 bg-[#13322B] hover:bg-[#1a4038] text-white px-8 rounded-xl text-xs font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-[#13322B]/20 hover:shadow-[#13322B]/40 active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Periodo
                    </button>
                </div>

                <div class="p-6">
                    <div wire:loading.class="opacity-50" wire:target="search"
                        class="transition-opacity duration-200 flex flex-col gap-3">
                        @forelse($periodos as $p)
                        <div wire:key="periodo-{{ $p->id }}"
                            class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md hover:border-[#13322B]/30 dark:hover:border-[#e6d194]/20 transition-all duration-200 overflow-hidden">

                            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 px-4 py-3">
                                <div class="flex items-center gap-4 min-w-0 w-full xl:w-auto">
                                    <div class="flex items-center gap-3">
                                        <!-- Bloque de Año -->
                                        <div class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 rounded-xl px-4 py-2 min-w-[70px] shadow-sm">
                                            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">AÑO</span>
                                            <span class="text-lg font-black text-[#9b2247] dark:text-[#e6d194] leading-tight">{{ $p->year }}</span>
                                        </div>

                                        <!-- Bloque de Periodo -->
                                        <div class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 rounded-xl px-4 py-2 min-w-[70px] shadow-sm">
                                            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">PERIODO</span>
                                            <span class="text-lg font-black text-gray-700 dark:text-gray-300 leading-tight">{{ $p->periodo }}</span>
                                        </div>

                                    </div>
                                </div>

                                <div class="shrink-0 flex flex-wrap items-center justify-around sm:justify-end gap-1 w-full xl:w-auto border-t xl:border-t-0 border-gray-100 dark:border-gray-700 pt-3 xl:pt-0">
                                    <button wire:click="edit({{ $p->id }})"
                                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="text-[8px] font-black uppercase tracking-tighter">Editar</span>
                                    </button>

                                    <div class="hidden sm:block w-px h-6 bg-gray-100 dark:bg-gray-700 mx-1"></div>

                                    <button onclick="confirmDeletePeriodo('{{ $p->id }}')"
                                        class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="text-[8px] font-black uppercase tracking-tighter">Eliminar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 text-gray-400 mb-4">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 italic">No se encontraron periodos vacacionales.</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-8 px-2">
                        {{ $periodos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Create / Edit -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" aria-hidden="true"
                wire:click="$set('showModal', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-white/20">
                <div
                    class="px-6 py-5 bg-gradient-to-r from-[#13322B] to-[#1e463d] text-white flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md text-[#e6d194]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-widest">
                            {{ $periodoId ? 'Editar Periodo' : 'Nuevo Periodo Vacacional' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="relative z-10 text-white opacity-60 hover:opacity-100 hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="flex flex-col h-full overflow-hidden">
                    <div class="px-8 py-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Año</label>
                                <input type="number" wire:model="year" id="year" placeholder="2024"
                                    class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all text-sm">
                                @error('year') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="periodo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cve Periodo (1 o 2)</label>
                                <input type="number" wire:model="periodo" id="periodo" placeholder="1"
                                    class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-oro/20 focus:border-oro transition-all text-sm">
                                @error('periodo') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="px-8 py-5 bg-gray-50 dark:bg-gray-900/80 flex flex-row-reverse rounded-b-2xl border-t dark:border-gray-700 backdrop-blur-md gap-4">
                        <button type="submit"
                            class="relative group bg-gradient-to-r from-[#13322B] to-[#1e463d] hover:from-[#1a4038] hover:to-[#245348] text-white px-10 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-[#13322B]/20 hover:shadow-[#13322B]/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 overflow-hidden">
                            <span class="relative z-10">Guardar Cambios</span>
                        </button>
                        <button wire:click="$set('showModal', false)" type="button"
                            class="px-5 py-2 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-gray-800 dark:hover:text-gray-300 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script shadow>
        function confirmDeletePeriodo(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#13322B',
                cancelButtonColor: '#9b2247',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.delete(id);
                }
            })
        }
    </script>
</div>
