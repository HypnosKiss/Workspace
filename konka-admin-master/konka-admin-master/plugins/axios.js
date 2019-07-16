const CryptoJs = require('crypto-js')
export default function({ $axios, app, store }) {
  $axios.interceptors.request.use(
    function(config) {
      store.commit('beginLoading')
      config.headers.bodyMD5 =
        config.data !== undefined
          ? CryptoJs.MD5(JSON.stringify(config.data)).toString(
              CryptoJs.enc.Base64
            )
          : ''
      return config
    },
    function(error) {
      store.commit('endLoading')
      return Promise.reject(error)
    }
  )
  $axios.interceptors.response.use(
    function(response) {
      store.commit('endLoading')
      response = response.data
      return response.code === 0 ? response : Promise.reject(response)
    },
    function(error) {
      store.commit('endLoading')
      if (error.response.data.code === 401) {
        error.response.data.message = '登录超时，请重新登录'
        app.$auth.redirect('login')
      }
      return Promise.reject(error.response.data)
    }
  )
}
