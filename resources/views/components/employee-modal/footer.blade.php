@props(['editingEmployeeId' => false])

<div
    class="px-10 py-6 bg-gray-50 dark:bg-gray-900/90 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3 rounded-b-3xl">
    <button type="button" wire:click="$set('showEmployeeModal', false)"
        class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
        Cerrar Ficha
    </button>

    <button type="submit"
        class="group relative px-10 py-3 bg-gradient-to-r from-[#13322B] to-[#1e463d] text-white rounded-2xl overflow-hidden shadow-xl shadow-[#13322B]/20 transition-all duration-300 hover:-translate-y-1 active:scale-95">
        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <span class="relative z-10 text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2">
            {{ $editingEmployeeId ? 'Guardar Cambios' : 'Finalizar Registro' }}
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </span>
    </button>
</div>