/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        screens: {
            'tablet': '640px',
            'laptop': '1024px',
            'desktop': '1280px',
        },
        darkMode: 'class',
        container: {
            center: true,
        },
        extend: {},
        plugins: [
            require('flowbite/plugin')
        ],
  },
  plugins: [],
}

