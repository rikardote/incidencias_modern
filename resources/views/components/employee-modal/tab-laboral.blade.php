@props(['departments', 'puestos', 'horarios', 'jornadas', 'condiciones'])

<div x-show="tab === 'laboral'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-6">
            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Departamento
                    de Adscripción</label>
                <select wire:model="deparment_id"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->id }}">{{ $d->code }} - {{ $d->description }}</option>
                    @endforeach
                </select>
                @error('deparment_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Puesto
                    / Cargo</label>
                <select wire:model="puesto_id"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($puestos as $p)
                    <option value="{{ $p->id }}">{{ $p->puesto }}</option>
                    @endforeach
                </select>
                @error('puesto_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Número
                        de Plaza</label>
                    <input type="text" wire:model="num_plaza"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none">
                    @error('num_plaza') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1.5">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Número
                        de Seguro</label>
                    <input type="text" wire:model="num_seguro"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none">
                    @error('num_seguro') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Condición
                    Laboral</label>
                <select wire:model="condicion_id"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none appearance-none text-[#9b2247]">
                    <option value="">-- Seleccionar --</option>
                    @foreach($condiciones as $c)
                    <option value="{{ $c->id }}">{{ $c->condicion }}</option>
                    @endforeach
                </select>
                @error('condicion_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Tipo
                    de Jornada</label>
                <select wire:model="jornada_id"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($jornadas as $j)
                    <option value="{{ $j->id }}">{{ $j->jornada }}</option>
                    @endforeach
                </select>
                @error('jornada_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1">Horario
                    Asignado</label>
                <select wire:model="horario_id"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-[#13322B] dark:focus:border-[#e6d194] rounded-2xl text-sm font-bold transition-all outline-none appearance-none">
                    <option value="">-- Seleccionar --</option>
                    @foreach($horarios as $h)
                    <option value="{{ $h->id }}">{{ $h->horario }}</option>
                    @endforeach
                </select>
                @error('horario_id') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>