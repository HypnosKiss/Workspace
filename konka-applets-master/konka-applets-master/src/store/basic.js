import Utils from '@/utils/index.js'

export default {
  state: {
    isIpx: false,
    wxSetting: {},
    defaultUserHeader: require('@static/image/tab_mine_active.png'),
    accessToken: Utils.getAccessToken(),
    partnerCode: '',
    systemSetting: {},
    userInfo: {
      id: '',
      code: '',
      username: '',
      password: '',
      phone: '',
      email: '',
      avatar: '',
      nickname: '',
      sex: '',
      status: '',
      createdAt: '',
      updatedAt: '',
      statusText: '',
      isPartner: '',
      defaultAddresses: '',
      defaultInvoices: '',
      orders: {
        hasSend: '',
        refunds: '',
        waitPay: '',
        waitSend: ''
      },
      partner: {
        availableKMoney: 0,
        code: '',
        hasWithdrawKMoney: 0,
        inviteCode: '',
        isBind: 20,
        monthSales: 0,
        myTeamCount: 0,
        saleOrders: 0,
        totalKMoney: 0,
        waitSettlementKMoney: 0,
        yearSales: 0
      }
    }
  },

  getters: {
    isIpx: (state, getters) => {
      return state.isIpx
    },
    addressScope: (state, getters) => {
      return state.wxSetting['scope.address'] || false
    },
    defaultUserHeader: (state, getters) => {
      return state.defaultUserHeader
    },
    partnerCode: (state, getters) => {
      return state.partnerCode
    },
    accessToken: (state, getters) => {
      return state.accessToken
    },
    hadLogin: (state, getters) => {
      return !!state.accessToken
    },
    userInfo: (state, getters) => {
      return state.userInfo
    }
  },

  mutations: {
    setIsIpx (state, val) {
      state.isIpx = val
    },
    setWxSetting (state, val) {
      state.wxSetting = val
    },
    setAccessToken (state, val) {
      state.accessToken = val
    },
    setPartnerCode (state, val) {
      state.partnerCode = val
    },
    setUserInfo (state, val) {
      state.userInfo = val
    },
    setSystemSetting (state, value) {
      console.log(value)
      state.systemSetting = value
    }
  },
  actions: {
    getWxSetting ({commit, getters}) {
      wx.getSetting({
        success (res) {
          console.log(res.authSetting)
          // res.authSetting = {
          //   "scope.userInfo": true,
          //   "scope.userLocation": true
          // }
          if (!res.authSetting) {
            return
          }
          commit('setWxSetting', res.authSetting)
        }
      })
    },
    logout ({commit, getters}) {
      console.log('store logout')
      commit('setAccessToken', '')
      Utils.logout()
    },
    checkIpx ({commit, getters}) {
      console.log('checkIpx')
      wx.getSystemInfo({
        success (res) {
          console.log('is iPhone X', res.model)
          var model = res.model
          if (model.search('iPhone X') !== -1) {
            commit('setIsIpx', true)
          } else {
            commit('setIsIpx', false)
          }
        }
      })
    },
    getUserInfo ({commit, getters}) {
      console.log('getUserInfo')
      let options = {
        url: `/current`,
        method: 'get'
      }

      Utils.requestServer(options)
        .then(data => {
          console.log(data)
          commit('setUserInfo', data)
        })
        .catch(err => {
          Utils.showModal('获取用户信息错误，', err)
        })
    }
  }
}
