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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'premium': '0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                'premium-hover': '0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.03)',
                'glass': 'inset 0 0 0 1px rgba(255, 255, 255, 0.1)',
            },
            colors: {
                primary: {
                    50: '#f0f4ff',
                    100: '#e1e9ff',
                    200: '#c3d3ff',
                    300: '#a5bcff',
                    400: '#87a6ff',
                    500: '#4f46e5', // Indigo-600 ish
                    600: '#4338ca',
                    700: '#3730a3',
                    800: '#312e81',
                    900: '#1e1b4b',
                },
                slate: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                }
            },
        },
    },

    plugins: [forms],
};
