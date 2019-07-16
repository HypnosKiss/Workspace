
export default {
  state: {
    placeProducts: [],
    address: '',
    invoice: '',
    // 需要线下转账付款 银行信息
    orderOffline: {}
  },

  getters: {
    placeProducts: (state, getters, rootState, rootGetters) => {
      return state.placeProducts
    },
    orderAddress: (state, getters, rootState, rootGetters) => {
      return state.address || rootState.Basic.userInfo.defaultAddresses || ''
    },
    orderInvoice: (state, getters, rootState, rootGetters) => {
      return state.invoice || rootState.Basic.userInfo.defaultInvoices || ''
    },
    orderOffline: (state) => {
      return state.orderOffline
    }
  },

  mutations: {
    setPlaceProducts (state, val) {
      console.log('setPlaceProducts===>', val)
      state.placeProducts = val
    },
    setAddress (state, val) {
      state.address = val
    },
    setInvoice (state, val) {
      state.invoice = val
    },
    setOrderOffline (state, val) {
      state.orderOffline = val
    }
  },
  actions: {
  }
}
