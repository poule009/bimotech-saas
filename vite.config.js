import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 'resources/js/app.js',
                // Site vitrine (marketing) — CSS/JS autonomes, indépendants du Tailwind de l'app.
                'resources/css/vitrine.css', 'resources/js/vitrine.js',
                // Vitrine publique par agence (BimoPortail v2) — CSS/JS autonomes.
                'resources/css/vitrine-agence.css', 'resources/js/vitrine-agence.js',
            ],
            refresh: true,
        }),
    ],
});
