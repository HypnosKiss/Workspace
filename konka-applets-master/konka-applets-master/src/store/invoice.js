
export default {
  state: {
    address: ''
  },

  getters: {
    invoiceAddress: (state, getters) => {
      return state.address || ''
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
    }
  },
  actions: {
  }
}
