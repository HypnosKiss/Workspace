
export default {
  state: {
    getPhoneNumberStatus: false,
    getPhoneNumberToPage: '',
    // K币相关搜索
    getSearchStatus: false
  },

  getters: {
    getPhoneNumberStatus: (state, getters) => {
      return state.getPhoneNumberStatus
    },
    getPhoneNumberToPage: (state, getters) => {
      return state.getPhoneNumberToPage
    },
    getSearchStatus: (state) => {
      return state.getSearchStatus
    }
  },

  mutations: {
    setPhoneNumberStatus (state, val) {
      state.getPhoneNumberStatus = val
    },
    setPhoneNumberToPage (state, val) {
      state.getPhoneNumberToPage = val
    },
    setSearchStatus (state, val) {
      state.getSearchStatus = val
    }
  },
  actions: {
  }
}
