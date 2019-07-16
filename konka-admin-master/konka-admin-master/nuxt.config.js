require('dotenv').config()
const basePath = process.env.BASE_PATH || '/admin'
const apiPrefix = process.env.API_PREFIX || '/api'
module.exports = {
  mode: 'universal',
  head: {
    title: '康佳优品管理后台',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '康佳优品管理后台' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: basePath + '/favicon.ico' }
    ],
    script: [
      { src: basePath + '/shim.min.js' },
      { src: basePath + '/xlsx.full.min.js' }
    ]
  },
  loading: { color: '#fff' },
  css: ['normalize.css/normalize.css', 'element-ui/lib/theme-chalk/index.css'],
  plugins: ['@/plugins/element-ui', '@/plugins/axios'],
  router: {
    base: basePath,
    middleware: ['auth']
  },
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/auth',
    ['@nuxtjs/dotenv', { systemvars: true }]
  ],
  auth: {
    strategies: {
      local: {
        endpoints: {
          login: {
            url: '/admin/login',
            method: 'post',
            propertyName: 'accessToken'
          },
          logout: { url: '/admin/logout', method: 'post' },
          user: { url: '/admin/user', method: 'get', propertyName: null }
        },
        tokenType: '',
        tokenName: 'X-Access-Token'
      }
    }
  },
  axios: {
    prefix: basePath + apiPrefix,
    proxy: true
  },
  build: {
    transpile: [/^element-ui/],
    extend(config, ctx) {
      // Run ESLint on save
      if (ctx.isDev && ctx.isClient) {
        config.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /(node_modules)/
        })
      }
    }
  }
}
