import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue'; // ← 追加


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js', 'resources/js/main.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(), // ← 追加
    ],
});
