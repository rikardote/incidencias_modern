<div x-show="tab === 'laboral'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    
    <div class="space-y-6">
        {{-- Estructura Principal --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Departamento de Adscripción</label>
                <select wire:model="deparment_id"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->code }} - {{ $d->description }}</option>
                    @endforeach
                </select>
                @error('deparment_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Puesto / Cargo</label>
                <select wire:model="puesto_id"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($puestos as $p)
                        <option value="{{ $p->id }}">{{ $p->puesto }}</option>
                    @endforeach
                </select>
                @error('puesto_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Condición Laboral</label>
                <select wire:model="condicion_id"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-xl text-sm font-bold transition-all outline-none appearance-none text-[#9b2247] dark:text-red-400">
                    <option value="">-- Seleccionar --</option>
                    @foreach($condiciones as $c)
                        <option value="{{ $c->id }}">{{ $c->condicion }}</option>
                    @endforeach
                </select>
                @error('condicion_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>@enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Tipo de Jornada</label>
                <select wire:model="jornada_id"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($jornadas as $j)
                        <option value="{{ $j->id }}">{{ $j->jornada }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Horario Asignado</label>
                <select wire:model="horario_id"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($horarios as $h)
                        <option value="{{ $h->id }}">{{ $h->horario }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-800">
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                    Número de Plaza <span class="text-[8px] text-oro font-black tracking-widest leading-none">●</span>
                </label>
                <input type="text" wire:model="num_plaza" readonly
                    class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-xl text-sm font-bold opacity-80 cursor-not-allowed">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                    Número de Seguro <span class="text-[8px] text-oro font-black tracking-widest leading-none">●</span>
                </label>
                <input type="text" wire:model="num_seguro" readonly
                    class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-xl text-sm font-bold opacity-80 cursor-not-allowed">
            </div>
        </div>
    </div>
</div>