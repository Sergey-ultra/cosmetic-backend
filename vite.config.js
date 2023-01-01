import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/admin.scss',
                'resources/css/app.scss',
                'resources/css/supplier.scss',
                'resources/js/Admin/admin.js',
                'resources/js/Supplier/supplier.js',
            ],
            refresh: true,
        }),
    ],
});
