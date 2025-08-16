import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Listen on all interfaces
        port: 5173,      // Default Vite port
        hmr: {
            host: '192.168.1.100', // Your PC local IP
        },
    },
});
