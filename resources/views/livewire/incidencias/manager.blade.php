<div>
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Layout principal: formulario fijo + tabla --}}
            <div class="grid gap-4 items-start" style="grid-template-columns: 40% 60%">

                {{-- COLUMNA IZQUIERDA: Formulario de captura --}}
                <div>



                    {{-- Formulario de captura --}}
                    <div
                        class="bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700 sticky top-4 overflow-hidden relative">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#13322B] dark:bg-[#e6d194]"></div>
                        <div
                            class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                            <h3
                                class="text-sm font-bold text-[#13322B] dark:text-gray-200 uppercase tracking-wide flex items-center gap-2">
                                Capturar Incidencia
                            </h3>
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
                            {{-- Buscador de empleado integrado --}}
                            <div class="mb-6 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700"
                                wire:ignore x-data="{
                                    query: '',
                                    results: [],
                                    open: false,
                                    loading: false,
                                    debounceTimer: null,
                                    search() {
                                        clearTimeout(this.debounceTimer);
                                        if (this.query.length < 2) { this.results = []; this.open = false; return; }
                                        this.loading = true;
                                        this.debounceTimer = setTimeout(() => {
                                            fetch('/api/employees/search?q=' + encodeURIComponent(this.query), {
                                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                            })
                                            .then(r => r.json())
                                            .then(data => { this.results = data; this.open = data.length > 0; this.loading = false; })
                                            .catch(() => { this.loading = false; });
                                        }, 300);
                                    },
                                    select(emp) {
                                        this.open = false;
                                        this.query = emp.label;
                                        $wire.cambiarEmpleado(emp.id);
                                    }
                                 }" x-on:click.outside="open = false">
                                <label
                                    class="block text-[10px] text-gray-500 font-black uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <svg class="w-3 h-3 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Buscador de Personal
                                </label>
                                <div class="relative">
                                    <input type="text" x-model="query" x-on:input="search()"
                                        x-on:focus="if(query.length >= 2) open = true"
                                        placeholder="Nombre o No. de Empleado..." spellcheck="false"
                                        class="block w-full text-xs border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 pr-8 focus:border-oro focus:ring-1 focus:ring-oro outline-none transition-all shadow-inner bg-white dark:bg-gray-950 dark:text-gray-300">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <span x-show="loading"
                                            class="text-oro text-[10px] animate-pulse mr-1">...</span>
                                        <button x-show="query.length > 0"
                                            x-on:click="query = ''; results = []; open = false;" type="button"
                                            class="text-gray-300 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none transition-colors mt-0.5">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="open" x-transition
                                        class="absolute z-50 left-0 top-full mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-2xl max-h-56 overflow-y-auto">
                                        <template x-for="emp in results" :key="emp.id">
                                            <div x-on:click="select(emp)" x-text="emp.label"
                                                class="px-4 py-2 text-xs cursor-pointer hover:bg-guinda hover:text-white border-b border-gray-50 last:border-0 font-medium transition-colors">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            @if(\Illuminate\Support\Facades\Cache::get('capture_maintenance', false) &&
                            !auth()->user()->admin())
                            <div
                                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 text-center my-4">
                                <svg class="w-12 h-12 text-red-500 mx-auto mb-3 opacity-80" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-sm font-bold text-red-800 dark:text-red-400">Captura Suspendida
                                    Temporalmente</h3>
                                <p class="text-xs text-red-600 dark:text-red-300 mt-2">
                                    El sistema se encuentra en modo de mantenimiento administrativo.<br>
                                    Por el momento no se pueden capturar nuevas incidencias. Intente más tarde.
                                </p>
                            </div>
                            @else
                            <form wire:submit.prevent="store">

                                {{-- Código --}}
                                <div class="mb-3">
                                    <label
                                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Código
                                        de Incidencia</label>
                                    <div wire:ignore>
                                        <select x-data="{
                                            tom: null,
                                            init() {
                                                this.tom = new window.TomSelect(this.$el, {
                                                    create: false,
                                                    placeholder: '-- Buscar código --',
                                                    searchField: ['text'],
                                                    maxOptions: 200,
                                                });
                                                this.tom.on('change', (value) => {
                                                    @this.set('codigo', value);
                                                });
                                            }
                                        }"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm text-sm">
                                            <option value="">-- Buscar código --</option>
                                            @foreach($codigos as $c)
                                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('codigo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                {{-- Fechas con Flatpickr --}}
                                <div class="mb-3" wire:key="flatpickr-container-{{ $dateMode }}" x-data="{
                                    init() {
                                        if(this.$refs.fechaInput && this.$refs.fechaInput._flatpickr) {
                                            this.$refs.fechaInput._flatpickr.destroy();
                                        }
                                        window.flatpickr(this.$refs.fechaInput, {
                                            mode: '{{ $dateMode }}',
                                            dateFormat: 'Y-m-d',
                                            showMonths: '{{ $dateMode }}' === 'range' ? 2 : 1,
                                            locale: { rangeSeparator: '||' },
                                            onChange: function(selectedDates, dateStr) {
                                                $wire.fechas_seleccionadas = dateStr;
                                            }
                                        });
                                    }
                                }">
                                    <label
                                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fechas a
                                        Aplicar</label>
                                    <div wire:ignore>
                                        <input x-ref="fechaInput" type="text" placeholder="Seleccione la(s) fecha(s)"
                                            readonly
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-oro focus:ring-oro text-sm bg-gray-50 dark:bg-gray-900 dark:text-gray-100 cursor-pointer">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        {{ $dateMode === 'multiple' ? 'Múltiples días sueltos.' : 'Seleccione inicio y
                                        fin del rango.' }}
                                    </p>
                                    @error('fechas_seleccionadas') <span class="text-red-500 text-xs">{{ $message
                                        }}</span> @enderror
                                </div>

                                {{-- Campos dinámicos según tipo de incidencia --}}
                                <div
                                    wire:key="dynamic-fields-{{ $isIncapacidad ? 'inca' : '' }}-{{ $isVacacional ? 'vac' : '' }}-{{ $isTxt ? 'txt' : '' }}">

                                    {{-- Campos de TXT (código 900) --}}
                                    @if($isTxt)
                                    <div
                                        class="bg-verde-50/30 dark:bg-[#13322b]/20 p-4 rounded-xl mb-3 border border-verde/20 dark:border-[#13322B]">
                                        <h4
                                            class="text-xs font-black text-verde-dark dark:text-[#e6d194] mb-3 uppercase tracking-wider flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-verde dark:bg-[#e6d194]"></span>
                                            Detalle T.X.T (Tiempo por Tiempo)
                                        </h4>

                                        <div class="mb-2">
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sustituto
                                                (quién lo cubrió)</label>
                                            <input type="text" wire:model="cobertura_txt"
                                                placeholder="Nombre del sustituto" spellcheck="false"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100 shadow-sm py-1.5 text-sm">
                                            @error('cobertura_txt') <span class="text-red-500 text-xs">{{ $message
                                                }}</span> @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Autorizó
                                                el cambio de guardia</label>
                                            <input type="text" wire:model="autoriza_txt"
                                                placeholder="Nombre de quien autorizó" spellcheck="false"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100 shadow-sm py-1.5 text-sm">
                                            @error('autoriza_txt') <span class="text-red-500 text-xs">{{ $message
                                                }}</span> @enderror
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Campos de Incapacidad --}}
                                    @if($isIncapacidad)
                                    <div
                                        class="bg-guinda-50 dark:bg-gray-800 p-3 rounded-md mb-3 border border-guinda-100 dark:border-gray-700">
                                        <h4 class="text-xs font-semibold text-guinda-800 dark:text-gray-300 mb-2">
                                            Detalles Incapacidad</h4>

                                        <div class="mb-2" x-data="{
                                        init() {
                                            window.flatpickr(this.$refs.expedidaInput, {
                                                dateFormat: 'Y-m-d',
                                                onChange: function(selectedDates, dateStr) {
                                                    $wire.fecha_expedida = dateStr;
                                                }
                                            });
                                        }
                                    }">
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha
                                                Expedida</label>
                                            <div wire:ignore>
                                                <input x-ref="expedidaInput" type="text" placeholder="Seleccionar"
                                                    readonly
                                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm py-1.5 text-sm bg-gray-50 dark:bg-gray-900 dark:text-gray-100 cursor-pointer">
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">No.
                                                Licencia Médica</label>
                                            <input type="text" wire:model="num_licencia" spellcheck="false"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100 shadow-sm py-1.5 text-sm">
                                        </div>

                                        <div class="mb-2">
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Diagnóstico</label>
                                            <input type="text" wire:model="diagnostico" spellcheck="false"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100 shadow-sm py-1.5 text-sm">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Médico
                                                Expedidor</label>
                                            <div wire:ignore>
                                                <select x-data="{
                                                tom: null,
                                                init() {
                                                    this.tom = new window.TomSelect(this.$el, {
                                                        create: false,
                                                        placeholder: '-- Buscar médico --'
                                                    });
                                                    this.tom.on('change', (value) => {
                                                        @this.set('medico_id', value);
                                                    });
                                                }
                                            }" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm py-1.5 text-sm">
                                                    <option value="">-- Buscar médico --</option>
                                                    @foreach($medicos as $medico)
                                                    <option value="{{ $medico->id }}">{{ $medico->num_empleado }} - {{
                                                        $medico->fullname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('medico_id') <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Campos de Vacaciones --}}
                                    @if($isVacacional)
                                    <div
                                        class="bg-yellow-50 dark:bg-gray-800 p-3 rounded-md mb-3 border border-yellow-100 dark:border-gray-700">
                                        <h4 class="text-xs font-semibold text-yellow-800 dark:text-gray-300 mb-2">
                                            Detalle Vacaciones</h4>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Periodo
                                            a Disfrutar</label>
                                        <select wire:model="periodo_id"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-oro focus:ring-oro text-sm">
                                            <option value="">-- Elija el Periodo --</option>
                                            @foreach($periodos as $p)
                                            <option value="{{ $p->id }}">{{ $p->periodo }} - {{ $p->year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                </div>{{-- fin wire:key dynamic-fields --}}

                                {{-- Botón guardar --}}
                                <button type="submit"
                                    class="w-full bg-[#13322B] hover:bg-[#0a1f1a] text-white font-bold uppercase py-2.5 px-4 rounded text-sm transition mt-4 focus:ring-2 focus:ring-offset-2 focus:ring-[#13322B]">
                                    Guardar Incidencia
                                </button>

                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: Tabla de historial --}}
                <div class="flex-1 min-w-0">

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
                                        <span class="font-semibold">{{ $employee->department->code ?? 'N/A' }}</span>
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

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center whitespace-nowrap">
                                            Qna</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Código</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center whitespace-nowrap">
                                            Inicio</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center whitespace-nowrap">
                                            Fin</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center whitespace-nowrap">
                                            Días</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center whitespace-nowrap">
                                            Período Vac.</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                            Capturó</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center whitespace-nowrap">
                                            Fecha Captura</th>
                                        <th
                                            class="px-3 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-right whitespace-nowrap">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($incidencias as $incidencia)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                                        wire:key="inc-{{ $incidencia->id }}">
                                        <td
                                            class="px-3 py-2 text-center text-xs font-mono text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            {{ str_pad($incidencia->qna->qna ?? '', 2, '0', STR_PAD_LEFT) }}-{{
                                            $incidencia->qna->year ?? '' }}
                                        </td>
                                        <td
                                            class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ $incidencia->codigo->code ?? 'N/A' }}
                                        </td>
                                        <td
                                            class="px-3 py-2 text-center text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($incidencia->fecha_inicio)->format('d/m/Y') }}
                                        </td>
                                        <td
                                            class="px-3 py-2 text-center text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($incidencia->fecha_final)->format('d/m/Y') }}
                                        </td>
                                        <td
                                            class="px-3 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            {{ $incidencia->total_dias }}
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-600 dark:text-gray-400">
                                            @if($incidencia->periodo)
                                            <span
                                                class="inline-block bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-500 text-xs font-semibold px-2 py-0.5 rounded">
                                                {{ $incidencia->periodo->periodo }}-{{ $incidencia->periodo->year }}
                                            </span>
                                            @else
                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 max-w-[120px] truncate"
                                            title="{{ $incidencia->capturado_por }}">
                                            {{ $incidencia->capturado_por ?? '—' }}
                                        </td>
                                        <td
                                            class="px-3 py-2 text-center text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                            @if($incidencia->fecha_capturado)
                                            {{ \Carbon\Carbon::parse($incidencia->fecha_capturado)->format('d/m/Y') }}
                                            @elseif($incidencia->created_at)
                                            {{ $incidencia->created_at->format('d/m/Y') }}
                                            @else
                                            —
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-right text-xs">
                                            @php
                                            $isMaintenance =
                                            \Illuminate\Support\Facades\Cache::get('capture_maintenance', false) &&
                                            !auth()->user()->admin();
                                            $qnaActiva = $incidencia->qna && $incidencia->qna->active == '1';
                                            $tienePermiso = $qnaActiva || (!$isMaintenance &&
                                            auth()->user()->canCaptureInClosedQna($incidencia->qna_id));
                                            @endphp
                                            @if($tienePermiso && !$isMaintenance)
                                            <button x-on:click="window.Swal.fire({
                                                        title: '¿Eliminar incidencia?',
                                                        text: 'Esta acción eliminará esta incidencia y cualquier otra ligada a la misma captura.',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#ef4444',
                                                        cancelButtonColor: '#6b7280',
                                                        confirmButtonText: 'Sí, eliminar',
                                                        cancelButtonText: 'Cancelar'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $wire.delete('{{ $incidencia->token }}');
                                                        }
                                                    })" class="text-red-500 hover:text-red-400 font-medium">
                                                Eliminar
                                            </button>
                                            @elseif($isMaintenance && $qnaActiva)
                                            <span class="text-red-400 dark:text-red-500 italic"
                                                title="Eliminación bloqueada por mantenimiento">Mantenimiento</span>
                                            @else
                                            <span class="text-gray-300 dark:text-gray-600 italic">Cerrada</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-10 text-center text-gray-400 text-sm italic">
                                            No hay incidencias capturadas para este empleado.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- fin grid --}}

        </div>
    </div>
</div>