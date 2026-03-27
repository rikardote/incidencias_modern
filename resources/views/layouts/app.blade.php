<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <script>
        function setDarkModePreference() {
            @if(request()->is('empleado*'))
            document.documentElement.classList.remove('dark');
            @else
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            @endif
        }
        setDarkModePreference();
        document.addEventListener('livewire:navigated', setDarkModePreference);
    </script>
    <style>
        /* Prevent layout shift during Livewire updates */
        html { overflow-y: scroll; }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">



    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'], 'dist')
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

        .bg-guinda {
            background-color: var(--guinda) !important;
        }

        .bg-guinda-dark {
            background-color: var(--guinda-dark) !important;
        }

        .text-guinda {
            color: var(--guinda) !important;
        }

        .border-guinda {
            border-color: var(--guinda) !important;
        }

        .border-oro {
            border-color: var(--oro) !important;
        }

        .text-oro {
            color: var(--oro) !important;
        }

        .bg-oro {
            background-color: var(--oro) !important;
        }

        .focus\:border-oro:focus {
            border-color: var(--oro) !important;
        }

        .focus\:ring-oro:focus {
            --tw-ring-color: var(--oro) !important;
        }
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('island', {
                logCount: 0,
                logIsOpen: false,
                incrementLog() {
                    if (!this.logIsOpen) this.logCount++;
                },
                toggleLog() {
                    this.logIsOpen = !this.logIsOpen;
                    if (this.logIsOpen) this.logCount = 0;
                    window.dispatchEvent(new CustomEvent('toggle-live-log-internal', {
                        detail: { open: this.logIsOpen }
                    }));
                }
            });
        });

        document.addEventListener('livewire:initialized', () => {

            // Compatibility Bridge: Redirect Island Notifs to Toasts
            window.addEventListener('island-notif', (e) => {
                if (window.Toast) {
                    window.Toast.fire({
                        icon: e.detail.type || 'info',
                        title: e.detail.message || 'Aviso'
                    });
                }
            });

            Livewire.on('swal', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                Swal.fire({
                    title: data.title || 'Aviso',
                    text: data.text || '',
                    icon: data.icon || 'info',
                    confirmButtonText: data.confirmButtonText || 'Entendido',
                    confirmButtonColor: '#9b2247', // guinda
                });
            });

            // Bridge para Notificaciones
            Livewire.on('toast', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                if (window.Toast) {
                    window.Toast.fire({
                        icon: data.icon || 'info',
                        title: data.title || (data.icon === 'error' ? 'Error' : 'Aviso')
                    });
                }

                if (data.icon === 'success') {
                    localStorage.setItem('biometrico_refresh', Date.now());
                }
            });

        });
    </script>


    @if(!request()->is('empleado*'))
        @livewire('chat-widget')
    @endif
    @livewireScripts
</body>

</html>