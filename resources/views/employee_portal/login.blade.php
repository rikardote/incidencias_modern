<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Incidencias') }} - Empleado Login</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col selection:bg-[#13322B] selection:text-white">
        
        <header class="w-full p-6 lg:p-10 flex items-center justify-end absolute top-0 z-10 pointer-events-none">
            <div class="hidden sm:block text-xs font-bold text-gray-500 uppercase tracking-widest bg-white/50 px-4 py-2 rounded-full backdrop-blur-sm pointer-events-auto shadow-sm">
                ISSSTE BAJA CALIFORNIA &copy; {{ date('Y') }}
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col md:flex-row min-h-screen items-stretch relative overflow-hidden">
            
            <!-- Global Background Text Overlay (crossing panels) -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] md:opacity-[0.03] pointer-events-none select-none z-10 overflow-hidden">
                <span class="text-[20rem] lg:text-[28rem] font-black tracking-tighter text-gray-400 dark:text-white transform -rotate-12 whitespace-nowrap uppercase">ISSSTE</span>
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
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-2 tracking-tight">
                        Portal de<br>
                        <span class="text-[#C4A462]">Empleados</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-gray-300 mt-6 leading-relaxed font-medium">
                        Consulta rápida de registros de asistencia, incidencias, vacaciones y avisos institucionales.
                    </p>
                    
                    <a href="{{ route('welcome') }}" class="inline-flex items-center mt-12 text-[#C4A462] hover:text-white transition-colors text-sm font-bold uppercase tracking-wider backdrop-blur-sm bg-white/5 py-3 px-6 rounded-lg border border-white/10 hover:bg-white/10">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Volver a Inicio
                    </a>
                </div>
            </div>

            <!-- Right Side / Form -->
            <div class="w-full md:w-7/12 lg:w-1/2 bg-white dark:bg-gray-900 p-8 lg:p-20 flex flex-col justify-center items-center relative">
                
                <div class="w-full max-w-md">
                    <div class="mb-10 block text-center md:text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Iniciar Sesión</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Ingresa tu número de empleado y contraseña.</p>
                    </div>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('employee.login') }}" class="space-y-6">
                        @csrf

                        <!-- Num Empleado -->
                        <div>
                            <label for="num_empleado" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Número de Empleado</label>
                            <input id="num_empleado" class="block w-full py-3 px-4 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shadow-sm focus:border-[#C4A462] focus:ring focus:ring-[#C4A462] focus:ring-opacity-50 transition-colors" type="text" name="num_empleado" :value="old('num_empleado')" required autofocus autocomplete="username" placeholder="Ej: 123456" />
                            <x-input-error :messages="$errors->get('num_empleado')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Contraseña</label>
                            <input id="password" class="block w-full py-3 px-4 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shadow-sm focus:border-[#C4A462] focus:ring focus:ring-[#C4A462] focus:ring-opacity-50 transition-colors" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                            <p class="text-xs text-gray-400 mt-2 italic">Por defecto, tu contraseña es tu RFC impreso en mayúsculas.</p>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                                <input id="remember_me" type="checkbox" class="w-5 h-5 rounded border-gray-300 dark:border-gray-700 text-[#C4A462] shadow-sm focus:ring-[#C4A462] transition-all cursor-pointer" name="remember">
                                <span class="ms-3 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-[#13322B] transition-colors">Recordarme</span>
                            </label>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg shadow-[#C4A462]/20 text-sm font-bold text-white bg-[#C4A462] hover:bg-[#a3864d] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#C4A462] transition-all hover:-translate-y-1">
                                Entrar como Empleado
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </body>
</html>
