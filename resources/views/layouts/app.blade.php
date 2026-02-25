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
    <body class="font-sans antialiased bg-[#f4f6f9] dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Toast global listener via Livewire event (plain JS) -->
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('toast', (data) => {
                    if (window.Toast) {
                        window.Toast.fire({
                            icon: data.icon,
                            title: data.title,
                            timer: data.icon === 'error' ? 5000 : 3000
                        });
                    }
                });
            });
        </script>
        @livewireScripts
    </body>
</html>
