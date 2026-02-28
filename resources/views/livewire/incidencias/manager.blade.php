<div x-data="{ 
    query: '', 
    results: [], 
    openModal: false,
    loading: false,
    debounceTimer: null,
    search() {
        clearTimeout(this.debounceTimer);
        if (this.query.length < 2) { this.results = []; return; }
        this.loading = true;
        this.debounceTimer = setTimeout(() => {
            fetch('/api/employees/search?q=' + encodeURIComponent(this.query), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => { this.results = data; this.loading = false; })
            .catch(() => { this.loading = false; });
        }, 300);
    },
    select(emp) {
        this.openModal = false;
        this.query = '';
        this.results = [];
        $wire.cambiarEmpleado(emp.id);
    }
}" x-on:keydown.escape="openModal = false">
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Layout principal: formulario fijo + tabla --}}
            <div class="flex flex-col xl:flex-row items-start gap-4">

                {{-- COLUMNA IZQUIERDA: Formulario de captura --}}
                <div class="w-full xl:w-[40%] text-sm">



                    {{-- Formulario de captura --}}
                    <div
                        class="bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700 sticky top-4 relative z-40">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#13322B] dark:bg-[#e6d194]"></div>
                        <div
                            class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between relative overflow-visible">

                            <h3
                                class="text-xs font-black text-[#13322B] dark:text-gray-200 uppercase tracking-[0.2em] flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-[#13322B] dark:bg-[#e6d194] animate-pulse"></div>
                                <span>Capturar <span class="hidden sm:inline">Incidencia</span></span>
                            </h3>

                            {{-- Botón Gatillo Lupita --}}
                            <button @click="openModal = true; $nextTick(() => $refs.modalSearchInput.focus())"
                                type="button" title="Buscar empleado"
                                class="w-9 h-9 rounded-full bg-[#13322B] dark:bg-[#e6d194] flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 transition-all text-white dark:text-[#13322B]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>

                        @if(auth()->user()->canCaptureInClosedQna())
                        @php
                        $activeException = auth()->user()->captureExceptions()->where('expires_at', '>',
                        now())->first();
                        $expiresAtUnix = $activeException ? $activeException->expires_at->timestamp : 0;
                        @endphp
                        <div class="bg-oro/10 border-b border-oro/20 px-5 py-2 flex items-center justify-between"
                            x-data="{
                                expiresAt: {{ $expiresAtUnix }},
                                remaining: 0,
                                timer: null,
                                get minutes() { return String(Math.floor(this.remaining / 60)).padStart(2, '0') },
                                get seconds() { return String(this.remaining % 60).padStart(2, '0') },
                                get urgent()  { return this.remaining <= 30 },
                                get warning() { return this.remaining <= 120 && this.remaining > 30 },
                                init() {
                                    this.remaining = Math.max(0, this.expiresAt - Math.floor(Date.now() / 1000));
                                    this.timer = setInterval(() => {
                                        this.remaining = Math.max(0, this.expiresAt - Math.floor(Date.now() / 1000));
                                        if (this.remaining === 0) {
                                            clearInterval(this.timer);
                                            window.location.reload();
                                        }
                                    }, 1000);
                                }
                             }">
                            <div class="flex items-center gap-2">
                                <span class="flex h-2 w-2 relative">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-oro opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-oro"></span>
                                </span>
                                <span class="text-[10px] font-bold text-oro uppercase tracking-widest">Pase de Captura
                                    Activo</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-oro/70">expira en</span>
                                <span
                                    class="font-mono font-bold text-[11px] px-1.5 py-0.5 rounded transition-all duration-300"
                                    :style="urgent
                                        ? 'color:#fff; background:#dc2626;'
                                        : warning
                                            ? 'color:#78350f; background:#fcd34d;'
                                            : 'color:#c9a227; background:rgba(201,162,39,0.12);'"
                                    :class="urgent ? 'animate-pulse' : ''" x-text="minutes + ':' + seconds">
                                </span>
                            </div>
                        </div>
                        @endif
                        <div class="p-4">
                            {{-- PHP Helpers for Labels --}}
                            @php
                            $selectedCodigoId = $codigo;
                            $codigoFound = collect($topCodigos)->merge($otrosCodigos)->firstWhere('id',
                            $selectedCodigoId);
                            $selectedCodigoName = $codigoFound ? '[' . $codigoFound->code . '] ' .
                            $codigoFound->description : '-- Buscar código --';

                            $selectedMedicoId = $medico_id;
                            $medicoFound = collect($medicos)->firstWhere('id', $selectedMedicoId);
                            $selectedMedicoName = $medicoFound ? ($medicoFound->num_empleado . ' - ' .
                            $medicoFound->fullname) : '-- Buscar médico --';

                            $selectedPeriodoId = $periodo_id;
                            $periodoFound = collect($periodos)->firstWhere('id', $selectedPeriodoId);
                            $selectedPeriodoName = $periodoFound ? ($periodoFound->periodo . ' - ' .
                            $periodoFound->year) : '-- Elija el Periodo --';
                            @endphp

                            @if(\Illuminate\Support\Facades\Cache::get('capture_maintenance', false) &&
                            !auth()->user()->admin())
                            <div
                                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-6 text-center my-4">
                                <svg class="w-12 h-12 text-red-500 mx-auto mb-3 opacity-80" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xs font-black text-red-800 dark:text-red-400 uppercase tracking-widest">
                                    Captura Suspendida</h3>
                                <p class="text-[11px] font-bold text-red-600 dark:text-red-300 mt-2">
                                    Mantenimiento administrativo activo.<br>
                                    Intente más tarde.
                                </p>
                            </div>
                            @else
                            <form wire:submit.prevent="store" class="space-y-4">

                                {{-- Código --}}
                                <div>
                                    <label
                                        class="block text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1.5 ml-1">Código
                                        de Incidencia</label>
                                    <div class="relative" x-data="{
                                        open: false,
                                        search: '',
                                        toggle() { this.open = !this.open },
                                        close() { this.open = false },
                                        select(id) {
                                            $wire.set('codigo', id);
                                            this.close();
                                        }
                                    }" @click.away="close()">
                                        <button @click="toggle()" type="button"
                                            class="flex items-center justify-between w-full h-[42px] px-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-oro transition-all whitespace-nowrap overflow-hidden">
                                            <span class="truncate">{{ $selectedCodigoName }}</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0"
                                                :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-cloak x-transition.opacity
                                            class="absolute z-[100] w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden"
                                            style="display: none;">
                                            <div
                                                class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                                                <input type="text" x-model="search" placeholder="Filtrar códigos..."
                                                    class="w-full h-8 px-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-[11px] font-bold focus:ring-1 focus:ring-oro outline-none">
                                            </div>
                                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                                <div
                                                    class="px-3 py-1 text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                    MÁS USADAS</div>
                                                @foreach($topCodigos as $c)
                                                <div @click="select('{{ $c->id }}')"
                                                    x-show="String({{ json_encode($c->code . ' ' . $c->description) }}).toLowerCase().includes(search.toLowerCase())"
                                                    class="px-3 py-2 rounded-lg cursor-pointer text-[10px] font-bold transition-colors {{ $codigo == $c->id ? 'bg-oro/10 text-oro' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                                    [{{ $c->code }}] {{ $c->description }}
                                                </div>
                                                @endforeach

                                                <div
                                                    class="px-3 py-1 text-[9px] font-black text-gray-400 uppercase tracking-widest mt-2">
                                                    OTRAS INCIDENCIAS</div>
                                                @foreach($otrosCodigos as $c)
                                                <div @click="select('{{ $c->id }}')"
                                                    x-show="String({{ json_encode($c->code . ' ' . $c->description) }}).toLowerCase().includes(search.toLowerCase())"
                                                    class="px-3 py-2 rounded-lg cursor-pointer text-[10px] font-bold transition-colors {{ $codigo == $c->id ? 'bg-oro/10 text-oro' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                                    [{{ $c->code }}] {{ $c->description }}
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @error('codigo') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1 block">{{
                                        $message }}</span> @enderror
                                </div>

                                {{-- Fechas a Aplicar --}}
                                <div class="flex items-center gap-4 p-4" wire:key="flatpickr-container-{{ $dateMode }}"
                                    data-ranges="{{ json_encode($enabledDateRanges) }}" x-data="{
                                        init() {
                                            if(this.$refs.fechaInput && this.$refs.fechaInput._flatpickr) {
                                                this.$refs.fechaInput._flatpickr.destroy();
                                            }
                                            
                                            let allowedRanges = [];
                                            try {
                                                allowedRanges = JSON.parse(this.$el.dataset.ranges || '[]');
                                            } catch (e) {}

                                            window.flatpickr(this.$refs.fechaInput, {
                                                mode: '{{ $dateMode }}',
                                                dateFormat: 'Y-m-d',
                                                showMonths: ('{{ $dateMode }}' === 'range' && window.innerWidth > 640) ? 2 : 1,
                                                locale: { rangeSeparator: '||' },
                                                enable: allowedRanges,
                                                disableMobile: true,
                                                onChange: function(selectedDates, dateStr) {
                                                    $wire.fechas_seleccionadas = dateStr;
                                                }
                                            });

                                            window.addEventListener('reset-calendar', () => {
                                                if(this.$refs.fechaInput && this.$refs.fechaInput._flatpickr) {
                                                    this.$refs.fechaInput._flatpickr.clear();
                                                }
                                            });
                                        }
                                     }">
                                    <div class="flex-1 min-w-0">
                                        <label
                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Fechas
                                            a Aplicar</label>
                                        <div wire:ignore class="relative"
                                            :class="!$wire.codigo ? 'opacity-40 cursor-not-allowed' : ''">
                                            <input x-ref="fechaInput" type="text" placeholder="SELECCIONAR FECHAS"
                                                readonly :disabled="!$wire.codigo"
                                                class="block w-full py-2 px-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#13322B]/30 focus:border-[#13322B] transition-all cursor-pointer shadow-sm outline-none disabled:cursor-not-allowed">
                                        </div>
                                        <p
                                            class="mt-1 ml-1 text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                            {{ $dateMode === 'multiple' ? 'Múltiples días sueltos.' : 'Inicio y fin del
                                            rango.' }}
                                        </p>
                                        @error('fechas_seleccionadas') <span
                                            class="text-red-500 text-[10px] font-bold mt-1 ml-1 block">{{ $message
                                            }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Campos dinámicos según tipo de incidencia --}}
                                <div
                                    wire:key="dynamic-fields-{{ $isIncapacidad ? 'inca' : '' }}-{{ $isVacacional ? 'vac' : '' }}-{{ $isTxt ? 'txt' : '' }}">

                                    {{-- Campos de TXT (código 900) --}}
                                    @if($isTxt)
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-5 mb-4 relative">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-[#13322B] rounded-l-2xl"></div>

                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-[#13322B]/10 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-[#13322B] dark:text-[#e6d194]" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4
                                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                                    Campos de T.X.T</h4>

                                                <div class="space-y-4">
                                                    <div>
                                                        <label
                                                            class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Sustituto
                                                            (quién cubrió)</label>
                                                        <input type="text" wire:model="cobertura_txt"
                                                            placeholder="Nombre completo..."
                                                            class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-[#13322B]/30 outline-none transition-all">
                                                        @error('cobertura_txt') <span
                                                            class="text-red-500 text-[10px] font-bold mt-1 ml-1 block">{{
                                                            $message }}</span> @enderror
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Autorizó
                                                            Cambio</label>
                                                        <input type="text" wire:model="autoriza_txt"
                                                            placeholder="Nombre del responsable..."
                                                            class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-[#13322B]/30 outline-none transition-all">
                                                        @error('autoriza_txt') <span
                                                            class="text-red-500 text-[10px] font-bold mt-1 ml-1 block">{{
                                                            $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Campos de Incapacidad --}}
                                    @if($isIncapacidad)
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-5 mb-4 relative">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-[#9b2247] rounded-l-2xl"></div>

                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-[#9b2247]/10 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-[#9b2247]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4
                                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                                    Campos de Incapacidad</h4>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div x-data="{
                                                        init() {
                                                            window.flatpickr(this.$refs.expedidaInput, {
                                                                dateFormat: 'Y-m-d',
                                                                disableMobile: true,
                                                                onChange: function(selectedDates, dateStr) { $wire.fecha_expedida = dateStr; }
                                                            });
                                                        }
                                                    }">
                                                        <label
                                                            class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Fecha
                                                            Expedida</label>
                                                        <div wire:ignore>
                                                            <input x-ref="expedidaInput" type="text"
                                                                placeholder="Seleccionar" readonly
                                                                class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-[#9b2247]/30 outline-none transition-all cursor-pointer">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">No.
                                                            Licencia Médica</label>
                                                        <input type="text" wire:model="num_licencia"
                                                            placeholder="00000000"
                                                            class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-[#9b2247]/30 outline-none transition-all">
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <label
                                                        class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Diagnóstico
                                                        / Motivo</label>
                                                    <input type="text" wire:model="diagnostico"
                                                        placeholder="Descripción breve..."
                                                        class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-[#9b2247]/30 outline-none transition-all">
                                                </div>

                                                <div class="mt-4">
                                                    <label
                                                        class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Médico
                                                        Expedidor</label>
                                                    <div class="relative" x-data="{
                                                        open: false,
                                                        search: '',
                                                        select(id) { 
                                                            $wire.set('medico_id', id); 
                                                            this.open = false; 
                                                        }
                                                    }" @click.outside="open = false">
                                                        <button @click.stop="open = !open" type="button"
                                                            class="flex items-center justify-between w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-[#9b2247]/30 outline-none transition-all">
                                                            <span class="block truncate">{{ $selectedMedicoName
                                                                }}</span>
                                                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-200"
                                                                :class="open ? 'rotate-180' : ''" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" x-cloak x-transition.opacity
                                                            class="absolute z-[100] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden">
                                                            <div
                                                                class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                                                                <input type="text" x-model="search" @click.stop
                                                                    placeholder="Filtrar médicos..."
                                                                    class="w-full h-8 px-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-[10px] font-bold outline-none ring-1 ring-gray-200 dark:ring-gray-700 focus:ring-[#9b2247]">
                                                            </div>
                                                            <div
                                                                class="max-h-52 overflow-y-auto p-1.5 custom-scrollbar">
                                                                @foreach($medicos as $medico)
                                                                <div @click.stop="select('{{ $medico->id }}')"
                                                                    x-show="String({{ json_encode($medico->fullname) }}).toLowerCase().includes(search.toLowerCase()) || '{{ $medico->num_empleado }}'.includes(search)"
                                                                    class="px-3 py-2 rounded-lg cursor-pointer text-[10px] font-bold transition-all {{ $medico_id == $medico->id ? 'bg-[#9b2247]/10 text-[#9b2247]' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} mb-1 last:mb-0">
                                                                    <div class="flex items-center justify-between">
                                                                        <span>{{ $medico->fullname }}</span>
                                                                        <span class="text-[8px] opacity-50">{{
                                                                            $medico->num_empleado }}</span>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                                @if(count($medicos) === 0)
                                                                <div
                                                                    class="p-4 text-center text-[10px] text-gray-400 italic">
                                                                    No hay médicos registrados</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Campos de Vacaciones --}}
                                    @if($isVacacional)
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-5 mb-4 relative">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-[#c9a227] rounded-l-2xl"></div>

                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-[#c9a227]/10 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-[#c9a227]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.99 7.99 0 0120 13a7.966 7.966 0 01-2.343 5.657z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.879 16.121A3 3 0 1012.015 11L11 14l-0.657 2.121z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4
                                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                                    Campos de Vacaciones</h4>

                                                <div>
                                                    <label
                                                        class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Periodo
                                                        a Disfrutar</label>
                                                    <div class="relative" x-data="{
                                                        open: false,
                                                        toggle() { this.open = !this.open },
                                                        close() { this.open = false },
                                                        select(id) { $wire.set('periodo_id', id); this.close(); }
                                                    }" @click.away="close()">
                                                        <button @click="toggle()" type="button"
                                                            class="flex items-center justify-between w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-[#c9a227]/30 outline-none transition-all">
                                                            <span class="block truncate">{{ $selectedPeriodoName
                                                                }}</span>
                                                            <svg class="w-3 h-3 text-gray-400" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" x-cloak x-transition.opacity
                                                            class="absolute z-[100] w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden">
                                                            <div class="max-h-40 overflow-y-auto p-1.5">
                                                                @foreach($periodos as $p)
                                                                <div @click="select('{{ $p->id }}')"
                                                                    class="px-3 py-1.5 rounded-lg cursor-pointer text-[10px] font-bold transition-colors {{ $periodo_id == $p->id ? 'bg-[#c9a227]/10 text-[#c9a227]' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                                                    {{ $p->periodo }} - {{ $p->year }}
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>{{-- fin wire:key dynamic-fields --}}

                                {{-- Botón guardar --}}
                                <div x-data="{ 
                                        isError: false,
                                        triggerError() {
                                            this.isError = true;
                                            setTimeout(() => { this.isError = false; }, 800);
                                        }
                                     }" x-on:island-notif.window="if($event.detail.type === 'error') triggerError()"
                                    x-on:topbar-end.window="if($event.detail === 'error') triggerError()">

                                    <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50">
                                        <button type="submit" :disabled="!$wire.codigo"
                                            class="w-full text-white font-black uppercase py-4 px-6 rounded-2xl text-sm tracking-[0.2em] transition-all duration-300 shadow-lg active:scale-[0.98] will-change-transform flex items-center justify-center gap-3 overflow-hidden group relative disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none"
                                            :class="isError 
                                            ? 'bg-red-600 hover:bg-red-700 shadow-red-200 dark:shadow-red-900/20 animate-shake-button' 
                                            : 'bg-[#13322B] hover:bg-[#0a1f1a] shadow-[#13322B]/20 dark:shadow-black/40 hover:shadow-xl hover:-translate-y-0.5'">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                                            </div>

                                            <span x-show="!isError" class="flex items-center gap-2">
                                                <svg class="w-5 h-5 transition-transform group-hover:scale-110"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Guardar Incidencia
                                            </span>

                                            <span x-show="isError" x-cloak class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                Revisar Datos
                                            </span>
                                        </button>
                                    </div>
                            </form>
                            @endif

                            <style>
                                @keyframes shimmer {
                                    100% {
                                        transform: translateX(100%);
                                    }
                                }

                                .animate-shimmer {
                                    animation: shimmer 1.5s infinite;
                                }

                                @keyframes shake-button {

                                    10%,
                                    90% {
                                        transform: translate3d(-1px, 0, 0);
                                    }

                                    20%,
                                    80% {
                                        transform: translate3d(2px, 0, 0);
                                    }

                                    30%,
                                    50%,
                                    70% {
                                        transform: translate3d(-4px, 0, 0);
                                    }

                                    40%,
                                    60% {
                                        transform: translate3d(4px, 0, 0);
                                    }
                                }

                                .animate-shake-button {
                                    animation: shake-button 0.5s cubic-bezier(.36, .07, .19, .97) both;
                                }
                            </style>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Tabla de historial --}}
            <div class="flex-1 w-full xl:w-[60%] min-w-0">

                {{-- Bloque Unificado de Empleado e Historial --}}
                <div
                    class="bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700 overflow-hidden">

                    {{-- Cabecera: Información del Trabajador --}}
                    <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700 relative">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#9b2247]"></div>
                        <div class="space-y-2 mt-2">
                            {{-- Línea 1: NUMERO DE EMPLEADO - NOMBRE --}}
                            <div>
                                <h2 class="text-xl text-[#333333] dark:text-gray-100 leading-none">
                                    <span
                                        class="font-bold border-r border-gray-300 dark:border-gray-600 pr-3 mr-2 text-[#9b2247] dark:text-[#e6d194]">{{
                                        $employee->num_empleado }}</span>
                                    <span class="font-bold">{{ strtoupper($employee->name . ' ' .
                                        $employee->father_lastname . ' ' . $employee->mother_lastname) }}</span>
                                </h2>
                            </div>

                            {{-- Línea 2: CODIGO CENTRO - DESCRIPCION --}}
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold">{{ $employee->department->code ?? 'N/A'
                                        }}</span>
                                    <span class="text-gray-300 dark:text-gray-600 mx-1">|</span>
                                    {{ $employee->department->description ?? 'N/A' }}
                                </p>
                            </div>

                            {{-- Línea 3: PUESTO --}}
                            <div>
                                <p class="text-[#13322B] dark:text-[#e6d194] font-semibold text-xs tracking-wide">
                                    {{ $employee->puesto->puesto ?? 'SIN PUESTO ASIGNADO' }}
                                </p>
                            </div>

                            {{-- Línea 4: HORARIO - TURNO --}}
                            <div>
                                <p
                                    class="text-[11px] font-medium text-gray-400 uppercase tracking-widest flex items-center justify-center gap-2">
                                    <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $employee->horario->horario ?? 'N/A' }}
                                    <span class="text-gray-200">|</span>
                                    <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4"></path>
                                    </svg>
                                    {{ $employee->jornada->jornada ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto hidden xl:block">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                    <th
                                        class="px-4 py-4 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] text-center">
                                        Qna</th>
                                    <th
                                        class="px-4 py-4 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">
                                        Cód.</th>
                                    <th
                                        class="px-4 py-4 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] text-center">
                                        Periodo</th>
                                    <th
                                        class="px-4 py-4 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] text-center">
                                        Días</th>
                                    <th
                                        class="px-4 py-4 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">
                                        Capturó</th>
                                    <th
                                        class="px-4 py-4 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] text-right">
                                        Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                @forelse($incidencias as $incidencia)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition-colors group"
                                    wire:key="inc-{{ $incidencia->id }}">
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-[#13322B]/10 text-[#13322B] dark:bg-[#e6d194]/10 dark:text-[#e6d194] uppercase">
                                            {{ str_pad($incidencia->qna->qna ?? '', 2, '0', STR_PAD_LEFT) }}-{{
                                            $incidencia->qna->year ?? '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-900 dark:text-white">{{
                                                $incidencia->codigo->code ?? 'N/A' }}</span>
                                            <span
                                                class="text-[9px] font-bold text-gray-400 dark:text-gray-500 truncate max-w-[120px]"
                                                title="{{ $incidencia->codigo->description ?? '' }}">
                                                {{ $incidencia->codigo->description ?? '' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="flex items-center gap-1.5 text-[10px] font-black text-gray-700 dark:text-gray-300">
                                                <span>{{
                                                    \Carbon\Carbon::parse($incidencia->fecha_inicio)->format('d/m/Y')
                                                    }}</span>
                                                <span class="text-gray-300 dark:text-gray-600">→</span>
                                                <span>{{
                                                    \Carbon\Carbon::parse($incidencia->fecha_final)->format('d/m/Y')
                                                    }}</span>
                                            </div>
                                            @if($incidencia->periodo)
                                            <span
                                                class="text-[9px] font-black text-[#9b2247] dark:text-[#e6d194] mt-0.5 uppercase tracking-tighter">
                                                Per. {{ $incidencia->periodo->periodo }}-{{
                                                $incidencia->periodo->year }}
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-xs font-black text-[#9b2247] dark:text-[#e6d194]">
                                            {{ $incidencia->total_dias }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{
                                                $incidencia->capturado_por ?? '—' }}</span>
                                            <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500">
                                                {{ $incidencia->created_at ?
                                                $incidencia->created_at->format('d/m/y H:i') : '—' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        @php
                                        $isMaintenance =
                                        \Illuminate\Support\Facades\Cache::get('capture_maintenance', false) &&
                                        !auth()->user()->admin();
                                        $qnaActiva = $incidencia->qna && $incidencia->qna->active == '1';
                                        $tienePermiso = $qnaActiva || (!$isMaintenance &&
                                        auth()->user()->canCaptureInClosedQna($incidencia->qna_id));
                                        @endphp
                                        @if($tienePermiso && !$isMaintenance)
                                        <button
                                            x-on:click="window.Swal.fire({
                                                        title: '¿Eliminar incidencia?',
                                                        text: 'Esta acción eliminará esta incidencia y cualquier otra ligada a la misma captura.',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#ef4444',
                                                        cancelButtonColor: '#6b7280',
                                                        confirmButtonText: 'Sí, eliminar',
                                                        cancelButtonText: 'Cancelar'
                                                    }).then((result) => { if (result.isConfirmed) { $wire.delete('{{ $incidencia->token }}'); } })"
                                            class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        @else
                                        <span
                                            class="text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase italic">Cerrada</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-20 text-center">
                                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Sin
                                            historial de captura</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- VISTA MÓVIL (TARJETAS) --}}
                    <div
                        class="xl:hidden flex flex-col gap-4 p-4 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="px-2 pb-2 flex justify-between items-center">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Capturas
                                Recientes</h3>
                            <span
                                class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-tighter">Total:
                                {{ count($incidencias) }}</span>
                        </div>

                        @forelse($incidencias as $incidencia)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group"
                            wire:key="inc-mob-{{ $incidencia->id }}">
                            <div class="absolute top-0 left-0 w-1 h-full bg-[#13322B]"></div>

                            <div class="flex justify-between items-start mb-3">
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Quincena</span>
                                    <span class="text-xs font-black text-[#13322B] dark:text-[#e6d194]">
                                        {{ str_pad($incidencia->qna->qna ?? '', 2, '0', STR_PAD_LEFT) }}-{{
                                        $incidencia->qna->year ?? '' }}
                                    </span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Código</span>
                                    <span class="text-xs font-black text-gray-900 dark:text-white">{{
                                        $incidencia->codigo->code ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div
                                class="bg-gray-50/80 dark:bg-gray-900/50 rounded-xl p-3 mb-3 border border-gray-100 dark:border-gray-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-tight">Periodo</span>
                                        <div
                                            class="flex items-center gap-1.5 text-[10px] font-black text-gray-700 dark:text-gray-300">
                                            <span>{{
                                                \Carbon\Carbon::parse($incidencia->fecha_inicio)->format('d/m/y')
                                                }}</span>
                                            <span class="text-gray-300">→</span>
                                            <span>{{
                                                \Carbon\Carbon::parse($incidencia->fecha_final)->format('d/m/y')
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="text-[9px] font-black text-gray-400 uppercase tracking-tight block">Días</span>
                                        <span class="text-sm font-black text-[#9b2247] dark:text-[#e6d194]">{{
                                            $incidencia->total_dias }}</span>
                                    </div>
                                </div>
                                @if($incidencia->periodo)
                                <div
                                    class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                                    <svg class="w-3 h-3 text-[#9b2247]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
                                    </svg>
                                    <span
                                        class="text-[9px] font-black text-[#9b2247] dark:text-[#e6d194] uppercase tracking-widest text-xs">
                                        Per. {{ $incidencia->periodo->periodo }}-{{ $incidencia->periodo->year
                                        }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Capturó:
                                        <span class="text-gray-600 dark:text-gray-300">{{
                                            $incidencia->capturado_por ?? '—' }}</span>
                                    </span>
                                </div>
                                <div>
                                    @php
                                    $isMaintenance =
                                    \Illuminate\Support\Facades\Cache::get('capture_maintenance', false) &&
                                    !auth()->user()->admin();
                                    $qnaActiva = $incidencia->qna && $incidencia->qna->active == '1';
                                    $tienePermiso = $qnaActiva || (!$isMaintenance &&
                                    auth()->user()->canCaptureInClosedQna($incidencia->qna_id));
                                    @endphp
                                    @if($tienePermiso && !$isMaintenance)
                                    <button
                                        x-on:click="window.Swal.fire({
                                                    title: '¿Eliminar?',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#ef4444',
                                                    confirmButtonText: 'Eliminar'
                                                }).then((result) => { if (result.isConfirmed) { $wire.delete('{{ $incidencia->token }}'); } })"
                                        class="p-2 bg-red-50 dark:bg-red-950/30 text-red-500 rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    @else
                                    <span
                                        class="text-[9px] font-black text-gray-300 dark:text-gray-600 uppercase italic">Cerrada</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div
                            class="py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block px-4">Sin
                                capturas recientes</span>
                        </div>
                        @endforelse
                    </div> {{-- fin mobile view --}}
                </div> {{-- fin history container --}}
            </div> {{-- fin right column --}}
        </div> {{-- fin flex-row container --}}
    </div> {{-- fin max-w-full --}}
</div> {{-- fin py-6 --}}

<script>
    document.addEventListener('livewire:init', () => {
        window.addEventListener('storage', (event) => {
            if (event.key === 'biometrico_refresh') {
                Livewire.dispatch('refreshIncidencias');
            }
        });
    });
</script>

{{-- MODAL DE BÚSQUEDA GLOBAL --}}
<div x-show="openModal" x-cloak
    class="fixed inset-0 z-[5000] flex items-start justify-center pt-10 sm:pt-20 px-4 sm:px-6"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60" @click="openModal = false"></div>

    {{-- Modal Card --}}
    <div class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] border border-gray-100 dark:border-gray-700 overflow-hidden transform"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0">

        {{-- Header del Modal --}}
        <div
            class="px-8 py-6 border-b border-gray-50 dark:border-gray-700/50 flex items-center justify-between bg-white dark:bg-gray-800">
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-oro uppercase tracking-[0.2em]">Buscador de Personal</span>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Seleccione un Empleado</h2>
            </div>
            <button @click="openModal = false"
                class="p-2 hover:bg-gray-50 dark:hover:bg-gray-900 rounded-full transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Cuerpo del Modal / Input --}}
        <div class="p-8">
            <div class="relative group">
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-oro transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input x-ref="modalSearchInput" x-model="query" @input="search()" type="text"
                    placeholder="Escriba el nombre o número de empleado..."
                    class="w-full h-14 pl-12 pr-4 bg-gray-50/50 dark:bg-gray-900 border-2 border-transparent focus:border-oro/30 focus:bg-white dark:focus:bg-gray-950 rounded-2xl text-sm font-bold text-gray-700 dark:text-gray-200 outline-none transition-all shadow-inner">

                <div x-show="loading" class="absolute inset-y-0 right-4 flex items-center">
                    <div class="w-5 h-5 border-2 border-oro border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>

            {{-- Área de Resultados --}}
            <div class="mt-6">
                <div x-show="query.length < 2" class="text-center py-10">
                    <div
                        class="w-16 h-16 bg-gray-50 dark:bg-gray-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Ingrese al menos 2 caracteres
                    </p>
                </div>

                <div x-show="query.length >= 2 && results.length === 0 && !loading" class="text-center py-10">
                    <p class="text-xs text-oro font-black uppercase tracking-widest">No se encontraron resultados</p>
                </div>

                <div x-show="results.length > 0" class="space-y-2 max-h-[45vh] overflow-y-auto custom-scrollbar pr-2">
                    <template x-for="emp in results" :key="emp.id">
                        <div @click="select(emp)"
                            class="p-4 bg-gray-50/50 dark:bg-gray-900/30 hover:bg-oro/5 dark:hover:bg-oro/10 border border-transparent hover:border-oro/20 rounded-2xl cursor-pointer transition-all flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center font-black text-[#13322B] dark:text-oro shadow-sm group-hover:scale-110 transition-transform"
                                x-text="emp.id"></div>
                            <div class="flex-1">
                                <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5"
                                    x-text="emp.department || 'Sin Departamento'"></div>
                                <div class="text-sm font-bold text-gray-700 dark:text-gray-200 transition-colors"
                                    x-text="emp.label"></div>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-oro group-hover:translate-x-1 transition-all"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Footer del Modal --}}
        <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900/80 flex justify-between items-center">
            <span class="text-[10px] text-gray-400 font-bold uppercase flex items-center gap-2">
                <kbd
                    class="px-1.5 py-0.5 rounded border border-gray-300 dark:border-gray-600 font-sans text-[8px]">ESC</kbd>
                para cerrar
            </span>
            <div class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-oro"></span>
                <span class="text-[9px] font-black text-oro uppercase tracking-[0.2em]"
                    x-text="results.length + ' resultados'"></span>
            </div>
        </div>
    </div>
</div>
</div> {{-- fin root div --}}