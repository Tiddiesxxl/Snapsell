// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  ssr: true,

  css: [
    '~/assets/styles/main.css',
    'vue-toastification/dist/index.css',
  ],

  app: {
    head: {
      link: [
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Bitter:ital,wght@0,100..900;1,100..900&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
        },
      ],
    }
  },

  modules: [
    '@nuxtjs/kinde',
    '@vite-pwa/nuxt'
  ],

  plugins: [
    { src: '~/plugins/toast.client.js', mode: 'client' },
    '~/plugins/fontawesome.ts'
  ],

  build: {
    transpile: [
      '@fortawesome/vue-fontawesome',
      '@fortawesome/fontawesome-svg-core',
      '@fortawesome/free-solid-svg-icons',
    ]
  },

  routeRules: {
    '/dashboard/**': { ssr: false },
    '/login': { ssr: true },
    '/register': { ssr: true }
  },

  appConfig: {
    pwa: {
      registerType: 'autoUpdate',
      manifest: {
        name: 'Snap Sell',
        short_name: 'SnapSell',
        description: 'Your Photography Marketplace',
        theme_color: '#11101D',
        icons: [
          {
            src: 'icons/icon-64.png',
            sizes: '64x64',
            type: 'image/png'
          },
          {
            src: 'icons/icon-144.png',
            sizes: '144x144',
            type: 'image/png'
          },
          {
            src: 'icons/icon-512.png',
            sizes: '512x512',
            type: 'image/png'
          }
        ]
      },
      workbox: {
        navigateFallback: '/',
        globPatterns: ['**/*.{js,css,html,png,jpg,svg}'],
        runtimeCaching: [
          {
            urlPattern: /^https:\/\/api\.your-domain\.com\/.*/,
            handler: 'NetworkFirst',
            options: {
              cacheName: 'api-cache',
              expiration: {
                maxEntries: 100,
                maxAgeSeconds: 60 * 60 * 24
              }
            }
          }
        ]
      },
      client: {
        installPrompt: true,
      },
      devOptions: {
        enabled: true,
        type: 'module'
      }
    }
  }
})