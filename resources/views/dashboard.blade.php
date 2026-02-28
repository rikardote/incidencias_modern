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

            @if(auth()->user()->admin())
            <div class="mb-8">
                <h4
                    class="text-xs font-bold text-[#9b2247] dark:text-[#e6d194] uppercase tracking-wider mb-4 border-b border-[#9b2247] dark:border-[#e6d194] pb-2">
                    Opciones de Administrador</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card Usuarios -->
                    <a href="{{ route('users.index') }}"
                        class="mt-4 md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-oro transition-all group">
                        <div>
                            <h5
                                class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Usuarios</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestión de cuentas y accesos</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center group-hover:bg-oro/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Quincenas -->
                    <a href="{{ route('qnas.index') }}"
                        class="mt-4 md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-oro transition-all group">
                        <div>
                            <h5
                                class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Quincenas</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Apertura y cierre de periodos</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center group-hover:bg-oro/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                    </a>

                    <!-- Card Codigos de Incidencia -->
                    <a href="{{ route('codigos-incidencia.index') }}"
                        class="mt-4 md:mt-0 flex items-center justify-between p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-oro transition-all group">
                        <div>
                            <h5
                                class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-oro transition-colors">
                                Códigos</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tipos de incidencias</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center group-hover:bg-oro/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-oro">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                        </div>
                    </a>

                    <!-- App Maintenance toggle -->
                    <div class="md:col-span-2 lg:col-span-2">
                        @livewire('system.maintenance-toggle')
                    </div>
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
                <a href="{{ route('reports.general') }}"
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