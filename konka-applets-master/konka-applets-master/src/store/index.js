import Vue from 'vue'
import Vuex from 'vuex'
import Basic from './basic.js'
import Oauth from './oauth.js'
import Order from './order.js'

Vue.use(Vuex)

let store = new Vuex.Store({
  modules: {
    Basic,
    Oauth,
    Order
  }
})

console.log('store/index.js')

export default store
