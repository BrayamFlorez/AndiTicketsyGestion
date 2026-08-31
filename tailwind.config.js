import defaultTheme from 'tailwindcss/defaultTheme';

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
            colors: {
                current: 'currentColor',
                transparent: 'transparent',
                white: '#FFFFFF',
                black: '#1C2434',
                'black-2': '#010813',
                body: '#64748B',
                bodydark: '#AEB7C0',
                bodydark1: '#DEE4EE',
                bodydark2: '#8A99AD',
                primary: '#3C50E0',
                stroke: '#E2E8F0',
                gray: '#F7F8FA',
                graydark: '#333A48',
                boxdark: '#24303F',
                'boxdark-2': '#1A2232',
                strokedark: '#2E3A47',
                meta: {
                    1: '#DC3545',
                    2: '#EFF2F7',
                    3: '#10B981',
                    4: '#313D4A',
                    5: '#259219',
                    6: '#FFBA00',
                    7: '#FF6766',
                    8: '#F0950C',
                    9: '#E5E7EB',
                }
            },
        },
    },
    plugins: [],
};