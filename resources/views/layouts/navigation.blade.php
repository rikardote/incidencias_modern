<nav x-data="{ open: false }"
    class="bg-[#13322B] dark:bg-gray-950 border-b border-[#0a1f1a] dark:border-gray-800 shadow-md relative sticky top-0 z-50">

    {{-- BARRA DE PROGRESO TIPO VUEJS (GLOBAL) --}}
    <div x-data="{
        barWidth: 0,
        barColor: 'bg-[#e6d194]',
        barOpacity: 0,
        loadingTimer: null,
        resetTimer: null,
        isError: false,
        start() {
            clearTimeout(this.resetTimer);
            clearInterval(this.loadingTimer);
            this.barColor = 'bg-[#e6d194]';
            this.barOpacity = 100;
            this.barWidth = 5;
            this.isError = false;
            this.loadingTimer = setInterval(() => {
                if (this.barWidth < 90) this.barWidth += Math.random() * 5;
            }, 200);
        },
        finish(type = 'success') {
            if (this.isError && type === 'success') return; // Disable fast successful responses from overriding earlier reported errors 
            clearInterval(this.loadingTimer);
            
            if (type === 'error') {
                this.isError = true;
                this.barColor = 'bg-red-500';
                this.barWidth = 0; // Regresa agresivamente a 0
            } else {
                this.barColor = 'bg-emerald-500';
                this.barWidth = 100;
            }
            
            this.resetTimer = setTimeout(() => {
                this.barOpacity = 0;
                setTimeout(() => { if (this.barOpacity === 0) { this.barWidth = 0; this.isError = false; } }, 400); 
            }, type === 'error' ? 800 : 500);
        }
    }" x-on:topbar-start.window="start()" x-on:topbar-end.window="finish($event.detail)"
        x-on:island-notif.window="if($event.detail.type === 'error') finish('error'); else finish('success');"
        class="absolute top-0 left-0 w-full h-[3px] z-[100000] pointer-events-none overflow-hidden"
        :class="barOpacity === 0 ? 'opacity-0' : 'opacity-100'" style="transition: opacity 0.4s ease;">
        <div class="h-full shadow-[0_0_8px_currentColor] transition-all duration-300 ease-out"
            :style="`width: ${barWidth}%;`" :class="barColor">
        </div>
    </div>

    {{-- Pleca superior (eliminada por ser redundante en dark bg) --}}
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 hidden sm:flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                        <x-application-logo class="block h-11 w-auto drop-shadow-sm" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')"
                        wire:navigate>
                        {{ __('Empleados') }}
                    </x-nav-link>

                    <!-- Dropdown Reportes -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="max">
                            <x-slot name="trigger">
                                <button
                                    class="h-16 inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('reports.*') ? 'border-white dark:border-oro text-white font-bold' : 'border-transparent text-gray-300 font-medium hover:text-white hover:border-gray-300' }} text-sm leading-5 focus:outline-none transition duration-150 ease-in-out uppercase tracking-wider">
                                    <div>REPORTES</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('reports.general')"
                                    class="border-b border-gray-100 dark:border-gray-700/50" wire:navigate>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        {{ __('General (RH5)') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.sinderecho')"
                                    class="dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 border-b border-gray-100 dark:border-gray-700 py-3 uppercase text-xs font-bold tracking-wider hover:text-oro transition-colors"
                                    wire:navigate>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        {{ __('Sin Derecho a Nota Buena') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.exceso-incapacidades')"
                                    class="dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 border-b border-gray-100 dark:border-gray-700 py-3 uppercase text-xs font-bold tracking-wider hover:text-oro transition-colors"
                                    wire:navigate>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ __('Exceso de Incapacidades') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('biometrico.index')"
                                    class="dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 py-3 uppercase text-xs font-bold tracking-wider hover:text-oro transition-colors"
                                    wire:navigate>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 3m0 18a10.003 10.003 0 01-10-10C2 5.002 3.368 2.89 5.612 1.706m9.546 1.954A10.014 10.014 0 0115 10c0 1.588-.368 3.091-1.028 4.428m-2.43 2.722L10.18 18m0 0a9.992 9.992 0 01-2.927-4.572M10.18 18a9.998 9.998 0 003.82-2.848m-5.462-8.49a3 3 0 10-2.434 2.196M5 19a9 9 0 0014 0" />
                                        </svg>
                                        {{ __('Asistencia Biométrica') }}
                                    </div>
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <!-- Active QNA Notice (Dynamic Island) -->
            @php
            $isMaintenanceActive = \Illuminate\Support\Facades\Cache::get('capture_maintenance', false);
            @endphp
            @if($activeQna)
            <div class="flex items-center px-2 sm:px-4" wire:persist="active-qna-widget">
                <div x-data="{ 
                        isMaint: {{ $isMaintenanceActive ? 'true' : 'false' }},
                        islandMsg: null,
                        islandType: 'info',
                        islandTimer: null,
                        showPhase: 'face',
                        progress: 0,
                        get face() {
                            const msg = (this.islandMsg || '').toLowerCase();
                            if (this.islandType === 'error' || msg.includes('error')) return '( > _ < )';
                            if (msg.includes('eliminada') || msg.includes('borrar')) return '( - _ - )';
                            if (msg.includes('duplicado') || msg.includes('traslape')) return '( ° o ° )';
                            if (this.islandType === 'success') return '( ^ ‿ ^ )';
                            return '( - ‿ - )';
                        },
                        showIsland(rawMsg, type) {
                            if (this.islandTimer) clearTimeout(this.islandTimer);
                            const msg = (rawMsg || '');
                            this.islandMsg = msg;
                            this.islandType = type || 'info';

                            if (navigator.vibrate && navigator.userActivation && navigator.userActivation.hasBeenActive) {
                                try {
                                    if (this.islandType === 'error') navigator.vibrate([200, 100, 200]);
                                    else if (this.islandType === 'success') navigator.vibrate([100, 50, 100]);
                                    else navigator.vibrate(50);
                                } catch(e) {}
                            }
                            
                            // Si el mensaje nuevo NO es reporte listo, reseteamos progreso
                            if (!msg.includes('Listo')) this.progress = 0;
                            
                            const currentStyle = Alpine.store('island').activeStyle;
                            const isReport = msg.includes('Generando');
                            const isAction = msg.includes('Capturada') || msg.includes('Eliminada') || isReport;

                            // 1. Determinar Fase Inicial
                            const showFaces = Alpine.store('island').showFaces;
                            if (currentStyle === 'glass' || currentStyle === 'cyberpunk' || currentStyle === 'minimal' || currentStyle === 'kinetic' || currentStyle === 'starwars' || currentStyle === 'avengers' || isReport || !showFaces) {
                                this.showPhase = 'text';
                            } else {
                                this.showPhase = 'face';
                                setTimeout(() => {
                                    if (this.islandMsg === msg) this.showPhase = 'text';
                                }, 800);
                            }

                            // 2. Lógica de Progreso
                            if (currentStyle === 'progress' || isReport) {
                                if (isAction) {
                                    let step = isReport ? 1 : 2;
                                    let speed = isReport ? 15 : 20;
                                    let limit = isReport ? 95 : 100;
                                    
                                    let interval = setInterval(() => {
                                        if (this.islandMsg !== msg || this.progress >= limit) {
                                            clearInterval(interval);
                                            return;
                                        }
                                        this.progress += step;
                                    }, speed);
                                }
                            }

                            // 3. Timer de Cierre (No aplica si es reporte activo)
                            if (!isReport) {
                                let duration = 5000;
                                if (currentStyle === 'progress' && isAction) duration = 4000;
                                if (currentStyle === 'avengers') duration = 2800;
                                
                                this.islandTimer = setTimeout(() => {
                                    this.islandMsg = null;
                                    setTimeout(() => { 
                                        this.progress = 0; 
                                        this.showPhase = 'face'; 
                                    }, 500);
                                }, duration);
                            }
                        }
                     }" @maintenance-updated.window="isMaint = $event.detail.mode"
                    x-on:island-progress-update.window="progress = $event.detail.progress"
                    x-on:island-notif.window="showIsland($event.detail.message, $event.detail.type)"
                    class="bg-[#0a1f1a] dark:bg-gray-900 border rounded-full shadow-lg h-9 px-4 flex items-center justify-center relative min-w-max transition-all duration-700 cubic-bezier(0.4, 0, 0.2, 1) transform"
                    :class="{ 
                        'border-oro ring-2 ring-oro/20 bg-[#0a1f1a] z-[100]': islandMsg && islandType !== 'error', 
                        'border-red-500 ring-2 ring-red-500/30 bg-[#1a0a0a] z-[100]': islandMsg && islandType === 'error',
                        'border-oro/30': !islandMsg && !isMaint,
                        'border-red-500/40 ring-1 ring-red-500/10': isMaint && !islandMsg,
                        'ring-2 ring-white/10 shadow-[0_0_20px_rgba(255,255,255,0.1)]': islandMsg && $store.island.activeStyle === 'kinetic'
                     }"
                    :style="islandMsg ? ($store.island.activeStyle === 'kinetic' ? 'min-width: 320px' : 'min-width: 280px') : ''">

                    {{-- Estado 1: QNA Activa (Default) --}}
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0 transition-all duration-500 ease-in-out"
                        :class="(isMaint || islandMsg) ? 'opacity-0 invisible blur-sm translate-y-2' : 'opacity-100 visible translate-y-0'">
                        <div class="flex items-center gap-1.5 border-r border-oro/20 pr-2 sm:pr-3 leading-none">
                            <span class="flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-oro opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-oro"></span>
                            </span>
                            <span
                                class="hidden lg:inline text-[8px] font-bold text-gray-400 uppercase tracking-tighter nothing-font">QNA
                                Activa</span>
                            <span class="text-[12px] font-black text-white leading-none nothing-font">
                                {{ $activeQna->qna }}/{{ $activeQna->year }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 sm:gap-2 leading-none">
                            <span
                                class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter nothing-font">Cierre:</span>
                            <span class="text-[12px] font-black text-oro tracking-wide leading-none nothing-font">
                                {{ $activeQna->cierre ? \Carbon\Carbon::parse($activeQna->cierre)->format('d/m/Y') :
                                'PENDIENTE' }}
                            </span>
                        </div>
                    </div>

                    {{-- Estado 2: Mantenimiento --}}
                    <div x-show="isMaint && !islandMsg" x-cloak x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute inset-0 flex items-center justify-center gap-2 text-red-500 whitespace-nowrap animate-pulse">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none nothing-font">
                            Mantenimiento
                        </span>
                    </div>

                    {{-- Estado 3: Notificación Dinámica --}}
                    <div x-show="islandMsg" x-cloak x-transition:enter="transition transform ease-out duration-500"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition transform ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2 blur-md"
                        class="absolute inset-0 flex items-center justify-center px-4"
                        :class="$store.island.activeStyle === 'kinetic' ? 'rounded-full' : 'overflow-hidden rounded-full'"
                        :style="$store.island.activeStyle !== 'kinetic' ? 'clip-path: inset(0 round 999px); -webkit-clip-path: inset(0 round 999px);' : ''">

                        <div class="relative flex items-center justify-center w-full h-full">

                            {{-- Fase 1: El Rostro Reactivo --}}
                            <div x-show="showPhase === 'face'" x-transition:enter="transition duration-400"
                                x-transition:enter-start="opacity-0 scale-50"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition duration-400 blur-sm"
                                x-transition:leave-end="opacity-0 -translate-y-2 scale-125"
                                class="absolute inset-0 flex flex-col items-center justify-center text-green-400 nothing-font transition-all"
                                :class="{ 'text-red-500': islandType === 'error', 'text-amber-400': islandType === 'warning' }">
                                <span x-text="face"
                                    class="text-xl font-black tracking-widest drop-shadow-[0_0_8px_rgba(74,222,128,0.3)]"></span>
                            </div>

                            {{-- Fase 2: Contenido Dinámico --}}
                            <div x-show="showPhase === 'text'"
                                x-transition:enter="transition duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)"
                                x-transition:enter-start="opacity-0 translate-y-3 scale-90"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                class="absolute inset-0 flex items-center justify-center px-4">

                                {{-- CASE: REPORT OR PROGRESS STYLE --}}
                                <template
                                    x-if="(islandMsg || '').includes('Generando') || ($store.island.activeStyle === 'progress' && ((islandMsg || '').includes('Capturada') || (islandMsg || '').includes('Eliminada')))">
                                    <x-island.styles.progress />
                                </template>

                                {{-- CASE: GLASS (Only if not progress/report) --}}
                                <template
                                    x-if="$store.island.activeStyle === 'glass' && !((islandMsg || '').includes('Generando'))">
                                    <x-island.styles.glass />
                                </template>

                                {{-- CASE: CYBERPUNK (Only if not progress/report) --}}
                                <template
                                    x-if="$store.island.activeStyle === 'cyberpunk' && !((islandMsg || '').includes('Generando'))">
                                    <x-island.styles.cyberpunk />
                                </template>

                                {{-- CASE: MATRIX --}}
                                <template
                                    x-if="$store.island.activeStyle === 'matrix' && !((islandMsg || '').includes('Generando'))">
                                    <x-island.styles.matrix />
                                </template>

                                {{-- CASE: KINETIC --}}
                                <template
                                    x-if="$store.island.activeStyle === 'kinetic' && !((islandMsg || '').includes('Generando'))">
                                    <x-island.styles.kinetic />
                                </template>

                                {{-- CASE: STARWARS --}}
                                <template
                                    x-if="$store.island.activeStyle === 'starwars' && !((islandMsg || '').includes('Generando'))">
                                    <x-island.styles.starwars />
                                </template>

                                {{-- CASE: AVENGERS --}}
                                <template
                                    x-if="$store.island.activeStyle === 'avengers' && !((islandMsg || '').includes('Generando'))">
                                    <x-island.styles.avengers />
                                </template>

                                {{-- CASE: CLASSIC (Default fallback) --}}
                                <template
                                    x-if="($store.island.activeStyle === 'classic' || ($store.island.activeStyle === 'progress' && !((islandMsg || '').includes('Capturada') || (islandMsg || '').includes('Eliminada')))) && !((islandMsg || '').includes('Generando'))">
                                    <x-island.styles.classic />
                                </template>

                                {{-- CASE: MINIMAL --}}
                                <template
                                    x-if="$store.island.activeStyle === 'minimal' && !((islandMsg || '').includes('Generando'))">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full animate-pulse"
                                            :class="islandType === 'error' ? 'bg-red-500' : 'bg-emerald-500'"></span>
                                        <span x-text="islandMsg"
                                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-200 nothing-font"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                @font-face {
                    font-family: 'NothingFont';
                    src: url('/fonts/nothing-font-5x7.otf.woff2') format('woff2');
                    font-weight: normal;
                    font-style: normal;
                }

                .text-cyan-400 {
                    color: #22d3ee;
                }

                .text-magenta-500 {
                    color: #ff00ff;
                }

                .bg-cyan-400 {
                    background-color: #22d3ee;
                }

                .bg-magenta-500 {
                    background-color: #ff00ff;
                }

                .border-cyan-400\/50 {
                    border-color: rgba(34, 211, 238, 0.5);
                }

                .border-magenta-500\/30 {
                    border-color: rgba(255, 0, 255, 0.3);
                }

                .shadow-cyan-shadow {
                    filter: drop-shadow(0 0 8px rgba(34, 211, 238, 0.6));
                }

                .nothing-font {
                    font-family: 'NothingFont', monospace;
                }

                @keyframes thumb-pop {
                    0% {
                        transform: scale(0.5) rotate(-20deg);
                        opacity: 0;
                    }

                    50% {
                        transform: scale(1.4) rotate(10deg);
                    }

                    70% {
                        transform: scale(0.9) rotate(0deg);
                    }

                    100% {
                        transform: scale(1) rotate(0);
                        opacity: 1;
                    }
                }

                .animate-thumb-pop {
                    animation: thumb-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
                }

                @keyframes success-face {

                    0%,
                    100% {
                        transform: translateY(0) scale(1.1);
                    }

                    50% {
                        transform: translateY(-2px) scale(1.2);
                    }
                }

                .animate-success-face {
                    animation: success-face 0.8s ease-in-out infinite;
                }

                @keyframes blink {

                    0%,
                    45%,
                    55%,
                    100% {
                        opacity: 1;
                    }

                    50% {
                        opacity: 0;
                    }
                }

                .animate-blink {
                    animation: blink 3s linear infinite;
                }

                @keyframes marquee {
                    0% {
                        transform: translateX(0);
                    }

                    10% {
                        transform: translateX(0);
                    }

                    90% {
                        transform: translateX(calc(-100% + 150px));
                    }

                    100% {
                        transform: translateX(calc(-100% + 150px));
                    }
                }

                .animate-marquee {
                    animation: marquee 8s linear infinite;
                    padding-left: 0;
                    display: inline-block;
                }

                [x-cloak] {
                    display: none !important;
                }
            </style>
            @endif

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-200 bg-[#13322B] dark:bg-gray-950 hover:text-white hover:bg-[#0a1f1a] dark:hover:bg-gray-800 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')"
                            class="border-b border-gray-100 dark:border-gray-700/50" wire:navigate>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('Perfil') }}
                            </div>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    {{ __('Cerrar Sesión') }}
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-[#0a1f1a] dark:hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')"
                wire:navigate>
                {{ __('Empleados') }}
            </x-responsive-nav-link>
            <div class="px-4 py-2 mt-2">
                <div class="font-medium text-xs text-gray-400 uppercase tracking-widest pl-1">Reportes</div>
            </div>
            <x-responsive-nav-link :href="route('reports.general')" :active="request()->routeIs('reports.general')"
                wire:navigate>
                <span class="pl-4">{{ __('General (RH5)') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.sinderecho')"
                :active="request()->routeIs('reports.sinderecho')" wire:navigate>
                <span class="pl-4">{{ __('Sin Derecho a Nota Buena') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.exceso-incapacidades')"
                :active="request()->routeIs('reports.exceso-incapacidades')" wire:navigate>
                <span class="pl-4">{{ __('Exceso de Incapacidades') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('biometrico.index')" :active="request()->routeIs('biometrico.index')"
                wire:navigate>
                <span class="pl-4">{{ __('Asistencia Biométrica') }}</span>
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-[#0a1f1a] dark:border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    <!-- Hooks Globales Livewire para Barra de Progreso -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({ succeed, fail }) => {
                window.dispatchEvent(new CustomEvent('topbar-start'));

                succeed(({ snapshot }) => {
                    let hasError = false;

                    // 1. Validar si la petición resultó en errores de validación
                    if (snapshot && snapshot.memo && snapshot.memo.errors && Object.keys(snapshot.memo.errors).length > 0) {
                        hasError = true;
                    }

                    // 2. Inspeccionar Nativamente si el Servidor ordenó emitir un Toast de Error (Ej. Traslapes Exception)
                    if (snapshot && snapshot.effects && snapshot.effects.dispatches) {
                        snapshot.effects.dispatches.forEach(d => {
                            if (d.name === 'toast') {
                                let params = d.params || {};
                                if (Array.isArray(params)) params = params[0] || {};
                                // Los parámetros interceptan si trae icono de error nativo programado en Managers
                                if (params.icon === 'error' || params.type === 'error') {
                                    hasError = true;
                                }
                            }
                        });
                    }

                    if (hasError) {
                        window.dispatchEvent(new CustomEvent('topbar-end', { detail: 'error' }));
                    } else {
                        window.dispatchEvent(new CustomEvent('topbar-end', { detail: 'success' }));
                    }
                });

                fail(() => {
                    window.dispatchEvent(new CustomEvent('topbar-end', { detail: 'error' }));
                });
            });
        });
    </script>
</nav>