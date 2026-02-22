/** @type {import('tailwindcss').Config} */
export default {
    content: [
        // Only new Vue components — don't touch legacy Blade styles
        './resources/js/**/*.{vue,js}',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
    // Prefix to avoid conflicts with existing SCSS classes
    prefix: 'tw-',
};
