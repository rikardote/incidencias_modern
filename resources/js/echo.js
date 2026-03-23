import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const wsPort = import.meta.env.VITE_REVERB_PORT && import.meta.env.VITE_REVERB_PORT !== 'null' && import.meta.env.VITE_REVERB_PORT !== ''
    ? import.meta.env.VITE_REVERB_PORT 
    : (window.location.port || (window.location.protocol === 'https:' ? 443 : 80));

console.log(`📡 Configurando Reverb en ${window.location.hostname}:${wsPort} (${window.location.protocol})`);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: wsPort,
    wssPort: wsPort,
    forceTLS: window.location.protocol === 'https:',
    enabledTransports: ['ws', 'wss'],
    cluster: 'mt1',
    autoConnect: true,
    backoff: {
        initialDelay: 1000,
        maxDelay: 10000,
    },
});

// Listener para depuración en consola (Opcional, puedes quitarlo en producción)
window.Echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log(`Estado de Reverb: ${states.current}`);
});