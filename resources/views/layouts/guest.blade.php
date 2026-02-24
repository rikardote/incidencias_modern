<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <!-- Scripts (Manual link to bypass stale hot-reload file) -->
        <link rel="stylesheet" href="{{ asset('dist/assets/app-CymDgtdw.css') }}">
        <script src="{{ asset('dist/assets/app-C9narHaO.js') }}" defer></script>

        <!-- Institutional Colors Shim -->
        <style>
            :root {
                --guinda: #9b2247;
                --guinda-dark: #611232;
                --oro: #a57f2c;
                --oro-light: #e6d194;
                --verde: #1e5b4f;
            }
            .bg-guinda { background-color: var(--guinda) !important; }
            .bg-guinda-dark { background-color: var(--guinda-dark) !important; }
            .text-guinda { color: var(--guinda) !important; }
            .border-guinda { border-color: var(--guinda) !important; }
            .border-oro { border-color: var(--oro) !important; }
            .text-oro { color: var(--oro) !important; }
            .bg-oro { background-color: var(--oro) !important; }
            .focus\:border-oro:focus { border-color: var(--oro) !important; }
            .focus\:ring-oro:focus { --tw-ring-color: var(--oro) !important; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 bg-[url('https://www.gob.mx/cms/uploads/image/file/485038/pleca_gobmx.png')] bg-bottom bg-repeat-x">
            <div class="mb-8">
                <a href="/">
                    <x-application-logo class="w-auto h-20" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-10 py-12 bg-white shadow-2xl overflow-hidden sm:rounded-xl border-t-8 border-oro">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-black text-guinda tracking-tighter uppercase">Sistema de Incidencias</h1>
                    <p class="text-xs font-bold text-oro uppercase tracking-widest mt-1">Identidad Institucional 2024-2030</p>
                </div>
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">
                Gobierno de México &copy; {{ date('Y') }}
            </div>
        </div>
    </body>
</html>
