<nav x-data="{ open: false }"
    class="bg-[#13322B] dark:bg-gray-950 border-b border-[#0a1f1a] dark:border-gray-800 shadow-md sticky top-0 z-50 antialiased">

    {{-- BARRA DE PROGRESO GLOBAL --}}
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
            if (this.isError && type === 'success') return; 
            clearInterval(this.loadingTimer);
            
            if (type === 'error') {
                this.isError = true;
                this.barColor = 'bg-red-500';
                this.barWidth = 100; 
            } else {
                this.barColor = 'bg-emerald-500';
                this.barWidth = 100;
            }
            
            this.resetTimer = setTimeout(() => {
                this.barOpacity = 0;
                setTimeout(() => { if (this.barOpacity === 0) { this.barWidth = 0; this.isError = false; } }, 400); 
            }, type === 'error' ? 1200 : 500);
        }
    }" x-on:topbar-start.window="start()" x-on:topbar-end.window="finish($event.detail)"
        x-on:island-notif.window="if($event.detail.type === 'error') finish('error'); else finish('success');"
        class="absolute top-0 left-0 w-full h-[3px] z-[100000] pointer-events-none overflow-hidden transition-opacity duration-500"
        :class="barOpacity === 0 ? 'opacity-0' : 'opacity-100'">
        <div class="h-full shadow-[0_0_10px_currentColor] transition-all duration-500 ease-out"
            :style="`width: ${barWidth}%;`" :class="barColor">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex flex-1 items-center">
                <!-- Logo -->
                <div class="shrink-0 hidden sm:flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                        <x-application-logo class="block h-10 w-auto drop-shadow-sm" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')"
                        wire:navigate>
                        {{ __('Empleados') }}
                    </x-nav-link>


                    <!-- Dropdown Reportes -->
                    <div class="flex items-center">
                        <x-dropdown align="left" width="64"
                            contentClasses="bg-white dark:bg-[#0d2a23] border border-gray-200 dark:border-white/10 rounded-md shadow-2xl overflow-hidden min-w-[240px]">
                            <x-slot name="trigger">
                                <button
                                    class="h-16 inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('reports.*') ? 'border-white text-white font-bold' : 'border-transparent text-gray-400 font-medium hover:text-white hover:border-gray-300' }} text-[11px] uppercase tracking-widest transition duration-150">
                                    REPORTES
                                    <svg class="ms-1 h-4 w-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('reports.general')" wire:navigate
                                    class="hover:bg-gray-100 dark:hover:bg-white/5 py-3 border-b border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-[#e6d194]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        {{ __('General (RH5)') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.sinderecho')" wire:navigate
                                    class="hover:bg-gray-100 dark:hover:bg-white/5 py-3 border-b border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-[#e6d194]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        {{ __('Sin Derecho a Nota Buena') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.estadisticas')" wire:navigate
                                    class="hover:bg-gray-100 dark:hover:bg-white/5 py-3 border-b border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-[#e6d194]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        {{ __('Estadística de Conceptos') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.exceso-incapacidades')" wire:navigate
                                    class="hover:bg-gray-100 dark:hover:bg-white/5 py-3 border-b border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-[#e6d194]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ __('Exceso de Incapacidades') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.ausentismo')" wire:navigate
                                    class="hover:bg-gray-100 dark:hover:bg-white/5 py-3 border-b border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-[#e6d194]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m16-10V4m0 0L13 8m4-4l4 4m-11 4h6">
                                            </path>
                                        </svg>
                                        {{ __('Ausentismo') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('biometrico.index')" wire:navigate
                                    class="hover:bg-gray-100 dark:hover:bg-white/5 py-3">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-[#e6d194]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 3m0 18a10.003 10.003 0 01-10-10C2 5.002 3.368 2.89 5.612 1.706m9.546 1.954A10.014 10.014 0 0115 10c0 1.588-.368 3.091-1.028 4.428m-2.43 2.722L10.18 18m0 0a9.992 9.992 0 01-2.927-4.572M10.18 18a9.998 9.998 0 003.82-2.848m-5.462-8.49a3 3 0 10-2.434 2.196M5 19a9 9 0 0014 0">
                                            </path>
                                        </svg>
                                        {{ __('Asistencia Biométrica') }}
                                    </div>
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <!-- DYNAMIC ISLAND CONTAINER (MIDDLE) -->
            <div class="flex items-center px-4 shrink-0">
                @if($activeQna)
                <div x-data="{ 
                    isMaint: {{ \Illuminate\Support\Facades\Cache::get('capture_maintenance', false) ? 'true' : 'false' }},
                    islandMsg: null,
                    islandType: 'info',
                    islandTimer: null,
                    showPhase: 'face',
                    progress: 0,
                    activeStyle: $store.island.activeStyle,
                    init() {
                        this.$watch('$store.island.activeStyle', val => this.activeStyle = val);
                        window.addEventListener('island-notif', (e) => {
                            this.showIsland(e.detail.message || 'Sin mensaje', e.detail.type || 'info');
                        });
                    },
                    get face() {
                        const msg = (this.islandMsg || '').toLowerCase();
                        if (this.islandType === 'error' || msg.includes('error')) return '( > _ < )';
                        if (msg.includes('eliminada') || msg.includes('borrar')) return '( - _ - )';
                        if (this.islandType === 'success') return '( ^ ‿ ^ )';
                        return '( - ‿ - )';
                    },
                    showIsland(rawMsg, type) {
                        if (this.islandTimer) clearTimeout(this.islandTimer);
                        this.islandMsg = rawMsg;
                        this.islandType = type || 'info';

                        if (navigator.vibrate) {
                            try {
                                if (this.islandType === 'error') navigator.vibrate([200, 100, 200]);
                                else navigator.vibrate(50);
                            } catch(e) {}
                        }

                        let duration = Math.max(5000, (rawMsg.length * 120) + 2000);

                        this.showPhase = $store.island.showFaces ? 'face' : 'text';
                        if ($store.island.showFaces) {
                            setTimeout(() => {
                                if (this.islandMsg === rawMsg) this.showPhase = 'text';
                            }, 800);
                        }

                        this.islandTimer = setTimeout(() => {
                            this.islandMsg = null;
                        }, duration);
                    }
                 }" @maintenance-updated.window="isMaint = $event.detail.mode"
                    class="bg-[#0a1f1a] dark:bg-gray-900 border rounded-full shadow-lg h-9 px-4 flex items-center justify-center relative transition-all duration-700 cubic-bezier(0.4, 0, 0.2, 1) z-[100000]"
                    :class="{ 
                        'border-[#e6d194] ring-2 ring-[#e6d194]/20 bg-[#0a1f1a] z-[100]': islandMsg && islandType !== 'error', 
                        'border-red-500 ring-2 ring-red-500/30 bg-[#1a0a0a] z-[100]': islandMsg && islandType === 'error',
                        'border-white/10': !islandMsg
                     }" :style="islandMsg ? 'min-width: clamp(280px, 15ch + 10rem, 400px)' : 'min-width: 14.5rem'">

                    {{-- Estado 1: QNA Activa (Default) --}}
                    <div class="flex items-center gap-3 sm:gap-4 shrink-0 transition-all duration-500 ease-in-out"
                        :class="(isMaint || islandMsg) ? 'opacity-0 invisible blur-sm translate-y-2' : 'opacity-100 visible translate-y-0'">
                        <div class="flex items-center gap-2 border-r border-oro/20 pr-3 sm:pr-4 leading-none">
                            <div class="relative flex h-2 w-2 shrink-0">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-oro opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-oro"></span>
                            </div>
                            <div class="flex items-baseline gap-1.5">
                                <span
                                    class="hidden lg:inline text-[7px] font-bold text-gray-400 uppercase tracking-wider nothing-font">QNA
                                    ACTIVA</span>
                                <span
                                    class="text-[13px] font-black text-white leading-none nothing-font tracking-tight">
                                    {{ $activeQna->qna }}/{{ $activeQna->year }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-1.5 leading-none">
                            <span
                                class="text-[7px] font-bold text-gray-400 uppercase tracking-wider nothing-font">CIERRE:</span>
                            <span class="text-[13px] font-black text-oro tracking-tight leading-none nothing-font">
                                {{ $activeQna->cierre ? \Carbon\Carbon::parse($activeQna->cierre)->format('d/m/Y') :
                                'PENDIENTE' }}
                            </span>
                        </div>
                    </div>

                    {{-- Estado: Notificación con Estilos --}}
                    <div x-show="islandMsg" x-cloak class="absolute inset-0 flex items-center justify-center px-4">
                        {{-- Fase Rostro --}}
                        <template x-if="showPhase === 'face'">
                            <span x-text="face"
                                class="text-emerald-400 font-black animate-pulse tracking-widest text-xl nothing-font"></span>
                        </template>

                        {{-- Fase Texto con Selección de Estilo --}}
                        <template x-if="showPhase === 'text'">
                            <div class="w-full">
                                {{-- Estilo Reporte/Progreso --}}
                                <template x-if="(islandMsg || '').includes('Generando') || activeStyle === 'progress'">
                                    <x-island.styles.progress />
                                </template>

                                {{-- Estilo Matrix --}}
                                <template x-if="activeStyle === 'matrix' && !(islandMsg || '').includes('Generando')">
                                    <x-island.styles.matrix />
                                </template>

                                {{-- Estilo StarWars --}}
                                <template x-if="activeStyle === 'starwars' && !(islandMsg || '').includes('Generando')">
                                    <x-island.styles.starwars />
                                </template>

                                {{-- Estilo Minimal/Classic (Fallback) --}}
                                <template x-if="activeStyle === 'classic' || activeStyle === 'minimal'">
                                    <div
                                        class="flex items-center justify-center gap-2 overflow-hidden w-full relative min-w-0">
                                        <div :class="islandMsg && islandMsg.length > 25 ? 'animate-marquee whitespace-nowrap' : ''"
                                            :style="islandMsg && islandMsg.length > 25 ? `animation-duration: ${Math.max(4, islandMsg.length * 0.12)}s` : ''"
                                            class="inline-block min-w-0">
                                            <span x-text="islandMsg"
                                                class="text-[10px] font-bold text-white uppercase tracking-widest block text-center truncate px-2 nothing-font"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Estado: Mantenimiento --}}
                    <div x-show="isMaint && !islandMsg" x-cloak
                        class="absolute inset-0 flex items-center justify-center px-1">
                        <div class="flex items-baseline gap-2 animate-pulse">
                            <span class="text-red-500 text-xs font-black animate-ping">●</span>
                            <span
                                class="text-red-500 text-[10px] font-black uppercase tracking-[0.2em] nothing-font">Mantenimiento
                                de Captura</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Profile Dropdown (Right) -->
            <div class="flex flex-1 items-center justify-end">
                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                    <!-- Botón LOG (Desktop Icon Only) -->
                    <button @click="$store.island.toggleLog()"
                        class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-white/5 transition relative group active:scale-95">
                        <div class="relative">
                            <svg class="w-5 h-5 text-[#e6d194] group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <template x-if="$store.island.logCount > 0">
                                <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-600 text-[8px] text-white font-black flex items-center justify-center"
                                        x-text="$store.island.logCount"></span>
                                </span>
                            </template>
                        </div>
                    </button>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-3 text-sm font-medium text-gray-300 hover:text-white transition">
                                <x-user-avatar :avatar="Auth::user()->avatar" :name="Auth::user()->name"
                                    size="w-8 h-8 rounded-full border border-white/10" />
                                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')" wire:navigate>{{ __('Perfil')
                                }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-400">{{
                                    __('Cerrar Sesión') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger (Mobile) -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = !open" class="p-2 text-gray-400 hover:text-white">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" x-transition x-cloak
        class="sm:hidden bg-white dark:bg-[#0a1f1a] border-t border-gray-200 dark:border-white/5">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')"
                wire:navigate>{{ __('Empleados') }}</x-responsive-nav-link>

            <button @click="$store.island.toggleLog(); open = false"
                class="w-full flex items-center gap-3 ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#13322B] dark:text-gray-300 hover:text-[#0a1f1a] dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 hover:border-[#13322B] dark:hover:border-[#e6d194] focus:outline-none transition duration-150 ease-in-out uppercase tracking-widest group">
                <div class="relative">
                    <svg class="w-4 h-4 text-[#e6d194]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <template x-if="$store.island.logCount > 0">
                        <span class="absolute -top-1.5 -right-1.5 flex h-3.5 w-3.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span
                                class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-600 text-[8px] text-white font-black flex items-center justify-center"
                                x-text="$store.island.logCount"></span>
                        </span>
                    </template>
                </div>
                <span>LOG</span>
            </button>

            <div class="px-4 py-2 mt-2">
                <div class="font-medium text-[10px] text-gray-500 uppercase tracking-[0.2em]">Reportes</div>
            </div>
            <x-responsive-nav-link :href="route('reports.general')" :active="request()->routeIs('reports.general')"
                wire:navigate>
                <span class="ps-4">{{ __('General (RH5)') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.sinderecho')"
                :active="request()->routeIs('reports.sinderecho')" wire:navigate>
                <span class="ps-4">{{ __('Sin Derecho a Nota Buena') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.estadisticas')"
                :active="request()->routeIs('reports.estadisticas')" wire:navigate>
                <span class="ps-4">{{ __('Estadística de Conceptos') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.exceso-incapacidades')"
                :active="request()->routeIs('reports.exceso-incapacidades')" wire:navigate>
                <span class="ps-4">{{ __('Exceso de Incapacidades') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.ausentismo')"
                :active="request()->routeIs('reports.ausentismo')" wire:navigate>
                <span class="ps-4">{{ __('Ausentismo') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('biometrico.index')" :active="request()->routeIs('biometrico.index')"
                wire:navigate>
                <span class="ps-4">{{ __('Asistencia Biométrica') }}</span>
            </x-responsive-nav-link>
        </div>

        <!-- Mobile User Info -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-800">
            <div class="px-4 flex items-center gap-3">
                <x-user-avatar :avatar="Auth::user()->avatar" :name="Auth::user()->name" />
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" wire:navigate>
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    <!-- Livewire Progress Bar Hooks -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({ component, succeed }) => {
                if (component.name === 'chat-widget') return;
                window.dispatchEvent(new CustomEvent('topbar-start'));
                succeed(({ snapshot }) => {
                    let hasError = false;
                    if (snapshot?.memo?.errors && Object.keys(snapshot.memo.errors).length > 0) hasError = true;
                    if (snapshot?.effects?.dispatches) {
                        snapshot.effects.dispatches.forEach(d => {
                            if (d.name === 'toast' && (d.params?.[0]?.icon === 'error' || d.params?.[0]?.type === 'error')) hasError = true;
                        });
                    }
                    window.dispatchEvent(new CustomEvent('topbar-end', { detail: hasError ? 'error' : 'success' }));
                });
            });
        });
    </script>

    <style>
        @font-face {
            font-family: 'NothingFont';
            src: url('/fonts/nothing-font-5x7.otf.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }

        .nothing-font {
            font-family: 'NothingFont', monospace;
        }

        @keyframes marquee {

            0%,
            15% {
                transform: translateX(0);
            }

            85%,
            100% {
                transform: translateX(calc(-100% + 200px));
            }
        }

        .animate-marquee {
            animation: marquee 8s linear infinite;
            display: inline-block;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</nav>