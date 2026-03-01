import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                guinda: {
                    DEFAULT: '#9b2247',
                    dark: '#611232',
                },
                oro: {
                    DEFAULT: '#a57f2c',
                    light: '#e6d194',
                },
                verde: {
                    DEFAULT: '#1e5b4f',
                    dark: '#002f2a',
                },
            },
        },
    },

    plugins: [forms],
};
