/** @type {import('tailwindcss').Config} */
module.exports = {
    mode: "jit",
    prefix: "tw-",
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    corePlugins: {
        preflight: false,
    },
    theme: {
        screens: {
            xs: "425px",
            sm: "640px",
            md: "768px",
            lg: "1024px",
            xl: "1280px",
            "2xl": "1536px",
        },
        extend: {
            colors: {
                primary: {
                    '900': 'hsla(216, 100%, 20%, 1)',
                    '600': 'hsla(216, 100%, 40%, 1)',
                    '500': 'var(--primary-500)',
                    '400': 'hsla(216, 100%, 60%, 1)',
                    '300': 'hsla(216, 100%, 60%, 1)',
                    '200': 'hsla(216, 100%, 80%, 1)',
                    '100': 'hsla(216, 100%, 93%, 1)',
                    '50': '#E7F0FA',
                },
            },
        },
    },
    plugins: [],
};
