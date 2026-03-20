@props(['departments', 'puestos', 'horarios', 'jornadas', 'condiciones', 'externalData' => null])

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
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                        Número de Plaza
                        <span class="text-[8px] bg-oro/10 text-oro px-1.5 py-0.5 rounded uppercase tracking-widest font-black">Sincronizado</span>
                    </label>
                    <input type="text" wire:model="num_plaza" readonly
                        class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-2xl text-sm font-bold transition-all outline-none opacity-80 cursor-not-allowed">
                    @error('num_plaza') <span class="text-[9px] font-bold text-red-500 uppercase">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1.5">
                    <label
                        class="text-[10px] font-black text-[#13322B]/60 dark:text-[#e6d194]/60 uppercase tracking-tighter ml-1 flex items-center gap-2">
                        Número de Seguro
                        <span class="text-[8px] bg-oro/10 text-oro px-1.5 py-0.5 rounded uppercase tracking-widest font-black">Sincronizado</span>
                    </label>
                    <input type="text" wire:model="num_seguro" readonly
                        class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border-2 border-transparent rounded-2xl text-sm font-bold transition-all outline-none opacity-80 cursor-not-allowed">
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

    {{-- Sección de Datos Externos --}}
    <div class="mt-10 pt-8 border-t border-dashed border-gray-100 dark:border-gray-800">
        <h4 class="text-[10px] font-black text-oro uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-oro animate-pulse"></span>
            Datos Externos (Sincronizados de Plantilla)
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-gray-50/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Nivel Escalar</span>
                <span class="text-sm font-black text-[#13322B] dark:text-[#e6d194]">{{ $externalData['id_nivel'] ?? 'N/A' }}</span>
            </div>
            
            <div class="p-4 bg-gray-50/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Sub-Nivel</span>
                <span class="text-sm font-black text-gray-700 dark:text-gray-300">{{ $externalData['id_sub_nivel'] ?? 'N/A' }}</span>
            </div>
            
            <div class="p-4 bg-gray-50/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Forma de Pago</span>
                <span class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">{{ $externalData['id_forma_pago'] ?? 'N/A' }}</span>
            </div>
        </div>
        
        <p class="mt-4 text-[9px] text-gray-400 italic">
            * Estos datos se obtienen en tiempo real desde plantilla.issstebc.gob.mx y no son editables desde este sistema.
        </p>
    </div>
</div>