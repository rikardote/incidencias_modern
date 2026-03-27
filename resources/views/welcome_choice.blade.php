<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Incidencias') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col selection:bg-[#13322B] selection:text-white">
        
        <!-- Header / Return Link (if needed later) -->
        <header class="w-full p-6 lg:p-10 flex items-center justify-end absolute top-0 z-10 pointer-events-none">
            <div class="hidden sm:block text-xs font-bold text-gray-500 uppercase tracking-widest bg-white/50 px-4 py-2 rounded-full backdrop-blur-sm pointer-events-auto shadow-sm">
                ISSSTE BAJA CALIFORNIA &copy; {{ date('Y') }}
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col md:flex-row min-h-screen items-stretch relative overflow-hidden">
            
            <!-- Global Background Text Overlay (crossing panels) -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] md:opacity-[0.03] pointer-events-none select-none z-10 overflow-hidden">
                <span class="text-[20rem] lg:text-[35rem] font-black tracking-tighter text-gray-400 dark:text-white transform -rotate-12 whitespace-nowrap">ISSSTE</span>
            </div>
            
            <!-- Left Side / Hero Info -->
            <div class="w-full md:w-5/12 lg:w-1/2 bg-[#13322B] p-10 lg:p-20 flex flex-col justify-center relative overflow-hidden">
                <!-- Decorative Elements -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-white opacity-5 mix-blend-overlay"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-[#C4A462] opacity-10 blur-3xl"></div>
                
                <!-- Background Text Overlay -->
                <!-- No longer here, moved to main level -->
                
                <!-- Logo Top Left -->
                <div class="absolute top-8 left-8 lg:top-12 lg:left-12 z-20">
                    <img src="{{ asset('images/60issste.png') }}" alt="Logo Institucional" class="h-12 md:h-20 w-auto object-contain drop-shadow-lg opacity-90 hover:opacity-100 transition-opacity">
                </div>
                
                <div class="relative z-10 max-w-lg pt-16">
                    <div class="w-16 h-1 bg-[#C4A462] mb-8 rounded-full"></div>
                    <h1 class="text-4xl lg:text-6xl font-extrabold text-white leading-tight mb-2 tracking-tight">
                        Gestión de<br>
                        <span class="text-[#C4A462]">Incidencias</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-gray-300 mt-6 leading-relaxed font-medium">
                        Plataforma administrativa para el control de asistencia de la Representación Estatal Baja California.
                    </p>
                </div>
            </div>

            <!-- Right Side / Selection Cards -->
            <div class="w-full md:w-7/12 lg:w-1/2 bg-gray-50 dark:bg-gray-900 p-8 lg:p-20 flex flex-col justify-center items-center relative">
                
                <div class="w-full max-w-xl">
                    <div class="mb-12 text-center md:text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3">Bienvenido</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">Por favor, selecciona tu portal de acceso para continuar.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full">
                        
                        <!-- Admin Card -->
                        <a href="{{ route('login') }}" class="group block p-8 bg-white dark:bg-gray-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-700 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgb(19,50,43,0.08)] hover:border-[#13322B]/20 transition-all duration-300 relative overflow-hidden text-center md:text-left">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#13322B]/5 rounded-bl-[100px] transition-transform group-hover:scale-110"></div>
                            
                            <div class="w-14 h-14 bg-[#13322B]/10 rounded-2xl flex items-center justify-center mb-6 text-[#13322B] mx-auto md:mx-0 group-hover:bg-[#13322B] group-hover:text-white transition-colors">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 relative z-10">Administración</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 relative z-10 leading-relaxed">Gestión de personal, incidencias y reportes.</p>
                            
                            <div class="inline-flex items-center text-[#13322B] text-sm font-bold opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all">
                                Ingresar <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </a>

                        <!-- Employee Card -->
                        <a href="{{ route('employee.login') }}" class="group block p-8 bg-white dark:bg-gray-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-700 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgb(196,164,98,0.15)] hover:border-[#C4A462]/30 transition-all duration-300 relative overflow-hidden text-center md:text-left">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#C4A462]/5 rounded-bl-[100px] transition-transform group-hover:scale-110"></div>
                            
                            <div class="w-14 h-14 bg-[#C4A462]/10 rounded-2xl flex items-center justify-center mb-6 text-[#C4A462] mx-auto md:mx-0 group-hover:bg-[#C4A462] group-hover:text-white transition-colors">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 relative z-10">Empleados</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 relative z-10 leading-relaxed">Consulta de checadas, permisos y avisos.</p>
                            
                            <div class="inline-flex items-center text-[#C4A462] text-sm font-bold opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all">
                                Ingresar <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </a>

                    </div>
                </div>

            </div>
        </main>
    </body>
</html>
