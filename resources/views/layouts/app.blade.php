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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
            // Centralized Echo Listener
            if (typeof Echo !== 'undefined') {
                Echo.private('chat')
                    .listen('.NewIncidenciaBatchCreated', (e) => {
                        console.log('BATCH RECEPTION (LAYOUT):', e);

                        // Increment badge in layout's Alpine store
                        if (window.Alpine) {
                            Alpine.store('island').incrementLog();
                        }

                        // Show Toast instead of Island Notif
                        if (window.Toast) {
                            window.Toast.fire({
                                icon: 'info',
                                title: 'Nuevas incidencias detectadas'
                            });
                        }

                        // Trigger internal component refresh if it exists
                        window.dispatchEvent(new CustomEvent('live-log-refresh'));
                    });
            }

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

    <div x-data x-on:toggle-live-log.window="$store.island.toggleLog()">


        <livewire:admin.live-capture-log />
    </div>

    @livewire('chat-widget')
    @livewireScripts
</body>

</html>