<div x-show="tab === 'personal'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5 group">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Número
                        de Empleado</label>
                    <input type="text" wire:model.live.blur="num_empleado" maxlength="6"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none"
                        placeholder="000000">
                    @error('num_empleado') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message
                        }}</span> @enderror
                </div>
                <div class="space-y-1.5">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Fecha
                        de Ingreso</label>
                    <input type="date" wire:model="fecha_ingreso"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none">
                    @error('fecha_ingreso') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message
                        }}</span> @enderror
                </div>
            </div>

            <div class="space-y-1.5 group">
                <label
                    class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                    Nombre(s)
                    <span class="text-[8px] bg-oro/10 text-oro px-1.5 py-0.5 rounded uppercase tracking-widest font-black">Sincronizado</span>
                </label>
                <input type="text" wire:model="name" readonly
                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-2xl text-sm font-bold transition-all outline-none uppercase opacity-80 cursor-not-allowed">
                @error('name') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5 group">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                        Apellido Paterno
                        <span class="text-[8px] bg-oro/10 text-oro px-1.5 py-0.5 rounded uppercase tracking-widest font-black">Sincronizado</span>
                    </label>
                    <input type="text" wire:model="father_lastname" readonly
                        class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-2xl text-sm font-bold transition-all outline-none uppercase opacity-80 cursor-not-allowed">
                    @error('father_lastname') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message
                        }}</span> @enderror
                </div>
                <div class="space-y-1.5 group">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                        Apellido Materno
                        <span class="text-[8px] bg-oro/10 text-oro px-1.5 py-0.5 rounded uppercase tracking-widest font-black">Sincronizado</span>
                    </label>
                    <input type="text" wire:model="mother_lastname" readonly
                        class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-2xl text-sm font-bold transition-all outline-none uppercase opacity-80 cursor-not-allowed">
                    @error('mother_lastname') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message
                        }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-4">
                <div class="space-y-1.5">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                        CURP
                        <span class="text-[8px] bg-oro/10 text-oro px-1.5 py-0.5 rounded uppercase tracking-widest font-black">Sincronizado</span>
                    </label>
                    <input type="text" wire:model="curp" readonly
                        class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-2xl text-[11px] font-mono font-bold tracking-widest transition-all outline-none uppercase opacity-80 cursor-not-allowed">
                    @error('curp') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1.5">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                        RFC
                        <span class="text-[8px] bg-oro/10 text-oro px-1.5 py-0.5 rounded uppercase tracking-widest font-black">Sincronizado</span>
                    </label>
                    <input type="text" wire:model="rfc" readonly
                        class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-2xl text-[11px] font-mono font-bold tracking-widest transition-all outline-none uppercase opacity-80 cursor-not-allowed">
                    @error('rfc') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="bg-oro/5 dark:bg-oro/10 p-6 rounded-3xl border border-oro/20 flex items-center gap-4">
                <div
                    class="w-12 h-12 shrink-0 bg-oro text-[#13322B] rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-oro uppercase tracking-wider mb-1">Nota importante</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-snug">Asegúrese de sincronizar el
                        Número de Empleado con el sistema de nómina.</p>
                </div>
            </div>
        </div>
    </div>
</div>