import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';
import { visualizer } from "rollup-plugin-visualizer";

export default defineConfig({
    plugins: [
        vue(),
        visualizer(),
        laravel({
            input: [
                'resources/frontend/sass/app.scss',
                'resources/frontend/app.css',
                'resources/frontend/public.css',
                'resources/backend/app.css',
                'resources/frontend/js/app.js',
                'resources/frontend/js/public.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    },
    build: {
        chunkSizeWarningLimit: 5000,
      },
});
