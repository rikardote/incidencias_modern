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
                    <h3 class="text-lg font-medium text-gray-900">Bienvenido, {{ $empleado->name }} {{ $empleado->father_lastname }}</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        A continuación puedes consultar tu información, tus registros biométricos del mes y tu kardex histórico.
                    </p>
                    
                    <div class="mt-6 flex flex-col space-y-2">
                        <p><strong>N° Empleado:</strong> {{ $empleado->num_empleado }}</p>
                        <p><strong>RFC:</strong> {{ $empleado->rfc }}</p>
                        <p><strong>Departamento:</strong> {{ $empleado->department->description ?? 'N/A' }}</p>
                        <p><strong>Puesto:</strong> {{ $empleado->puesto->puesto ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mis Checadas Biométricas</h3>
                    @livewire('biometrico.employee-attendance', ['employeeId' => $empleado->id])
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
