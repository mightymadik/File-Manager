import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        hmr: {
            host: 'localhost'
        }
    },
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/list.css', 'resources/js/list.js'],
            refresh: true,
        }),
    ],
});
