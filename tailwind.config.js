/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './includes/**/*.php',
        './templates/**/*.php',
        './assets/src/**/*.{js,jsx}',
    ],
    prefix: 'cf-',
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eef5ff',
                    100: '#d9e8ff',
                    200: '#bcd7ff',
                    300: '#8ebdff',
                    400: '#5999ff',
                    500: '#4361ee',
                    600: '#2541e3',
                    700: '#1d2fd0',
                    800: '#1e28a9',
                    900: '#1e2785',
                },
                secondary: {
                    50: '#edfdf7',
                    100: '#d1fae8',
                    200: '#a7f3d6',
                    300: '#6ee7bf',
                    400: '#34d399',
                    500: '#06d6a0',
                    600: '#05a079',
                    700: '#047d5f',
                    800: '#06624c',
                    900: '#065140',
                },
            },
            fontFamily: {
                sans: [
                    '-apple-system',
                    'BlinkMacSystemFont',
                    '"Segoe UI"',
                    'Roboto',
                    'Oxygen-Sans',
                    'Ubuntu',
                    'Cantarell',
                    '"Helvetica Neue"',
                    'sans-serif',
                ],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
