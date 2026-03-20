import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/dist/',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            buildDirectory: 'dist',
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        outDir: 'public/dist',
    },
});
