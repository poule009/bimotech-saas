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
                'bimo-bg':        '#F4F4F5',
                'bimo-bg2':       '#E9E9EA',
                'bimo-surface':   '#FFFFFF',
                'bimo-navy':      '#7B1E3A',
                'bimo-navy-dk':   '#641630',
                'bimo-text':      '#111111',
                'bimo-gold':      '#6B7280',
                'bimo-red':       '#EF4444',
            },
            fontFamily: {
                'display': ['Poppins', ...defaultTheme.fontFamily.sans],
                'body':    ['Poppins', ...defaultTheme.fontFamily.sans],
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
