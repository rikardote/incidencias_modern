<x-guest-layout>
    <div class="min-h-[400px] flex flex-col items-center justify-center p-6">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-[#13322B] dark:text-oro tracking-tight mb-3">
                Sistema de Incidencias
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                Bienvenido al portal institucional. Por favor, selecciona tu área de acceso.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 w-full max-w-2xl">
            <!-- Admin Card -->
            <a href="{{ route('login') }}" class="group relative bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 hover:border-oro transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-[#13322B]/10 rounded-xl flex items-center justify-center mb-4 text-[#13322B] dark:text-oro">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Administración</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gestión de personal, incidencias y reportes generales.</p>
                </div>
                <div class="mt-6 flex items-center text-[#13322B] dark:text-oro text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                    Acceder <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            <!-- Employee Card -->
            <a href="{{ route('employee.login') }}" class="group relative bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 hover:border-oro transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-oro/10 rounded-xl flex items-center justify-center mb-4 text-[#13322B] dark:text-oro">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Empleados</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Consulta de checadas, información personal y avisos.</p>
                </div>
                <div class="mt-6 flex items-center text-oro text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                    Acceder <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>
        </div>
        
        <div class="mt-12 text-xs text-gray-400 uppercase tracking-widest font-bold">
            Institución de Seguridad Social
        </div>
    </div>
</x-guest-layout>
