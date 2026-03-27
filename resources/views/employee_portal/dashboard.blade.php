<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mi Panel (Portal Empleado)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-2 mb-3">
                            ¡Bienvenido, {{ $empleado->fullname }}!
                        </h3>
                        <p class="text-sm text-gray-600 mb-5">
                            Desde este portal podrás consultar tu información general y revisar tu historial reciente de registros biométricos.
                        </p>
                        
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg mb-2">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-blue-800">Mantente al tanto de tus checadas</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li><strong>Notificaciones web:</strong> Acepta los permisos de tu navegador para recibir alertas directamente en tu celular, sin necesidad de tener el portal abierto.</li>
                                            <li><strong>Bot de Telegram:</strong> Vincula tu cuenta usando el botón inferior y recibe tus checadas al instante en la aplicación de Telegram.</li>
                                        </ul>
                                        <p class="mt-3 font-semibold bg-blue-100 p-2 rounded-md">
                                            💡 Puedes activar **uno solo, o ambos** según prefieras. Son independientes.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex flex-col space-y-2">
                        <p><strong>N° Empleado:</strong> {{ $empleado->num_empleado }}</p>
                        <p><strong>RFC:</strong> {{ $empleado->rfc }}</p>
                        <p><strong>Departamento:</strong> {{ $empleado->department->description ?? 'N/A' }}</p>
                        <p><strong>Puesto:</strong> {{ $empleado->puesto->puesto ?? 'N/A' }}</p>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            @if($empleado->telegram_chat_id)
                                <div class="flex items-center gap-2 text-emerald-600 font-bold text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Telegram Vinculado
                                </div>
                            @else
                                <a href="{{ $empleado->getTelegramLinkUrl() }}" target="_blank" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#0088cc] hover:bg-[#0077b5] text-white rounded-lg text-sm font-bold transition-all shadow-lg shadow-blue-500/20">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.79 5.42-1.12 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.11.02-1.93 1.23-5.46 3.62-.51.35-.98.53-1.39.52-.46-.01-1.33-.26-1.98-.48-.8-.27-1.43-.42-1.37-.89.03-.25.38-.51 1.03-.78 4.04-1.76 6.74-2.92 8.09-3.48 3.85-1.6 4.64-1.88 5.17-1.89.11 0 .37.03.54.17.14.12.18.28.2.45-.02.07-.02.13-.03.2z"/>
                                    </svg>
                                    Vincular Telegram
                                </a>
                                <p class="mt-1 text-[10px] text-gray-400 italic">Recibe tus avisos de checada al instante en Telegram.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mis Checadas Biométricas</h3>
                    @livewire('biometrico.employee-attendance', ['employeeId' => $empleado->id, 'isPortal' => true])
                </div>
            </div>

        </div>
    </div>

    <!-- Firebase Push Notifications Logic -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>
    <script>
        // FIREBASE PUSH NOTIFICATIONS CONFIG
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.web.api_key') }}",
            authDomain: "{{ config('services.firebase.web.auth_domain') }}",
            projectId: "{{ config('services.firebase.web.project_id') }}",
            storageBucket: "{{ config('services.firebase.web.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.web.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.web.app_id') }}"
        };

        if (firebase.apps.length === 0) {
            firebase.initializeApp(firebaseConfig);
        }

        const messaging = firebase.messaging();

        function requestPermission() {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                    messaging.getToken({ vapidKey: "{{ config('services.firebase.web.vapid_key') }}" })
                    .then((currentToken) => {
                        if (currentToken) {
                            sendTokenToServer(currentToken);
                        } else {
                            console.log('No registration token available. Request permission to generate one.');
                        }
                    }).catch((err) => {
                        console.log('An error occurred while retrieving token. ', err);
                    });
                } else {
                    console.log('Unable to get permission to notify.');
                }
            });
        }

        function sendTokenToServer(token) {
            fetch("{{ route('employee.store_fcm_token') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: token })
            })
            .then(response => response.json())
            .then(data => console.log('Token registered on server:', data))
            .catch(error => console.error('Error saving token:', error));
        }

        messaging.onMessage((payload) => {
            console.log('Message received in foreground: ', payload);
            // Si quieres mostrar un Toast o algo en UI puedes hacerlo aquí
            // Pero quitamos el 'new Notification' para evitar duplicados si el navegador ya lo maneja
            // new Notification(payload.notification.title, { body: payload.notification.body });
        });

        // Solicitar permisos cuando carga la página
        window.onload = function() {
            console.log('Page loaded, checking for Service Worker support...');
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                    console.log('Service Worker registered with scope:', registration.scope);
                    requestPermission();
                }).catch((err) => {
                    console.error('Service Worker registration failed:', err);
                    alert('Error en Service Worker: ' + err.message + '. Asegúrate de estar usando HTTPS.');
                });
            } else {
                console.warn('Service Worker is not supported in this browser.');
            }
        };
    </script>
</x-app-layout>
