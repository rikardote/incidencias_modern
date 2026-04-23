{{-- SPA Incidencias Manager - Part 1: Search modal, Profile, Stepper, Step 1 (Codes) --}}
<div x-data="{
    query: '', results: [], openModal: false, loading: false, debounceTimer: null,
    search() {
        clearTimeout(this.debounceTimer);
        if (this.query.length < 2) { this.results = []; return; }
        this.loading = true;
        this.debounceTimer = setTimeout(() => {
            fetch('/api/employees/search?q=' + encodeURIComponent(this.query), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).then(data => { this.results = data; this.loading = false; })
            .catch(() => { this.loading = false; });
        }, 300);
    },
    select(emp) { this.openModal = false; this.query = ''; this.results = []; $wire.cambiarEmpleado(emp.id); },
    expandedCat: null
}" x-on:keydown.escape="openModal = false" x-on:keydown.ctrl.k.window.prevent="openModal = true; $nextTick(() => $refs.modalSearchInput?.focus())">

<div class="py-6 min-h-screen">
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ═══ TWO-COLUMN DESKTOP LAYOUT ═══ --}}
    <div class="flex flex-col lg:flex-row items-start gap-6">

    {{-- ═══ LEFT COLUMN: CAPTURE FORM ═══ --}}
    <div class="w-full lg:w-[42%] lg:sticky lg:top-4 text-sm">

    {{-- Capture Pass Banner --}}
    @if(auth()->user()->canCaptureInClosedQna())
    @php $activeException = auth()->user()->captureExceptions()->where('expires_at', '>', now())->first();
    $remainingSecondsFromServer = $activeException ? max(0, $activeException->expires_at->timestamp - now()->timestamp) : 0; @endphp
    <div class="mb-4 bg-oro/10 border border-oro/20 rounded-xl px-5 py-3 flex items-center justify-between"
        x-data="{ serverRemaining: {{ $remainingSecondsFromServer }}, remaining: 0, startTime: 0, timer: null,
            get minutes() { return String(Math.floor(this.remaining / 60)).padStart(2, '0') },
            get seconds() { return String(this.remaining % 60).padStart(2, '0') },
            get urgent() { return this.remaining <= 30 },
            init() { this.remaining = this.serverRemaining; this.startTime = Math.floor(Date.now() / 1000);
                if (this.remaining > 0) { this.timer = setInterval(() => { let now = Math.floor(Date.now() / 1000); this.remaining = Math.max(0, this.serverRemaining - (now - this.startTime)); if (this.remaining === 0) { clearInterval(this.timer); window.location.reload(); } }, 1000); }
            } }">
        <div class="flex items-center gap-2">
            <span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-oro opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-oro"></span></span>
            <span class="text-[10px] font-bold text-oro uppercase tracking-widest">Pase de Captura Activo</span>
        </div>
        <span class="font-mono font-bold text-[11px] px-2 py-0.5 rounded" :class="urgent ? 'bg-red-600 text-white animate-pulse' : 'bg-oro/10 text-oro'" x-text="minutes + ':' + seconds"></span>
    </div>
    @endif

    {{-- ═══ MAIN CAPTURE CARD ═══ --}}
    @if(!auth()->user()->canCapture())
    <div class="bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center mb-4">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">SOLO CONSULTA</h3>
        <p class="text-[11px] font-bold text-gray-400 mt-2 italic">Permisos de lectura. La captura está deshabilitada.</p>
    </div>
    @elseif(\Illuminate\Support\Facades\Cache::get('capture_maintenance', false) && !auth()->user()->admin())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-6 text-center mb-4">
        <svg class="w-10 h-10 text-red-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <h3 class="text-xs font-black text-red-800 uppercase tracking-widest">Captura Suspendida</h3>
        <p class="text-[11px] font-bold text-red-600 mt-2">Mantenimiento activo.</p>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-4">
        {{-- Stepper Header --}}
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-black text-[#13322B] dark:text-gray-200 uppercase tracking-[0.2em] flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-[#13322B] dark:bg-[#e6d194] animate-pulse"></div>
                    Capturar Incidencia
                </h2>
            </div>
            {{-- Step Indicators --}}
            <div class="flex items-center gap-0 mt-4">
                @foreach([['1', 'Código'], ['2', 'Fechas'], ['3', 'Confirmar']] as $s)
                <button wire:click="goToStep({{ $s[0] }})" class="flex items-center gap-2 group {{ $s[0] > 1 ? '' : '' }}">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-black transition-all duration-300
                        {{ $currentStep == $s[0] ? 'bg-[#13322B] dark:bg-oro text-white dark:text-[#13322B] shadow-lg scale-110' : ($currentStep > $s[0] ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400') }}">
                        @if($currentStep > $s[0]) ✓ @else {{ $s[0] }} @endif
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider {{ $currentStep == $s[0] ? 'text-[#13322B] dark:text-oro' : 'text-gray-400' }} hidden sm:inline">{{ $s[1] }}</span>
                </button>
                @if($s[0] < 3)
                <div class="flex-1 h-px mx-3 {{ $currentStep > $s[0] ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700' }} transition-colors"></div>
                @endif
                @endforeach
            </div>
        </div>

        <form wire:submit.prevent="store">
        <div class="p-6">

            {{-- ═══ STEP 1: Code Selection ═══ --}}
            @if($currentStep === 1)
            <div x-data="{ codeSearch: '' }">
                {{-- Quick search --}}
                <div class="mb-5">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="codeSearch" placeholder="Buscar código por nombre o número..."
                            class="w-full h-11 pl-10 pr-10 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#13322B]/20 outline-none transition-all">
                        <button type="button" x-show="codeSearch.length > 0" @click="codeSearch = ''" x-cloak
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Category Grid (Hidden when searching) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5" x-show="!codeSearch">
                    @foreach($categorizedCodes as $catKey => $cat)
                    @php
                        $colorMap = ['blue'=>'blue','red'=>'red','amber'=>'amber','rose'=>'rose','emerald'=>'emerald','cyan'=>'cyan','purple'=>'purple','gray'=>'gray'];
                        $c = $cat['color'];
                    @endphp
                    <button type="button" @click="expandedCat = expandedCat === '{{ $catKey }}' ? null : '{{ $catKey }}'"
                        class="p-3 rounded-xl border-2 transition-all text-left group hover:shadow-md"
                        :class="expandedCat === '{{ $catKey }}' ? 'border-{{ $c }}-500 bg-{{ $c }}-50 dark:bg-{{ $c }}-500/10 shadow-md' : 'border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-{{ $c }}-300'">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg bg-{{ $c }}-100 dark:bg-{{ $c }}-500/20 flex items-center justify-center">
                                <span class="text-{{ $c }}-600 dark:text-{{ $c }}-400 text-xs">
                                    @switch($cat['icon'])
                                        @case('clock') 🕐 @break
                                        @case('x-circle') ❌ @break
                                        @case('clipboard') 📋 @break
                                        @case('heart-pulse') 🏥 @break
                                        @case('file-text') 📜 @break
                                        @case('sun') 🌴 @break
                                        @case('settings') ⚙️ @break
                                        @case('ban') ⛔ @break
                                    @endswitch
                                </span>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-gray-700 dark:text-gray-300 leading-tight">{{ $cat['label'] }}</span>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400">{{ $cat['codes']->count() }} códigos</span>
                    </button>
                    @endforeach
                </div>

                {{-- Expanded Category Codes (Hidden when searching) --}}
                <div x-show="!codeSearch">
                    @foreach($categorizedCodes as $catKey => $cat)
                    <div x-show="expandedCat === '{{ $catKey }}'" x-cloak x-transition.opacity
                        class="bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700 p-4 mb-4 space-y-1">
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3 px-1">{{ $cat['label'] }}</div>
                        @foreach($cat['codes'] as $c)
                        <button type="button" wire:click="selectCode('{{ $c->id }}')"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left transition-all
                                {{ $codigo == $c->id ? 'bg-[#13322B] text-white' : 'hover:bg-white dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $codigo == $c->id ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">{{ $c->code }}</span>
                            <span class="text-[10px] font-bold flex-1 truncate">{{ $c->description }}</span>
                            @if($cat['frecuentes']->contains('id', $c->id))
                            <span class="text-amber-400 text-xs" title="Frecuente">⭐</span>
                            @endif
                        </button>
                        @endforeach
                    </div>
                    @endforeach
                </div>

                {{-- Global Search Results (Visible only when searching) --}}
                <div x-show="codeSearch.length > 0" x-cloak class="bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700 p-2 space-y-1 max-h-[300px] overflow-y-auto">
                    @foreach($categorizedCodes as $cat)
                        @foreach($cat['codes'] as $c)
                        <button type="button" wire:click="selectCode('{{ $c->id }}')"
                            x-show="String('{{ $c->code }} {{ addslashes($c->description) }}').toLowerCase().includes(codeSearch.toLowerCase())"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left transition-all
                                {{ $codigo == $c->id ? 'bg-[#13322B] text-white' : 'hover:bg-white dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $codigo == $c->id ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">{{ $c->code }}</span>
                            <span class="text-[10px] font-bold flex-1 truncate">{{ $c->description }}</span>
                            @if($cat['frecuentes']->contains('id', $c->id))
                            <span class="text-amber-400 text-xs" title="Frecuente">⭐</span>
                            @endif
                        </button>
                        @endforeach
                    @endforeach
                    
                    {{-- Empty State --}}
                    <div x-show="Array.from($el.parentElement.querySelectorAll('button[x-show]')).every(b => b.style.display === 'none')" 
                         class="text-center py-6 text-[10px] font-black text-gray-400 uppercase tracking-wider">
                        No se encontraron códigos
                    </div>
                </div>

                @error('codigo') <span class="text-red-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
            </div>
            @endif
            {{-- ═══ STEP 2: Date Selection ═══ --}}
            @if($currentStep === 2)
            <div>
                @php
                    $selCode = $this->selectedCode;
                    $selCodeName = $selCode ? '[' . $selCode->code . '] ' . $selCode->description : '';
                @endphp
                <div class="mb-4 flex items-center gap-3 px-3 py-2 bg-[#13322B]/5 dark:bg-[#13322B]/20 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-[#13322B] text-white flex items-center justify-center text-[10px] font-black">{{ $selCode->code ?? '' }}</div>
                    <div class="text-xs font-bold text-[#13322B] dark:text-[#e6d194] truncate">{{ $selCodeName }}</div>
                    <button type="button" wire:click="goToStep(1)" class="ml-auto text-[9px] font-black text-gray-400 hover:text-[#9b2247] uppercase tracking-wider transition-colors">Cambiar</button>
                </div>

                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">
                    {{ $dateMode === 'multiple' ? 'Seleccione días (múltiples)' : 'Seleccione rango (inicio → fin)' }}
                </label>
                <div wire:key="flatpickr-spa-{{ $dateMode }}-{{ md5(json_encode($enabledDateRanges)) }}" data-ranges="{{ json_encode($enabledDateRanges) }}"
                    x-data="{
                        init() {
                            if(this.$refs.fechaInput && this.$refs.fechaInput._flatpickr) this.$refs.fechaInput._flatpickr.destroy();
                            let allowedRanges = [];
                            try { allowedRanges = JSON.parse(this.$el.dataset.ranges || '[]'); } catch(e){}
                            window.flatpickr(this.$refs.fechaInput, {
                                mode: '{{ $dateMode }}',
                                dateFormat: 'Y-m-d',
                                inline: true,
                                showMonths: ('{{ $dateMode }}' === 'range' && window.innerWidth > 640) ? 2 : 1,
                                enable: allowedRanges,
                                disableMobile: true,
                                onChange: function(selectedDates, dateStr) { $wire.fechas_seleccionadas = dateStr; }
                            });
                            window.addEventListener('reset-calendar', () => { if(this.$refs.fechaInput && this.$refs.fechaInput._flatpickr) this.$refs.fechaInput._flatpickr.clear(); });
                        }
                    }">
                    <div wire:ignore class="flex justify-center">
                        <input x-ref="fechaInput" type="text" class="hidden">
                    </div>
                </div>
                @if($fechas_seleccionadas)
                <div class="mt-3 px-3 py-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg border border-emerald-200 dark:border-emerald-500/20">
                    <span class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Fechas seleccionadas: </span>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-300">{{ $fechas_seleccionadas }}</span>
                </div>
                @endif
                @error('fechas_seleccionadas') <span class="text-red-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
            </div>
            @endif

            {{-- ═══ STEP 3: Details & Confirm ═══ --}}
            @if($currentStep === 3)
            <div wire:key="step3-fields-{{ $isIncapacidad ? 'i' : '' }}{{ $isVacacional ? 'v' : '' }}{{ $isTxt ? 't' : '' }}{{ $isComision ? 'c' : '' }}{{ $isOtorgado ? 'o' : '' }}">

                {{-- Summary --}}
                @php $selCode = $this->selectedCode; @endphp
                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700 mb-5">
                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Resumen de captura</div>
                    <div class="grid grid-cols-2 gap-3 text-[11px]">
                        <div><span class="font-black text-gray-400 uppercase text-[9px] block">Código</span><span class="font-bold text-gray-800 dark:text-white">[{{ $selCode->code ?? '' }}] {{ $selCode->description ?? '' }}</span></div>
                        <div><span class="font-black text-gray-400 uppercase text-[9px] block">Fechas</span><span class="font-bold text-gray-800 dark:text-white">{{ $fechas_seleccionadas }}</span></div>
                        <div><span class="font-black text-gray-400 uppercase text-[9px] block">Empleado</span><span class="font-bold text-gray-800 dark:text-white">{{ $employee->num_empleado }} - {{ $employee->fullname }}</span></div>
                        <div><span class="font-black text-gray-400 uppercase text-[9px] block">Modo</span><span class="font-bold text-gray-800 dark:text-white">{{ $dateMode === 'multiple' ? 'Días sueltos' : 'Rango' }}</span></div>
                    </div>
                </div>

                {{-- Dynamic Fields: Incapacidad --}}
                @if($isIncapacidad)
                @php
                    $selectedMedicoId = $medico_id;
                    $medicoFound = collect($medicos)->firstWhere('id', $selectedMedicoId);
                    $selectedMedicoName = $medicoFound ? ($medicoFound->num_empleado . ' - ' . $medicoFound->fullname) : '-- Seleccionar médico --';
                @endphp
                <div class="p-4 rounded-xl border-l-4 border-[#9b2247] bg-rose-50/50 dark:bg-rose-500/5 mb-4 space-y-4">
                    <h4 class="text-[10px] font-black text-[#9b2247] uppercase tracking-[0.2em]">🏥 Datos de Incapacidad</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-data="{ init() { window.flatpickr(this.$refs.expedidaInput, { dateFormat: 'Y-m-d', disableMobile: true, onChange: (s, dateStr) => { $wire.fecha_expedida = dateStr; } }); } }">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Fecha Expedida</label>
                            <div wire:ignore><input x-ref="expedidaInput" type="text" placeholder="Seleccionar" readonly class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold cursor-pointer focus:ring-2 focus:ring-[#9b2247]/30 outline-none"></div>
                            @error('fecha_expedida') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">No. Licencia Médica</label>
                            <input type="text" wire:model="num_licencia" placeholder="00000000" class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold focus:ring-2 focus:ring-[#9b2247]/30 outline-none">
                            @error('num_licencia') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Médico Expedidor</label>
                        <div class="relative" x-data="{ open: false, search: '', select(id) { $wire.set('medico_id', id); this.open = false; } }" @click.outside="open = false">
                            <button @click.stop="open = !open" type="button" class="flex items-center justify-between w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-200 outline-none">
                                <span class="truncate">{{ $selectedMedicoName }}</span>
                                <svg class="w-3 h-3 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition.opacity class="absolute z-[100] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden">
                                <div class="p-2 border-b border-gray-100 dark:border-gray-700"><input type="text" x-model="search" @click.stop placeholder="Filtrar..." class="w-full h-8 px-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-[10px] font-bold outline-none"></div>
                                <div class="max-h-52 overflow-y-auto p-1.5">
                                    @foreach($medicos as $medico)
                                    <div @click.stop="select('{{ $medico->id }}')"
                                        x-show="String({{ json_encode($medico->fullname) }}).toLowerCase().includes(search.toLowerCase()) || '{{ $medico->num_empleado }}'.includes(search)"
                                        class="px-3 py-2 rounded-lg cursor-pointer text-[10px] font-bold transition-all {{ $medico_id == $medico->id ? 'bg-[#9b2247]/10 text-[#9b2247]' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                        {{ $medico->num_empleado }} - {{ $medico->fullname }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('medico_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Diagnóstico / Motivo</label>
                        <input type="text" wire:model="diagnostico" placeholder="Descripción breve..." class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold focus:ring-2 focus:ring-[#9b2247]/30 outline-none">
                        @error('diagnostico') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                @if(auth()->user()->admin())
                <label class="flex items-center gap-2 cursor-pointer group mb-4 px-1">
                    <input type="checkbox" wire:model="saltar_validacion_inca" class="w-4 h-4 rounded border-gray-300 text-[#9b2247] focus:ring-[#9b2247]">
                    <span class="text-[10px] font-black text-gray-400 group-hover:text-[#9b2247] uppercase tracking-widest">Saltar validación de exceso (ADMIN)</span>
                </label>
                @endif
                @endif

                {{-- Dynamic Fields: Vacaciones --}}
                @if($isVacacional)
                @php
                    $selectedPeriodoId = $periodo_id;
                    $periodoFound = collect($periodos)->firstWhere('id', $selectedPeriodoId);
                    $selectedPeriodoName = $periodoFound ? ($periodoFound->periodo . ' - ' . $periodoFound->year) : '-- Elija el Periodo --';
                @endphp
                <div class="p-4 rounded-xl border-l-4 border-cyan-500 bg-cyan-50/50 dark:bg-cyan-500/5 mb-4">
                    <h4 class="text-[10px] font-black text-cyan-700 dark:text-cyan-400 uppercase tracking-[0.2em] mb-3">🌴 Periodo Vacacional</h4>
                    <div class="relative" x-data="{ open: false, select(id) { $wire.set('periodo_id', id); this.open = false; } }" @click.away="close()">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold">
                            <span class="truncate">{{ $selectedPeriodoName }}</span>
                            <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition.opacity class="absolute z-[100] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden">
                            <div class="max-h-40 overflow-y-auto p-1.5">
                                @foreach($periodos as $p)
                                <div @click="select('{{ $p->id }}')" class="px-3 py-1.5 rounded-lg cursor-pointer text-[10px] font-bold {{ $periodo_id == $p->id ? 'bg-cyan-500/10 text-cyan-700' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                    {{ $p->periodo }} - {{ $p->year }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @error('periodo_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Dynamic Fields: TXT --}}
                @if($isTxt)
                <div class="p-4 rounded-xl border-l-4 border-[#13322B] bg-emerald-50/30 dark:bg-emerald-500/5 mb-4 space-y-3">
                    <h4 class="text-[10px] font-black text-[#13322B] dark:text-[#e6d194] uppercase tracking-[0.2em]">📋 Campos de T.X.T</h4>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Sustituto (quién cubrió)</label>
                        <input type="text" wire:model="cobertura_txt" placeholder="Nombre completo..." class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold focus:ring-2 focus:ring-[#13322B]/30 outline-none">
                        @error('cobertura_txt') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Autorizó Cambio</label>
                        <input type="text" wire:model="autoriza_txt" placeholder="Nombre del responsable..." class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold focus:ring-2 focus:ring-[#13322B]/30 outline-none">
                        @error('autoriza_txt') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                {{-- Dynamic Fields: Comisión --}}
                @if($isComision)
                <div class="p-4 rounded-xl border-l-4 border-purple-500 bg-purple-50/30 dark:bg-purple-500/5 mb-4">
                    <h4 class="text-[10px] font-black text-purple-700 dark:text-purple-400 uppercase tracking-[0.2em] mb-3">⚙️ Motivo de Comisión</h4>
                    <input type="text" wire:model="motivo_comision" placeholder="Describa el motivo..." class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold focus:ring-2 focus:ring-purple-500/30 outline-none">
                    @error('motivo_comision') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Dynamic Fields: Otorgado --}}
                @if($isOtorgado)
                <div class="p-4 rounded-xl border-l-4 border-amber-500 bg-amber-50/30 dark:bg-amber-500/5 mb-4">
                    <h4 class="text-[10px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-[0.2em] mb-3">📝 Detalles de Otorgado</h4>
                    <input type="text" wire:model="otorgado" placeholder="Describa el motivo..." class="block w-full h-9 px-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold focus:ring-2 focus:ring-amber-500/30 outline-none">
                    @error('otorgado') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Admin: Licencias skip --}}
                @if($isLicencia && auth()->user()->admin())
                <label class="flex items-center gap-2 cursor-pointer group mb-4 px-1">
                    <input type="checkbox" wire:model="saltar_validacion_lic" class="w-4 h-4 rounded border-gray-300 text-oro focus:ring-oro">
                    <span class="text-[10px] font-black text-gray-400 group-hover:text-oro uppercase tracking-widest">Saltar validación de tope (ADMIN)</span>
                </label>
                @endif

                {{-- No extra fields message --}}
                @if(!$this->hasExtraFields)
                <div class="text-center py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    ✓ No se requieren datos adicionales para este código
                </div>
                @endif
            </div>
            @endif

        </div>{{-- end p-6 --}}

        {{-- Navigation Buttons --}}
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            @if($currentStep > 1)
            <button type="button" wire:click="previousStep" class="flex items-center gap-2 px-4 py-2.5 text-[10px] font-black text-gray-500 uppercase tracking-wider hover:text-[#13322B] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Anterior
            </button>
            @else <div></div> @endif

            @if($currentStep < 3)
            <button type="button" wire:click="nextStep" class="flex items-center gap-2 px-6 py-2.5 bg-[#13322B] text-white text-[10px] font-black uppercase tracking-wider rounded-xl hover:bg-[#0a1f1a] transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                Siguiente <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            @else
            <button type="submit" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-8 py-3 bg-[#13322B] text-white text-xs font-black uppercase tracking-[0.15em] rounded-xl hover:bg-[#0a1f1a] transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 disabled:opacity-40">
                <span wire:loading.remove wire:target="store" class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Guardar Incidencia
                </span>
                <span wire:loading wire:target="store" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Procesando...
                </span>
            </button>
            @endif
        </div>
        </form>
    </div>{{-- end capture card --}}
    @endif
    </div>{{-- end left column --}}

    {{-- ═══ RIGHT COLUMN: HISTORY TIMELINE ═══ --}}
    <div class="flex-1 w-full lg:w-[58%] min-w-0 space-y-4">

    {{-- Employee Profile Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4 relative overflow-hidden">
        <div class="relative flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Search button BEFORE avatar --}}
                <button @click="openModal = true; $nextTick(() => $refs.modalSearchInput?.focus())"
                    class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-gray-900 hover:bg-[#13322B] border border-gray-200 dark:border-gray-700 hover:border-[#13322B] flex items-center justify-center transition-all group shrink-0">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-sm font-black text-gray-900 dark:text-white uppercase truncate tracking-wide">{{ $employee->num_empleado }} - {{ strtoupper($employee->fullname) }}</h1>
                    </div>
                    <div class="flex items-center gap-2 mt-1 text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-wrap">
                        <span>{{ $employee->department->description ?? 'N/A' }}</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span class="text-[#13322B] dark:text-[#e6d194]">{{ $employee->puesto->puesto ?? 'SIN PUESTO' }}</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span>{{ $employee->horario->horario ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                @if($employee->lactancia)
                <span class="hidden lg:inline-flex px-2 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black rounded-lg border border-emerald-200 dark:border-emerald-500/20 uppercase">Lactancia</span>
                @endif
                @if($employee->estancia)
                <span class="hidden lg:inline-flex px-2 py-1 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[8px] font-black rounded-lg border border-blue-200 dark:border-blue-500/20 uppercase">Estancia</span>
                @endif
                @if($employee->exento)
                <span class="hidden lg:inline-flex px-2 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[8px] font-black rounded-lg border border-amber-200 dark:border-amber-500/20 uppercase">Exento</span>
                @endif
                @if($employee->comisionado)
                <span class="hidden lg:inline-flex px-2 py-1 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 text-[8px] font-black rounded-lg border border-purple-200 dark:border-purple-500/20 uppercase">Comisionado</span>
                @endif
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">Historial de Capturas</h3>
            <span class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[9px] font-black text-gray-500 dark:text-gray-400">{{ count($incidencias) }} registros</span>
        </div>

        <div class="p-4 sm:p-6">
            @forelse($groupedIncidencias as $qnaLabel => $qnaIncidencias)
            @php
                $firstInc = $qnaIncidencias->first();
                $qnaObj = $firstInc->qna ?? null;
                $isActive = $qnaObj && $qnaObj->active == '1';
            @endphp
            <div class="mb-4 last:mb-0">
                {{-- QNA Header --}}
                <div class="w-full flex items-center gap-3 py-2">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                        <span class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">QNA {{ $qnaLabel }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider {{ $isActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                            {{ $isActive ? 'Abierta' : 'Cerrada' }}
                        </span>
                    </div>
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-700"></div>
                    <span class="text-[10px] font-bold text-gray-400">{{ $qnaIncidencias->count() }}</span>
                </div>

                {{-- QNA Items (always visible) --}}
                <div class="ml-5 pl-4 border-l-2 border-gray-100 dark:border-gray-700 space-y-2 mt-2">
                    @foreach($qnaIncidencias as $incidencia)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50/50 dark:bg-gray-900/30 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors group" wire:key="inc-tl-{{ $incidencia->id }}">
                        {{-- Code badge --}}
                        <div class="w-10 h-10 rounded-xl bg-[#13322B]/10 dark:bg-oro/10 flex items-center justify-center shrink-0">
                            <span class="text-[10px] font-black text-[#13322B] dark:text-oro">{{ $incidencia->codigo->code ?? 'N/A' }}</span>
                        </div>
                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] font-bold text-gray-700 dark:text-gray-300 truncate">{{ $incidencia->codigo->description ?? '' }}</div>
                            <div class="flex items-center gap-2 mt-0.5 text-[9px] font-bold text-gray-400">
                                <span>{{ \Carbon\Carbon::parse($incidencia->fecha_inicio)->format('d/m') }} → {{ \Carbon\Carbon::parse($incidencia->fecha_final)->format('d/m') }}</span>
                                <span class="text-gray-200 dark:text-gray-600">|</span>
                                <span class="text-[#9b2247] dark:text-oro font-black">{{ $incidencia->total_dias }}d</span>
                                @if($incidencia->periodo)
                                <span class="text-gray-200 dark:text-gray-600">|</span>
                                <span>Per.{{ $incidencia->periodo->periodo }}-{{ $incidencia->periodo->year }}</span>
                                @endif
                            </div>
                        </div>
                        {{-- Captured by --}}
                        <div class="text-right hidden sm:block shrink-0">
                            <div class="text-[9px] font-bold text-gray-400">{{ $incidencia->capturado_por ?? '—' }}</div>
                            <div class="text-[8px] font-bold text-gray-300 dark:text-gray-600">{{ $incidencia->created_at ? $incidencia->created_at->format('d/m H:i') : '' }}</div>
                        </div>
                        {{-- Delete --}}
                        @php
                            $user = auth()->user();
                            $isMaint = \Illuminate\Support\Facades\Cache::get('capture_maintenance', false) && !$user->admin();
                            $qnaActiva = $incidencia->qna && $incidencia->qna->active == '1';
                            $tienePermiso = $user->canCapture() && ($qnaActiva || (!$isMaint && $user->canCaptureInClosedQna($incidencia->qna_id)));
                        @endphp
                        @if($tienePermiso && !$isMaint)
                        <button type="button" x-on:click="window.Swal.fire({ title: '¿Eliminar?', text: 'Se eliminará esta incidencia y otras de la misma captura.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar' }).then((r) => { if (r.isConfirmed) $wire.delete('{{ $incidencia->token }}') })"
                            class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        @else
                        <span class="text-[9px] font-bold text-gray-300 dark:text-gray-600 uppercase italic px-2">Cerrada</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Sin historial de captura</span>
            </div>
            @endforelse
        </div>
    </div>

    </div>{{-- end right column --}}
    </div>{{-- end flex row --}}
</div>{{-- end max-w-full --}}
</div>{{-- end py-6 --}}

{{-- ═══ SEARCH MODAL ═══ --}}
<div x-show="openModal" x-cloak class="fixed inset-0 z-[5000] flex items-start justify-center pt-10 sm:pt-20 px-4"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <div class="fixed inset-0 bg-black/60" @click="openModal = false"></div>
    <div class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-700/50 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-black text-oro uppercase tracking-[0.2em]">Buscador</span>
                <h2 class="text-base font-bold text-gray-800 dark:text-white">Seleccione un Empleado</h2>
            </div>
            <button @click="openModal = false" class="p-2 hover:bg-gray-50 dark:hover:bg-gray-900 rounded-full"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-ref="modalSearchInput" x-model="query" @input="search()" type="text" placeholder="Nombre o número de empleado..."
                    class="w-full h-12 pl-12 pr-4 bg-gray-50 dark:bg-gray-900 border-2 border-transparent focus:border-oro/30 rounded-xl text-sm font-bold outline-none transition-all">
                <div x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2"><div class="w-5 h-5 border-2 border-oro border-t-transparent rounded-full animate-spin"></div></div>
            </div>
            <div class="mt-4">
                <div x-show="query.length < 2" class="text-center py-8"><p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Ingrese al menos 2 caracteres</p></div>
                <div x-show="query.length >= 2 && results.length === 0 && !loading" class="text-center py-8"><p class="text-xs text-oro font-black uppercase tracking-widest">Sin resultados</p></div>
                <div x-show="results.length > 0" class="space-y-2 max-h-[45vh] overflow-y-auto pr-1">
                    <template x-for="emp in results" :key="emp.id">
                        <div @click="select(emp)" class="p-3 bg-gray-50/50 dark:bg-gray-900/30 hover:bg-oro/5 border border-transparent hover:border-oro/20 rounded-xl cursor-pointer transition-all flex items-center gap-3 group">
                            <div class="w-9 h-9 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center font-black text-[#13322B] dark:text-oro text-xs shadow-sm" x-text="emp.initials"></div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="emp.department || 'Sin Dept.'"></div>
                                <div class="text-sm font-bold text-gray-700 dark:text-gray-200 truncate" x-text="emp.label"></div>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-oro group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/80 flex justify-between items-center">
            <span class="text-[10px] text-gray-400 font-bold uppercase flex items-center gap-2"><kbd class="px-1.5 py-0.5 rounded border border-gray-300 dark:border-gray-600 font-sans text-[8px]">ESC</kbd> cerrar</span>
            <span class="text-[9px] font-black text-oro uppercase tracking-[0.2em]" x-text="results.length + ' resultados'"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    window.addEventListener('storage', (event) => { if (event.key === 'biometrico_refresh') Livewire.dispatch('refreshIncidencias'); });
});
</script>
</div>
