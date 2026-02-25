<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script>
            function setDarkModePreference() {
                if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
            setDarkModePreference();
            document.addEventListener('livewire:navigated', setDarkModePreference);
        </script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

    </head>
    <body class="font-sans antialiased text-gray-800 dark:text-gray-200">
        <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-950 relative overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
            <div class="absolute inset-0 bg-[#13322B]/80 dark:bg-gray-950/90 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#13322B] via-transparent to-transparent opacity-80"></div>
            
            <div class="w-full max-w-5xl flex flex-col md:flex-row bg-white/10 dark:bg-white/5 backdrop-blur-md shadow-[0_8px_32px_0_rgba(0,0,0,0.37)] border border-white/20 rounded-2xl overflow-hidden min-h-[600px] z-10 mx-4">
                
                <!-- Left Side: Branding -->
                <div class="w-full md:w-1/2 p-12 flex flex-col justify-between relative overflow-hidden text-white bg-gradient-to-br from-[#13322B]/90 to-[#0a1f1a]/90 backdrop-blur-sm">
                    <div class="absolute -bottom-24 -left-24 opacity-10 blur-sm pointer-events-none">
                        <img src="{{ asset('images/60issste.png') }}" class="w-[500px] h-auto grayscale invert" />
                    </div>
                    
                    <div class="relative z-20">
                        <div class="bg-white p-4 rounded-xl inline-block shadow-lg">
                            <img src="{{ asset('images/60issste.png') }}" alt="Logo ISSSTE" class="h-14 w-auto drop-shadow-sm" />
                        </div>
                        <div class="mt-12">
                            <h2 class="text-4xl sm:text-5xl font-extrabold leading-tight tracking-tight drop-shadow-md">Gestión de<br><span class="text-oro">Incidencias</span></h2>
                            <p class="mt-4 text-gray-200 text-lg font-light leading-relaxed max-w-sm drop-shadow-sm">Plataforma administrativa para el control de asistencia de la Representación Estatal Baja California.</p>
                        </div>
                    </div>

                    <div class="relative z-20 mt-12 pb-2">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-1 bg-gradient-to-r from-oro to-oro-light rounded-full shadow-[0_0_10px_rgba(165,127,44,0.5)]"></div>
                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-oro drop-shadow-sm">Recursos Humanos</span>
                        </div>
                        <p class="mt-8 text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] drop-shadow-sm">
                            Gobierno de México &copy; {{ date('Y') }}
                        </p>
                    </div>
                </div>

                <!-- Right Side: Interaction -->
                <div class="w-full md:w-1/2 p-12 flex flex-col justify-center bg-white dark:bg-gray-900 shadow-[-10px_0_30px_rgba(0,0,0,0.1)] z-20">
                    <div class="w-full max-w-sm mx-auto">
                        <div class="mb-10 block md:hidden bg-white p-3 rounded-lg inline-block shadow-sm">
                            <img src="{{ asset('images/60issste.png') }}" class="h-10 w-auto" />
                        </div>
                        
                        <div class="mb-10 text-center md:text-left">
                            <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-2">Bienvenido</h3>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ingresa tus credenciales institucionales.</p>
                        </div>
                        
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
