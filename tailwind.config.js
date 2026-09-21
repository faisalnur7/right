import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                upBreath: {
                '0%, 100%': { transform: 'translateY(0) scale(1)', opacity: '1' },
                '50%': { transform: 'translateY(-10px) scale(1.2)', opacity: '0.5' },
                },
            },
            animation: {
                upBreath: 'upBreath 1.2s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
