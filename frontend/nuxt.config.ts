export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },

    modules: ['nuxt-auth-sanctum'],

    sanctum: {
        baseUrl: 'http://localhost:8000', // Laravel API
    },

})