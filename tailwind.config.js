/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.jsx",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        'xs': ['max(10px, 0.75rem)', { lineHeight: '1rem' }],
        'sm': ['max(10px, 0.875rem)', { lineHeight: '1.25rem' }],
      }
    },
  },
  plugins: [
    require('daisyui')
  ],
};