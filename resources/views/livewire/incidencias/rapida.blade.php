<div>
    <div class="max-w-[1600px] mx-auto p-4 lg:p-6 space-y-4">
        {{-- ═══ CABECERA DE EMPLEADO (MINIMALISTA) ═══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 flex flex-wrap items-center gap-6 transition-all">
            <div class="w-48 space-y-0.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1"># Empleado</label>
                <input type="text" 
                    wire:model.live="employee_input"
                    wire:click="clearEmployee"
                    onclick="this.select()"
                    id="field-employee"
                    class="w-full h-10 px-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border-2 border-transparent focus:border-emerald-500/50 text-base font-black text-[#13322B] dark:text-emerald-400 outline-none transition-all uppercase">
            </div>

            <div class="flex-1 space-y-0.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Nombre del Trabajador</label>
                <div class="h-10 flex items-center px-4 bg-gray-50/30 dark:bg-gray-900/20 rounded-xl border border-gray-100 dark:border-gray-700">
                    @if($selectedEmployee)
                        <span class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase truncate">
                            {{ $selectedEmployee->fullname }}
                        </span>
                        <span class="ml-3 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-black rounded-lg uppercase tracking-tighter">
                            {{ $selectedEmployee->puesto->nombre ?? 'Activo' }}
                        </span>
                    @else
                        <span class="text-xs font-bold text-gray-300 italic uppercase tracking-widest">Esperando selección...</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Sesión</p>
                    <p class="text-sm font-black text-emerald-500 leading-none">{{ count($displayCaptures) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-lg">
                    ⚡
                </div>
            </div>
        </div>

        {{-- ═══ BLOQUE DE CAPTURA (NUEVA BARRA DE ACCIÓN) ═══ --}}
        <div class="relative z-30 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-cyan-100 dark:border-cyan-500/20 p-4 transition-all">
            <div class="grid grid-cols-12 gap-3 items-end">
                {{-- Código --}}
                <div class="col-span-1">
                    <label class="block text-[9px] font-black text-cyan-600 dark:text-cyan-400 uppercase mb-1.5 ml-1 tracking-widest">Código</label>
                    <input type="text" id="field-codigo" wire:model.live.debounce.300ms="codigo_input" 
                        wire:keydown.enter="store"
                        onclick="this.value=''; @this.set('codigo_input', '')"
                        placeholder="00"
                        @if(!$selectedEmployee) disabled @endif
                        class="w-full h-9 px-2 bg-cyan-50/30 dark:bg-cyan-900/20 border-2 border-cyan-100 dark:border-cyan-500/30 rounded-xl text-xs font-black text-cyan-700 dark:text-cyan-400 uppercase outline-none focus:border-cyan-500 transition-all text-center {{ !$selectedEmployee ? 'opacity-20 cursor-not-allowed' : '' }}">
                </div>

                {{-- Descripción (Informativa) --}}
                <div class="col-span-3">
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Incidencia</label>
                    <div class="h-9 flex items-center px-3 bg-gray-50/50 dark:bg-gray-900/30 rounded-xl border border-gray-100 dark:border-gray-700">
                        @if($selectedCodigo)
                            <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase truncate">
                                {{ $selectedCodigo->description }}
                            </span>
                        @else
                            <span class="text-[9px] font-bold text-gray-300 italic uppercase">Esperando código...</span>
                        @endif
                    </div>
                </div>

                {{-- Fechas --}}
                <div class="col-span-2">
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Inicio</label>
                    <input type="text" id="field-fecha_inicio" wire:model.live="fecha_inicio" placeholder="DD/MM/AA"
                        maxlength="8"
                        wire:keydown.enter="store"
                        @if(!$selectedEmployee || !$selectedCodigo) disabled @endif
                        x-data @input.stop="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{2})(\d{2})?(\d{2})?/, (match, d, m, y) => {
                            let res = d || ''; if (m) res += '/' + m; if (y) res += '/' + y; return res;
                        }).substr(0, 8); $wire.set('fecha_inicio', $el.value)"
                        class="w-full h-9 px-2 bg-white dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl text-xs font-black text-center text-gray-700 dark:text-gray-200 outline-none focus:border-cyan-500 transition-all {{ (!$selectedEmployee || !$selectedCodigo) ? 'opacity-20 cursor-not-allowed' : '' }}">
                </div>

                <div class="col-span-2">
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">Final</label>
                    <input type="text" id="field-fecha_final" wire:model.live="fecha_final" placeholder="DD/MM/AA"
                        maxlength="8"
                        wire:keydown.enter="store"
                        @if(!$selectedEmployee || !$selectedCodigo || !$fecha_inicio) disabled @endif
                        x-data @input.stop="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{2})(\d{2})?(\d{2})?/, (match, d, m, y) => {
                            let res = d || ''; if (m) res += '/' + m; if (y) res += '/' + y; return res;
                        }).substr(0, 8); $wire.set('fecha_final', $el.value)"
                        class="w-full h-9 px-2 bg-white dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl text-xs font-black text-center text-gray-700 dark:text-gray-200 outline-none focus:border-cyan-500 transition-all {{ (!$selectedEmployee || !$selectedCodigo || !$fecha_inicio) ? 'opacity-20 cursor-not-allowed' : '' }}">
                </div>

                {{-- Selectores Dinámicos (Periodo) --}}
                <div class="col-span-3">
                    @if($is_vacaciones)
                        <x-searchable-dropdown 
                            label="Periodo Vacacional"
                            placeholder="Ejem: 01/24"
                            wireModel="periodo_search"
                            :items="$periodos"
                            :selectedId="$periodo_id"
                            :selectedName="$periodo_selected_name"
                            selectedIdVar="periodo_id"
                            selectedNameVar="periodo_selected_name"
                            itemClass="periodo-item"
                            color="cyan"
                            onSelect="selectPeriodo"
                            :highlightedIndex="$highlightedIndex"
                        />
                    @endif
                </div>

                {{-- Botón Guardar --}}
                <div class="col-span-1">
                    <button wire:click="store" id="field-save_button"
                        @if(!$selectedEmployee || !$selectedCodigo || !$fecha_inicio) disabled @endif
                        class="w-full h-9 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-500/20 flex items-center justify-center transition-all active:scale-95 disabled:opacity-10 group">
                        <i class="fa fa-plus text-sm group-hover:rotate-90 transition-transform"></i>
                    </button>
                </div>
            </div>

            {{-- Fila Secundaria (Especiales: Médicos, TXT, etc.) --}}
            @if($is_incapacidad || $is_txt || $is_comision || $is_otorgado)
            <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 animate-fadeIn">
                <div class="grid grid-cols-12 gap-3 items-end">
                    @if($is_incapacidad)
                        <div class="col-span-4">
                            <x-searchable-dropdown 
                                label="Médico Expeditor"
                                placeholder="Buscar médico..."
                                wireModel="medico_search"
                                :items="$medicos"
                                :selectedId="$medico_id"
                                :selectedName="$medico_selected_name"
                                selectedIdVar="medico_id"
                                selectedNameVar="medico_selected_name"
                                itemClass="medico-item"
                                color="rose"
                                onSelect="selectMedico"
                                :highlightedIndex="$highlightedIndex"
                            />
                        </div>
                        <div class="col-span-4">
                            <label class="block text-[8px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Diagnóstico</label>
                            <input type="text" id="field-diagnostico" wire:model="diagnostico" wire:keydown.enter="store" placeholder="Descripción breve..." class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-[9px] font-bold outline-none focus:border-rose-400">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[8px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Folio/Licencia</label>
                            <input type="text" id="field-num_licencia" wire:model="num_licencia" wire:keydown.enter="store" placeholder="ABC-123" class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-[9px] font-bold outline-none focus:border-rose-400 text-center uppercase">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[8px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fecha Expedida</label>
                            <input type="text" id="field-fecha_expedida" wire:model.live="fecha_expedida" wire:keydown.enter="store" placeholder="DD/MM/AA"
                                maxlength="8"
                                x-data @input.stop="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{2})(\d{2})?(\d{2})?/, (match, d, m, y) => {
                                    let res = d || ''; if (m) res += '/' + m; if (y) res += '/' + y; return res;
                                }).substr(0, 8); $wire.set('fecha_expedida', $el.value)"
                                class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-[9px] font-bold outline-none focus:border-rose-400 text-center">
                        </div>
                    @endif

                    @if($is_txt)
                        <div class="col-span-6">
                            <label class="block text-[8px] font-black text-emerald-500 uppercase mb-1 ml-1 tracking-widest">¿Quién Cubrió?</label>
                            <input type="text" id="field-cobertura_txt" wire:model="cobertura_txt" wire:keydown.enter="store" placeholder="Nombre completo..." class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-emerald-100 dark:border-emerald-500/20 rounded-xl text-[9px] font-bold outline-none focus:border-emerald-400">
                        </div>
                        <div class="col-span-6">
                            <label class="block text-[8px] font-black text-emerald-500 uppercase mb-1 ml-1 tracking-widest">¿Quién Autorizó?</label>
                            <input type="text" id="field-autoriza_txt" wire:model="autoriza_txt" wire:keydown.enter="store" placeholder="Nombre completo..." class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-emerald-100 dark:border-emerald-500/20 rounded-xl text-[9px] font-bold outline-none focus:border-emerald-400">
                        </div>
                    @endif

                    @if($is_comision)
                        <div class="col-span-12">
                            <label class="block text-[8px] font-black text-purple-500 uppercase mb-1 ml-1 tracking-widest">Motivo de la Comisión</label>
                            <input type="text" id="field-motivo_comision" wire:model="motivo_comision" wire:keydown.enter="store" placeholder="Lugar y motivo específico..." class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-purple-100 dark:border-purple-500/20 rounded-xl text-[9px] font-bold outline-none focus:border-purple-400">
                        </div>
                    @endif

                    @if($is_otorgado)
                        <div class="col-span-12">
                            <label class="block text-[8px] font-black text-amber-500 uppercase mb-1 ml-1 tracking-widest">Detalles del Día Otorgado</label>
                            <input type="text" id="field-otorgado_txt" wire:model="otorgado_txt" wire:keydown.enter="store" placeholder="Comentarios adicionales..." class="w-full h-8 px-3 bg-gray-50 dark:bg-gray-900 border border-amber-100 dark:border-amber-500/20 rounded-xl text-[9px] font-bold outline-none focus:border-amber-400">
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- ═══ BLOQUE DE HISTORIAL (GRID PURA) ═══ --}}
        <div class="flex-1 min-h-0 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col transition-all">
            <div class="px-5 py-3 bg-gray-50/50 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Historial de Incidencias</h3>
                <div class="flex items-center gap-4">
                    <span class="text-[8px] font-black text-emerald-500 bg-emerald-100 dark:bg-emerald-500/10 px-2 py-0.5 rounded-full uppercase">Quincenas Activas</span>
                </div>
            </div>

            <style>
                @keyframes flash-highlight {
                    0% { background-color: rgba(16, 185, 129, 0.3); }
                    100% { background-color: transparent; }
                }
                .animate-highlight {
                    animation: flash-highlight 2s ease-out forwards;
                }
            </style>

            <div class="flex-1 overflow-y-auto scrollbar-thin">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur z-20 shadow-sm">
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest w-16 text-center">Cód</th>
                            <th class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest w-20 text-center">Qna</th>
                            <th class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest w-24 text-center">Inicio</th>
                            <th class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest w-24 text-center">Final</th>
                            <th class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest w-12 text-center">Días</th>
                            <th class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest">Detalles / Etiquetas / Auditoría</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($displayCaptures as $cap)
                            <tr wire:key="inc-{{ $cap['token'] }}" 
                                class="group hover:bg-cyan-50/20 dark:hover:bg-cyan-900/10 transition-all animate-slideDown {{ $lastAddedToken === $cap['token'] ? 'animate-highlight' : '' }}">
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-[10px] font-black">{{ $cap['codigo'] }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-[10px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-tighter">{{ $cap['qna'] }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400">{{ $cap['f_inicio'] }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-400">{{ $cap['f_final'] }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-[11px] font-black">{{ $cap['total_dias'] }}</span>
                                </td>

                                <td class="px-4 py-2 flex items-center justify-between min-h-[44px]">
                                    <div class="flex flex-wrap gap-1.5 items-center">
                                        @if($cap['periodo'] && $cap['periodo'] !== '--')
                                            <span class="text-cyan-600 font-black text-[9px] mr-2">PER: {{ $cap['periodo'] }}</span>
                                        @endif

                                        @if($cap['medico_info'])
                                            <span title="Médico: {{ $cap['medico_info'] }}" class="cursor-help px-1.5 py-0.5 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg text-[6.5px] font-black tracking-wider border border-rose-100 dark:border-rose-500/20 uppercase">MÉDICO</span>
                                        @endif

                                        @if($cap['folio'])
                                            <span title="Folio: {{ $cap['folio'] }}" class="cursor-help px-1.5 py-0.5 bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 rounded-lg text-[6.5px] font-black tracking-wider uppercase">FOLIO</span>
                                        @endif

                                        @if($cap['has_fecha_expedida'])
                                            <span title="Fecha Exp: {{ $cap['fecha_expedida_text'] }}" class="cursor-help px-1.5 py-0.5 bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 rounded-lg text-[6.5px] font-black tracking-wider uppercase">FECHA EXP</span>
                                        @endif

                                        @if($cap['has_cobertura'])
                                            <span title="Cubrió: {{ $cap['cobertura_text'] }}" class="cursor-help px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-lg text-[6.5px] font-black tracking-wider uppercase">COBERTURA</span>
                                        @endif

                                        @if($cap['has_autoriza'])
                                            <span title="Autorizó: {{ $cap['autoriza_text'] }}" class="cursor-help px-1.5 py-0.5 bg-green-200 dark:bg-green-500/30 text-green-800 dark:text-green-200 rounded-lg text-[6.5px] font-black tracking-wider uppercase">AUTORIZA</span>
                                        @endif
                                        
                                        @if((int)$cap['codigo'] === 901 || $cap['has_otorgado'])
                                            <span title="Día Otorgado: {{ $cap['otorgado_text'] ?? 'Sí' }}" class="cursor-help px-1.5 py-0.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg text-[6.5px] font-black tracking-wider border border-amber-100 dark:border-amber-500/20 uppercase">OTORGADO</span>
                                        @endif

                                        @if($cap['has_diagnostico'])
                                            <span title="Diagnóstico: {{ $cap['diagnostico_text'] }}" class="cursor-help px-1.5 py-0.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-[6.5px] font-black tracking-wider border border-blue-100 dark:border-blue-500/20 uppercase">DIAG</span>
                                        @endif

                                        @if($cap['has_comision'])
                                            <span title="Comisión: {{ $cap['comision_text'] }}" class="cursor-help px-1.5 py-0.5 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-lg text-[6.5px] font-black tracking-wider border border-purple-100 dark:border-purple-500/20 uppercase">COMISIÓN</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="flex flex-col items-end leading-tight">
                                            <span class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-tighter">{{ $cap['capturado_por'] }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 dark:text-gray-500">{{ $cap['fecha_capturado'] }}</span>
                                        </div>
                                        <button x-on:click="window.Swal.fire({ title: '¿Eliminar?', text: 'Se eliminará esta incidencia del historial.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar' }).then((r) => { if (r.isConfirmed) $wire.delete('{{ $cap['token'] }}') })"
                                            class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-500/10 text-gray-400 hover:text-rose-500 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-20 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest italic">No hay registros capturados recientemente</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Compacto --}}
        <div class="flex items-center justify-between px-4 py-2 bg-gray-50/50 dark:bg-gray-900/30 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Tips: [ENTER] Guardar · [TAB] Navegar</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Optimizado</span>
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
