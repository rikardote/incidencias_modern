<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center gap-4">
                <x-user-avatar :avatar="Auth::user()->avatar" :name="Auth::user()->name" size="w-16 h-16"
                    iconSize="w-9 h-9" />
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-1">Bienvenido, {{
                        Auth::user()->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">Panel de accesos rápidos</p>
                </div>
            </div>

            {{-- Widgets Reactivos --}}
            @livewire('dashboard.stats-widgets')

            @if(auth()->user()->admin())
            <div class="mb-12">
                <h4
                    class="text-xs font-black text-[#9b2247] dark:text-[#e6d194] uppercase tracking-[0.2em] mb-6 border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-oro animate-pulse"></span>
                    Centro de Control Administrativo
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Card Usuarios -->
                    <a href="{{ route('users.index') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Usuarios</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Gestión de
                                accesos</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Quincenas -->
                    <a href="{{ route('qnas.index') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Quincenas</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Control de
                                Ciclos</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Codigos -->
                    <a href="{{ route('codigos-incidencia.index') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Códigos</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Catálogo
                                maestro</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Periodos -->
                    <a href="{{ route('catalogos.periodos') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Periodos</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Vacaciones</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Horarios -->
                    <a href="{{ route('catalogos.horarios') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Horarios</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Entradas/Salidas</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Jornadas -->
                    <a href="{{ route('catalogos.jornadas') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Jornadas</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Días laborales</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .596-.404 1.085-1 1.148-1.55.163-3.125.252-4.75.252s-3.2-.089-4.75-.252c-.596-.063-1-.552-1-1.148v-4.25m9 0a1.5 1.5 0 1 0-3 0m-9 0a1.5 1.5 0 1 0-3 0m12 0v-3.192c1.235-.46 2.25-1.509 2.25-2.808 0-1.299-1.015-2.348-2.25-2.808V3.033c0-.422-.43-.703-.832-.575a22.25 22.25 0 0 0-10.836 0c-.402.128-.832.409-.832.575v1.109c-1.235.46-2.25 1.509-2.25 2.808 0 1.299 1.015 2.348 2.25 2.808v3.192m12 0h-12" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Puestos -->
                    <a href="{{ route('catalogos.puestos') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Puestos</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Catálogo maestro</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Biometrico -->
                    <a href="{{ route('biometrico.sync') }}"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Biométrico</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Sincronización</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Plantilla -->
                    <a href="http://plantilla.issstebc.gob.mx" target="_blank"
                        class="md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 hover:border-oro/30 transition-all group">
                        <div>
                            <h5
                                class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Plantilla</h5>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Acceso Externo</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:bg-oro/10 transition-colors shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro transition-transform group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Otras Opciones (Unificado) -->
                    <div class="md:col-span-2">
                        <livewire:system.system-settings-manager />
                    </div>

            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card Empleados -->
                <a href="{{ route('employees.index') }}"
                    class="mt-4 md:mt-0 md:col-span-2 flex items-center justify-between p-6 bg-[#13322B] dark:bg-gray-800 rounded-xl shadow-sm border border-[#0a1f1a] dark:border-gray-700 hover:shadow-lg hover:-translate-y-1 transition-all group relative overflow-hidden">
                    <div class="absolute right-0 top-0 -mr-6 -mt-6">
                        <svg class="w-24 h-24 text-white opacity-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                            </path>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <h5 class="text-lg font-bold text-white group-hover:text-[#e6d194] transition-colors">Empleados
                        </h5>
                        <p class="text-sm text-gray-300 dark:text-gray-400 mt-1">Directorio y Captura</p>
                    </div>
                </a>

                <!-- Card Reportes -->
                <a href="{{ route('reports.rh5') }}"
                    class="mt-4 md:mt-0 md:col-span-2 flex items-center justify-between p-6 bg-[#9b2247] dark:bg-gray-800 rounded-xl shadow-sm border border-[#611232] dark:border-gray-700 hover:shadow-lg hover:-translate-y-1 transition-all group relative overflow-hidden">
                    <div class="absolute right-0 top-0 -mr-6 -mt-6">
                        <svg class="w-24 h-24 text-white opacity-10" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <h5 class="text-lg font-bold text-white group-hover:text-[#e6d194] transition-colors">Reportes
                        </h5>
                        <p class="text-sm text-gray-200 dark:text-gray-400 mt-1">Generación de RH5</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>