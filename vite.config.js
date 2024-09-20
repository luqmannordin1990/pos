import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/js/app.js', 
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/admin/themes/lppsa.css',
                'resources/css/filament/admin/themes/neumorphism.css'],
            refresh: true,
        }),
    ],
});
