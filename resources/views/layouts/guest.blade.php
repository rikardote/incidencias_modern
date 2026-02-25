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
    <body class="font-sans antialiased bg-[#f4f6f9] dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8">
                <a href="/">
                    <x-application-logo class="w-auto h-20" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-10 py-10 bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-[#13322B] dark:bg-[#1e5b4f]"></div>
                <div class="mb-8 text-center mt-2">
                    <h1 class="text-2xl font-bold text-[#333333] dark:text-gray-100 uppercase tracking-wide">Sistema de Incidencias</h1>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-1">Gobierno de México</p>
                </div>
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">
                Gobierno de México &copy; {{ date('Y') }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>
