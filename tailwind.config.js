import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Chaque couleur pointe vers un token CSS défini dans resources/css/app.css.
 * On ne met JAMAIS de #hex ici : la source unique de vérité, ce sont les
 * variables --xxx du :root. Le format rgb(var(--x) / <alpha-value>) permet
 * d'utiliser les opacités Tailwind (ex. bg-teal/12).
 */
const token = (name) => `rgb(var(${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                ink:   token('--ink'),
                teal:  { DEFAULT: token('--teal'), deep: token('--teal-deep') },
                paper: { DEFAULT: token('--paper'), dim: token('--paper-dim') },
                gold:  { DEFAULT: token('--gold'), soft: token('--gold-soft') },
                green: token('--green'),
                line:  token('--line'),
                muted: token('--muted'),
                error: token('--error'),
                amber: token('--amber'),
                crit:  token('--crit'),
            },
            fontFamily: {
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
                body:    ['Inter', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                DEFAULT: '10px',
                'stage': '20px',
            },
            boxShadow: {
                'stage': '0 40px 80px -30px rgba(15, 34, 37, 0.35)',
            },
        },
    },

    plugins: [
        forms({
            strategy: 'class',
        }),
    ],
};
