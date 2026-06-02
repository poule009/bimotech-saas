import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
                'bimo-bg':        '#F7F5F0',
                'bimo-bg2':       '#F0EDE6',
                'bimo-surface':   '#FFFFFF',
                'bimo-navy':      '#1B4F6B',
                'bimo-navy-dk':   '#163F56',
                'bimo-gold':      '#C9A84C',
                'bimo-red':       '#EF4444',
            },
            fontFamily: {
                'display': ['Syne', ...defaultTheme.fontFamily.sans],
                'body':    ['DM Sans', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                'card':  '14px',
                'btn':   '10px',
                'badge': '9999px',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
            },
            screens: {
                'xs': '375px',
            },
            boxShadow: {
                'gold-sm': '0 4px 16px rgba(201,168,76,0.25)',
                'gold-md': '0 8px 24px rgba(201,168,76,0.40)',
            },
            transitionDuration: {
                '150': '150ms',
                '250': '250ms',
            },
        },
    },

    plugins: [
        forms({
            strategy: 'class',
        }),
    ],
};
