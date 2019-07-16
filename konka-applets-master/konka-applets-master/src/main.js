import Vue from 'vue'
import App from './App'
import Utils from '@/utils/index'
import Store from '@/store/index'

Vue.config.productionTip = false
Vue.prototype.$utils = Utils
Vue.prototype.$store = Store
App.mpType = 'app'

const app = new Vue(App)
app.$mount()
