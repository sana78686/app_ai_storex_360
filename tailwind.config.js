/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                /** Brand UI font — load Ubuntu in resources/css/app.css */
                sans: ['Ubuntu', 'sans-serif'],
            },
            colors: {
        // brand: '#eb4925ff',
        brand:'#E46527', // 👈 apna brand color yahan
      },
        },
    },
    plugins: [],
};
