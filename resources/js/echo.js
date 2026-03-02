import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],

    // --- CONFIGURACIÓN DE RESILIENCIA ---
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    cluster: 'mt1', // Reverb usa esto por compatibilidad con Pusher
    autoConnect: true,
    // Intenta reconectar cada vez más lento si falla (evita saturar el servidor)
    backoff: {
        initialDelay: 1000,
        maxDelay: 10000,
    },
});

// Listener para depuración en consola (Opcional, puedes quitarlo en producción)
window.Echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log(`Estado de Reverb: ${states.current}`);
});