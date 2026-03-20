<div x-show="tab === 'personal'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    
    <div class="space-y-8">
        {{-- Fila Primaria - Datos Críticos --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1.5 group">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Número de Empleado</label>
                <input type="text" wire:model.live.blur="num_empleado" maxlength="6"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-xl text-sm font-bold transition-all outline-none"
                    placeholder="000000">
                @error('num_empleado') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Fecha de Ingreso</label>
                <input type="date" wire:model="fecha_ingreso"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-xl text-sm font-bold transition-all outline-none">
                @error('fecha_ingreso') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span> @enderror
            </div>
            <div class="bg-oro/5 dark:bg-oro/10 px-4 py-2 rounded-xl border border-oro/20 flex items-center gap-3 self-end h-[46px]">
                <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">Sincronice el ID con el sistema de nómina.</p>
            </div>
        </div>

        {{-- Fila de Nombres --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1.5 group">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                    Nombre(s) <span class="text-[8px] text-oro font-black tracking-widest leading-none">●</span>
                </label>
                <input type="text" wire:model="name" readonly
                    class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-xl text-sm font-bold transition-all outline-none uppercase opacity-80 cursor-not-allowed">
            </div>
            <div class="space-y-1.5 group">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                    Ap. Paterno <span class="text-[8px] text-oro font-black tracking-widest leading-none">●</span>
                </label>
                <input type="text" wire:model="father_lastname" readonly
                    class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-xl text-sm font-bold transition-all outline-none uppercase opacity-80 cursor-not-allowed">
            </div>
            <div class="space-y-1.5 group">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                    Ap. Materno <span class="text-[8px] text-oro font-black tracking-widest leading-none">●</span>
                </label>
                <input type="text" wire:model="mother_lastname" readonly
                    class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-xl text-sm font-bold transition-all outline-none uppercase opacity-80 cursor-not-allowed">
            </div>
        </div>

        <div class="flex items-center gap-2 text-[9px] text-gray-400 font-bold uppercase tracking-widest pt-4">
            <span class="text-oro font-black">●</span> Los campos marcados son sincronizados automáticamente con el sistema externo.
        </div>
    </div>
</div>