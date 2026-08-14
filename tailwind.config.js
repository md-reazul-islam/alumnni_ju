import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                navy: {
                    50: '#f0f4fa',
                    100: '#dbe6f3',
                    200: '#b9cfe8',
                    300: '#8babd6',
                    400: '#5980bf',
                    500: '#3960a3',
                    600: '#2a4a85',
                    700: '#233c6c',
                    800: '#1c2f56',
                    900: '#101b35',
                    950: '#0a1224',
                },
                gold: {
                    50: '#fdf9ed',
                    100: '#faf0cd',
                    200: '#f4dd96',
                    300: '#edc55a',
                    400: '#e6ad30',
                    500: '#d4941f',
                    600: '#b57318',
                    700: '#915217',
                    800: '#78421a',
                    900: '#66381a',
                },
            },
            boxShadow: {
                soft: '0 1px 2px 0 rgb(16 27 53 / 0.04), 0 1px 3px 0 rgb(16 27 53 / 0.06)',
                card: '0 2px 8px -2px rgb(16 27 53 / 0.08), 0 1px 2px -1px rgb(16 27 53 / 0.04)',
                popover: '0 12px 32px -8px rgb(16 27 53 / 0.18)',
            },
            borderRadius: {
                xl: '0.875rem',
                '2xl': '1.25rem',
            },
        },
    },

    plugins: [forms, typography],
};
