<nav x-data="{ open: false }" class="bg-[#13322B] dark:bg-gray-950 border-b border-[#0a1f1a] dark:border-gray-800 shadow-md relative sticky top-0 z-50">
    {{-- Pleca superior (eliminada por ser redundante en dark bg) --}}
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-11 w-auto drop-shadow-sm" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')">
                        {{ __('Empleados') }}
                    </x-nav-link>

                    <!-- Dropdown Reportes -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="max">
                            <x-slot name="trigger">
                                <button class="h-16 inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('reports.*') ? 'border-white dark:border-oro text-white font-bold' : 'border-transparent text-gray-300 font-medium hover:text-white hover:border-gray-300' }} text-sm leading-5 focus:outline-none transition duration-150 ease-in-out uppercase tracking-wider">
                                    <div>REPORTES</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('reports.general')" class="border-b border-gray-100 dark:border-gray-700/50">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        {{ __('General (RH5)') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.sinderecho')" class="dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 border-b border-gray-100 dark:border-gray-700 py-3 uppercase text-xs font-bold tracking-wider hover:text-oro transition-colors">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        {{ __('Sin Derecho a Nota Buena') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('reports.kardex')" class="dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 py-3 uppercase text-xs font-bold tracking-wider hover:text-oro transition-colors">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        {{ __('Kárdex de Empleado') }}
                                    </div>
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <!-- Active QNA Notice -->
            @if($activeQna)
            <div class="flex items-center px-2 sm:px-4">
                <div class="flex items-center gap-2 sm:gap-3 bg-[#0a1f1a] dark:bg-gray-900 border border-oro/30 px-3 sm:px-4 py-1.5 rounded-full shadow-inner animate-fade-in">
                    <div class="flex items-center gap-1.5 border-r border-oro/20 pr-2 sm:pr-3">
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-oro opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-oro"></span>
                        </span>
                        <span class="hidden lg:inline text-[9px] font-bold text-gray-400 uppercase tracking-tighter">QNA Activa</span>
                        <span class="text-xs sm:text-sm font-black text-white leading-none">
                            {{ $activeQna->qna }}/{{ $activeQna->year }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Cierre:</span>
                        <span class="text-xs sm:text-sm font-black text-oro tracking-wide leading-none">
                            {{ $activeQna->cierre ? \Carbon\Carbon::parse($activeQna->cierre)->format('d/m/Y') : 'PENDIENTE' }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-200 bg-[#13322B] dark:bg-gray-950 hover:text-white hover:bg-[#0a1f1a] dark:hover:bg-gray-800 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{ isMaintenance: {{ \Illuminate\Support\Facades\Cache::get('capture_maintenance', false) ? 'true' : 'false' }} }" 
                                 @maintenance-updated.window="isMaintenance = $event.detail.mode"
                                 x-show="isMaintenance" 
                                 x-cloak
                                 class="flex items-center text-red-400 mr-3 px-2 py-0.5 rounded-full bg-red-900/30 border border-red-500/30" 
                                 title="Modo Mantenimiento Activo">
                                <svg class="h-4 w-4 mr-1 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-wider">Mantenimiento</span>
                            </div>
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="border-b border-gray-100 dark:border-gray-700/50">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ __('Perfil') }}
                            </div>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    {{ __('Cerrar Sesión') }}
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-[#0a1f1a] dark:hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')">
                {{ __('Empleados') }}
            </x-responsive-nav-link>
            <div class="px-4 py-2 mt-2">
                <div class="font-medium text-xs text-gray-400 uppercase tracking-widest pl-1">Reportes</div>
            </div>
            <x-responsive-nav-link :href="route('reports.general')" :active="request()->routeIs('reports.general')">
                <span class="pl-4">{{ __('General (RH5)') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.sinderecho')" :active="request()->routeIs('reports.sinderecho')">
                <span class="pl-4">{{ __('Sin Derecho a Nota Buena') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.kardex')" :active="request()->routeIs('reports.kardex')">
                <span class="pl-4">{{ __('Kárdex de Empleado') }}</span>
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-[#0a1f1a] dark:border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
