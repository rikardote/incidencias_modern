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
                activeStyle: localStorage.getItem('island_style') || 'classic',
                showFaces: localStorage.getItem('island_show_faces') !== 'false',
                setStyle(style) {
                    this.activeStyle = style;
                    localStorage.setItem('island_style', style);
                },
                setFaces(show) {
                    this.showFaces = show;
                    localStorage.setItem('island_show_faces', show);
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
                        window.dispatchEvent(new CustomEvent('live-log-new'));

                        // Trigger internal component refresh if it exists
                        window.dispatchEvent(new CustomEvent('live-log-refresh'));
                    });
            }

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
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('island-notif', {
                        detail: {
                            message: data.title || (data.icon === 'error' ? 'Error' : 'Aviso'),
                            type: data.icon || 'info'
                        }
                    }));
                }, 50);

                if (data.icon === 'success') {
                    localStorage.setItem('biometrico_refresh', Date.now());
                }
            });

            // Bridge para Sincronización de Estilo (Dynamic Island)
            Livewire.on('island-style-updated', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                const style = data.style;
                // Actualizar store global de Alpine
                if (window.Alpine) {
                    Alpine.store('island').setStyle(style);
                }
            });

            // Escuchar cambios en otras pestañas
            window.addEventListener('storage', (event) => {
                if (event.key === 'island_style' && window.Alpine) {
                    Alpine.store('island').setStyle(event.newValue);
                }
            });
        });
    </script>

    <div x-data="{ 
         openBitacora: false, 
         newBitacoraCount: 0 
     }" x-on:toggle-live-log.window="openBitacora = !openBitacora; newBitacoraCount = 0"
        x-on:live-log-new.window="if(!openBitacora) newBitacoraCount++">

        <!-- Toggle Button (Siempre visible) -->
        <div class="fixed bottom-6 right-24 z-[60]">
            <button @click="openBitacora = !openBitacora; newBitacoraCount = 0"
                class="group relative w-12 h-12 bg-[#13322B] hover:bg-[#0a1f1a] text-oro rounded-full shadow-2xl border border-oro/20 flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95">

                <template x-if="newBitacoraCount > 0">
                    <span class="absolute -top-1 -right-1 flex h-5 w-5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-5 w-5 bg-red-500 text-[10px] text-white font-black flex items-center justify-center"
                            x-text="newBitacoraCount"></span>
                    </span>
                </template>

                <svg class="w-5 h-5 transition-transform duration-500" :class="openBitacora ? 'rotate-90' : ''"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!openBitacora" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    <path x-show="openBitacora" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <livewire:admin.live-capture-log />
    </div>

    @livewire('chat-widget')
    @livewireScripts
</body>

</html>