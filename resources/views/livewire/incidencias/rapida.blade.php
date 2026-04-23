<div>
    <div class="max-w-[1600px] mx-auto p-4 lg:p-8 space-y-8">
        {{-- ═══ ENCABEZADO DE SECCIÓN ═══ --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-[#13322B] flex items-center justify-center text-xl shadow-lg shadow-[#13322B]/20">⚡</span>
                    Incidencias Rápida <span class="text-xs font-bold text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full uppercase tracking-widest ml-2">Grid Mode</span>
                </h2>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mt-1 ml-1">Interfaz de alta velocidad para captura masiva</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-3">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado:</span>
                    <span class="flex items-center gap-2 text-[10px] font-black text-emerald-500 uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Listo para Capturar
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══ GRID DE CAPTURA (ESTILO EXCEL) ═══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700">
            {{-- Header del Grid --}}
            <div class="grid grid-cols-12 gap-0 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 rounded-t-2xl overflow-hidden">
                <div class="col-span-2 px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100 dark:border-gray-700 rounded-tl-2xl"># Empleado</div>
                <div class="col-span-2 px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100 dark:border-gray-700">Código</div>
                <div class="col-span-2 px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100 dark:border-gray-700 text-center">Fecha Inicio</div>
                <div class="col-span-2 px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100 dark:border-gray-700 text-center">Fecha Final</div>
                <div class="col-span-3 px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100 dark:border-gray-700">Periodo (Vac)</div>
                <div class="col-span-1 px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center rounded-tr-2xl">Acción</div>
            </div>

            {{-- Fila de Captura Activa --}}
            <div class="relative group z-20 transition-all duration-200 {{ $errors->has('general') ? 'bg-rose-50/20' : '' }}">
                <div class="grid grid-cols-12 gap-0 items-center">
                    {{-- Empleado --}}
                    <div class="col-span-2 border-r border-gray-100 dark:border-gray-700 relative">
                        <input type="text" 
                            wire:model.live="employee_input"
                            id="field-employee"
                            placeholder="33xxxx"
                            class="w-full h-14 px-6 bg-transparent text-sm font-black text-[#13322B] dark:text-emerald-400 placeholder-gray-300 outline-none focus:bg-emerald-50/30 transition-all uppercase rounded-bl-2xl"
                        >
                        {{-- Info flotante Empleado --}}
                        @if($selectedEmployee)
                            <div class="absolute left-6 top-[85%] z-30 bg-[#13322B] dark:bg-emerald-600 shadow-xl rounded-lg px-3 py-1 animate-fadeIn ring-2 ring-white dark:ring-gray-800">
                                <div class="text-[9px] font-black text-white uppercase whitespace-nowrap">{{ $selectedEmployee->fullname }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Código --}}
                    <div class="col-span-2 border-r border-gray-100 dark:border-gray-700 relative">
                        <input type="text" 
                            wire:model.live="codigo_input" 
                            placeholder="Cód." 
                            @if(!$selectedEmployee) disabled @endif
                            class="w-full h-14 px-6 bg-transparent text-sm font-black uppercase placeholder:text-gray-300 outline-none focus:bg-emerald-50/20 transition-all text-center {{ !$selectedEmployee ? 'opacity-10 cursor-not-allowed' : 'text-gray-700 dark:text-gray-200' }} {{ $errors->has('general') && $codigo_input ? 'text-rose-600' : '' }}">
                        
                        {{-- Burbuja de Código --}}
                        @if($selectedCodigo && !$errors->has('general'))
                            <div class="absolute left-6 top-[85%] z-50 bg-emerald-600 shadow-xl rounded-lg px-4 py-1.5 animate-fadeIn whitespace-nowrap border-2 border-white dark:border-gray-800">
                                <div class="text-[10px] font-black text-white uppercase tracking-tighter">
                                    {{ $selectedCodigo->description }}
                                </div>
                            </div>
                        @endif
                        
                        <div wire:loading wire:target="codigo_input" class="absolute right-2 top-4">
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Fechas (BLOQUEADAS SI HAY ERROR O NO HAY CÓDIGO) --}}
                    <div class="col-span-2 border-r border-gray-100 dark:border-gray-700">
                        <input type="text" wire:model.live="fecha_inicio" placeholder="D/M/A" 
                            @if(!$selectedEmployee || !$selectedCodigo || $errors->has('general')) disabled @endif
                            x-data @input.stop="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{2})(\d{2})?(\d{2,4})?/, (match, d, m, y) => {
                                let res = d || '';
                                if (m) res += '/' + m;
                                if (y) res += '/' + y;
                                return res;
                            }).substr(0, 10); $wire.set('fecha_inicio', $el.value)"
                            class="w-full h-14 px-4 bg-transparent text-xs font-black text-center text-gray-700 dark:text-gray-200 outline-none focus:bg-cyan-50/20 transition-all {{ (!$selectedEmployee || !$selectedCodigo || $errors->has('general')) ? 'opacity-5 cursor-not-allowed select-none' : '' }}">
                    </div>

                    <div class="col-span-2 border-r border-gray-100 dark:border-gray-700">
                        <input type="text" wire:model.live="fecha_final" placeholder="D/M/A" 
                            @if(!$selectedEmployee || !$selectedCodigo || !$fecha_inicio || $errors->has('general')) disabled @endif
                            x-data @input.stop="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{2})(\d{2})?(\d{2,4})?/, (match, d, m, y) => {
                                let res = d || '';
                                if (m) res += '/' + m;
                                if (y) res += '/' + y;
                                return res;
                            }).substr(0, 10); $wire.set('fecha_final', $el.value)"
                            class="w-full h-14 px-4 bg-transparent text-xs font-black text-center text-gray-700 dark:text-gray-200 outline-none focus:bg-cyan-50/20 transition-all {{ (!$selectedEmployee || !$selectedCodigo || !$fecha_inicio || $errors->has('general')) ? 'opacity-5 cursor-not-allowed select-none' : '' }}">
                    </div>

                    {{-- Periodo (Vacaciones) --}}
                    <div class="col-span-3 border-r border-gray-100 dark:border-gray-700 relative">
                        @if($is_vacaciones)
                            <select wire:model="periodo_id" 
                                @if(!$selectedEmployee || !$selectedCodigo || !$fecha_inicio || !$fecha_final || $errors->has('general')) disabled @endif
                                class="w-full h-14 px-6 bg-transparent text-[11px] font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest outline-none appearance-none {{ (!$selectedEmployee || !$selectedCodigo || !$fecha_inicio || !$fecha_final || $errors->has('general')) ? 'opacity-5 cursor-not-allowed' : '' }}">
                                <option value="">Seleccione Periodo...</option>
                                @foreach($periodos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->year }})</option>
                                @endforeach
                            </select>
                        @else
                            <div class="w-full h-14 px-6 flex items-center text-[10px] font-black text-gray-300 uppercase tracking-widest italic">No aplica</div>
                        @endif
                    </div>

                    {{-- Botón Guardar --}}
                    <div class="col-span-1 relative">
                        <button wire:click="store" 
                            @if(!$selectedEmployee || !$selectedCodigo || !$fecha_inicio || !$fecha_final || $errors->has('general')) disabled @endif
                            class="w-full h-14 flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white transition-all group-hover:scale-105 active:scale-95 shadow-lg shadow-emerald-500/20 rounded-br-2xl {{ (!$selectedEmployee || !$selectedCodigo || !$fecha_inicio || !$fecha_final || $errors->has('general')) ? 'opacity-5 grayscale cursor-not-allowed' : '' }}">
                            <span class="text-xl font-black">↵</span>
                        </button>
                    </div>
                </div>

                {{-- Barra de Error Persistente --}}
                @if($errors->has('general'))
                    <div class="bg-rose-600 px-6 py-2 border-t border-rose-500 animate-slideDown flex items-center gap-3">
                        <span class="text-lg">🚫</span>
                        <div class="text-[11px] font-black text-white uppercase tracking-widest">
                            Error de Validación: <span class="text-rose-100 ml-2">{{ $errors->first('general') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ═══ SUB-REGLONES PARA CAMPOS EXTRA (INLINE) ═══ --}}
            
            {{-- Extra: Incapacidad --}}
            @if($is_incapacidad)
                <div class="bg-rose-50/30 dark:bg-rose-500/5 border-t border-rose-100 dark:border-rose-500/10 px-6 py-4 animate-fadeIn">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2 min-w-[200px]">
                            <span class="text-xs">🩺</span>
                            <h4 class="text-[9px] font-black text-rose-800 dark:text-rose-400 uppercase tracking-widest">Incapacidad:</h4>
                        </div>
                        <div class="flex-1 grid grid-cols-12 gap-4">
                            <div class="col-span-6">
                                <select wire:model="medico_id" class="w-full h-9 px-3 bg-white dark:bg-gray-900 border border-rose-200/50 dark:border-rose-500/20 rounded-xl text-[10px] font-bold outline-none">
                                    <option value="">Seleccione Médico...</option>
                                    @foreach($medicos as $m)
                                        <option value="{{ $m->id }}">{{ strtoupper($m->fullname) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="text" wire:model="num_licencia" placeholder="Folio" class="w-full h-9 px-3 bg-white dark:bg-gray-900 border border-rose-200/50 dark:border-rose-500/20 rounded-xl text-[10px] font-bold outline-none">
                            </div>
                            <div class="col-span-2">
                                <input type="text" wire:model="fecha_expedida" placeholder="Fecha Exp." 
                                    x-data @input.stop="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{2})(\d{2})?(\d{2,4})?/, (match, d, m, y) => {
                                        let res = d || '';
                                        if (m) res += '/' + m;
                                        if (y) res += '/' + y;
                                        return res;
                                    }).substr(0, 10); $wire.set('fecha_expedida', $el.value)"
                                    class="w-full h-9 px-3 bg-white dark:bg-gray-900 border border-rose-200/50 dark:border-rose-500/20 rounded-xl text-[10px] font-bold outline-none">
                            </div>
                            <div class="col-span-12 mt-1">
                                <input type="text" wire:model="diagnostico" placeholder="Diagnóstico médico detallado..." class="w-full h-9 px-3 bg-white dark:bg-gray-900 border border-rose-200/50 dark:border-rose-500/20 rounded-xl text-[10px] font-bold outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Extra: TXT (900) --}}
            @if($is_txt)
                <div class="bg-emerald-50/30 dark:bg-emerald-500/5 border-t border-emerald-100 dark:border-emerald-500/10 px-6 py-4 animate-fadeIn">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2 min-w-[200px]">
                            <span class="text-xs">📝</span>
                            <h4 class="text-[9px] font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Detalles TXT:</h4>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <input type="text" wire:model="cobertura_txt" placeholder="¿Quién cubrió?" class="h-9 px-3 bg-white dark:bg-gray-900 border border-emerald-200/50 dark:border-emerald-500/20 rounded-xl text-[10px] font-bold outline-none">
                            <input type="text" wire:model="autoriza_txt" placeholder="¿Quién autorizó?" class="h-9 px-3 bg-white dark:bg-gray-900 border border-emerald-200/50 dark:border-emerald-500/20 rounded-xl text-[10px] font-bold outline-none">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Extra: Comisión (61) --}}
            @if($is_comision)
                <div class="bg-purple-50/30 dark:bg-purple-500/5 border-t border-purple-100 dark:border-purple-500/10 px-6 py-4 animate-fadeIn">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2 min-w-[200px]">
                            <span class="text-xs">💼</span>
                            <h4 class="text-[9px] font-black text-purple-800 dark:text-purple-400 uppercase tracking-widest">Comisión:</h4>
                        </div>
                        <input type="text" wire:model="motivo_comision" placeholder="Motivo de la comisión oficial..." class="flex-1 h-9 px-3 bg-white dark:bg-gray-900 border border-purple-200/50 dark:border-purple-500/20 rounded-xl text-[10px] font-bold outline-none">
                    </div>
                </div>
            @endif

            {{-- Extra: Otorgado (901) --}}
            @if($is_otorgado)
                <div class="bg-amber-50/30 dark:bg-amber-500/5 border-t border-amber-100 dark:border-amber-500/10 px-6 py-4 animate-fadeIn">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2 min-w-[200px]">
                            <span class="text-xs">🎖️</span>
                            <h4 class="text-[9px] font-black text-amber-800 dark:text-amber-400 uppercase tracking-widest">Otorgado:</h4>
                        </div>
                        <input type="text" wire:model="otorgado_txt" placeholder="Detalles del beneficio otorgado..." class="flex-1 h-9 px-3 bg-white dark:bg-gray-900 border border-amber-200/50 dark:border-amber-500/20 rounded-xl text-[10px] font-bold outline-none">
                    </div>
                </div>
            @endif
        </div>

        {{-- ═══ TABLA DE ACTIVIDAD RECIENTE ═══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-black uppercase tracking-tight flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs">📋</span>
                    Últimas Capturas de la Sesión
                </h3>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ count($displayCaptures) }} registros</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Qna</th>
                            <th class="px-6 py-4">Código</th>
                            <th class="px-6 py-4">Empleado</th>
                            <th class="px-6 py-4 text-center">Inicio</th>
                            <th class="px-6 py-4 text-center">Final</th>
                            <th class="px-6 py-4 text-center">Días</th>
                            <th class="px-6 py-4">Periodo</th>
                            <th class="px-6 py-4">Capturó</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($displayCaptures as $cap)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-900/20 transition-all group">
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black text-gray-500">{{ $cap['qna'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-[10px] font-black">{{ $cap['codigo'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-gray-700 dark:text-gray-200 uppercase">{{ $cap['employee'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400">{{ $cap['f_inicio'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400">{{ $cap['f_final'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-black">{{ $cap['total_dias'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black text-gray-500">{{ $cap['periodo'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-gray-700 dark:text-gray-200 uppercase">{{ $cap['quien'] }}</span>
                                        <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">{{ $cap['time'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="delete('{{ $cap['token'] }}')" wire:confirm="¿Eliminar esta incidencia?" class="p-2 hover:bg-rose-50 dark:hover:bg-rose-500/10 text-gray-300 hover:text-rose-500 rounded-xl transition-all opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="text-3xl">⌨️</span>
                                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">No hay capturas en esta sesión</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('focus-next', (event) => {
            const fieldId = `field-${event[0].field}`;
            const element = document.getElementById(fieldId);
            if (element) {
                setTimeout(() => {
                    element.focus();
                    if (element.tagName === 'INPUT') element.select();
                }, 50);
            }
        });
    });
</script>
