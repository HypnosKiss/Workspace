import {mapGetters} from 'vuex'

export default {
  data () {
    return {
      queryEncodeStr: ''
    }
  },
  computed: {
    ...mapGetters([
      'isIpx',
      'addressScope',
      'defaultUserHeader',
      'hadLogin',
      'userInfo'
    ]),
    queryText () {
      return 132435
    },
    query () {
      if (this.queryEncodeStr) {
        console.log('query()====>')
        console.log(this.queryEncodeStr)
        return JSON.parse(this.queryEncodeStr)
      } else {
        return {}
      }
    }
  },
  onShareAppMessage () {
    return {
      title: this.$store.state.Basic.systemSetting.globalShareTitle,
      imageUrl: this.$store.state.Basic.systemSetting.globalShareImageUrl
    }
  },
  onLoad (query) {
    console.log('onload query=====>')
    console.log(query)
  },
  methods: {
    resetComponent () {
      Object.assign(this.$data, this.$options.data())
    },
    checkSession () {
      return new Promise((resolve, reject) => {
        wx.checkSession({
          success () {
            // session_key 未过期，并且在本生命周期一直有效
            console.log('session_key 未过期，并且在本生命周期一直有效')
            resolve(true)
          },
          fail () {
            // session_key 已经失效，需要重新执行登录流程
            console.log('session_key 已经失效，需要重新执行登录流程')
            resolve(false)
          }
        })
      })
    },
    getWxCode () {
      let that = this
      return new Promise((resolve, reject) => {
        wx.login({
          success (res) {
            console.log(res)
            if (!res.code) {
              that.$utils.error('获取微信code失败')
              reject(new Error('获取微信code失败'))
              return
            }
            resolve(res.code)
          }
        })
      })
    },
    getWxOpenid (code) {
      let that = this
      return new Promise((resolve, reject) => {
        if (!code) {
          reject(new Error(`参数code不能为空`))
          return
        }
        that.$utils.showLoading(`加载中`)

        let options = {
          url: `/${code}/openid`,
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(data => {
            that.$utils.hideLoading()
            console.log(data)
            that.$utils.setOpenid(data.openid)
            resolve(data.openid)
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`获取openid失败，${err}`)
            reject(new Error(`获取openid失败，${err}`))
          })
      })
    },
    openidLogin (openid) {
      let that = this
      return new Promise((resolve, reject) => {
        if (!openid) {
          reject(new Error(`参数openid不能为空`))
          return
        }
        that.$utils.showLoading(`加载中`)

        let options = {
          url: '/wx-openid-login',
          method: 'post',
          data: {
            openId: openid,
            type: 10
          }
        }
        that.$utils.requestServer(options)
          .then(data => {
            that.$utils.hideLoading()
            console.log(data)
            that.$store.commit('setAccessToken', data.accessToken)
            resolve(data.accessToken)
          })
          .catch(err => {
            that.$utils.hideLoading()
            reject(new Error(`自动登录失败，${err}`))
          })
      })
    },
    getWxUserInfo () {
      let that = this
      wx.getUserInfo({
        success (res) {
          let obj = {
            'encryptedData': res.encryptedData,
            'iv': res.iv,
            'openId': that.openid
          }
          console.log(JSON.stringify(obj))
          // console.log(res.userInfo);
          that.$store.commit('setWxUserInfo', res.userInfo)
          that.saveWxInfo({
            openId: that.openid,
            nickName: res.userInfo.nickName,
            avatarUrl: res.userInfo.avatarUrl
          })
        }
      })
    },
    openGetPhoneNumber (toPage) {
      this.$store.commit('setPhoneNumberStatus', true)
      if (toPage) this.$store.commit('setPhoneNumberToPage', toPage)
    },
    closeGetPhoneNumber () {
      this.$store.commit('setPhoneNumberStatus', false)
    },
    mountedNextTick () {
    },
    getQuery () {
      console.log('$root query=====>')
      console.log(this.$root.$mp.query)
      console.log()
      if (this.$root.$mp && this.$root.$mp.query && this.$root.$mp.query.queryEncodeStr) {
        console.log('queryEncodeStr=======>')
        console.log(this.$root.$mp.query.queryEncodeStr)
        return this.$utils.base64Decode(decodeURIComponent(this.$root.$mp.query.queryEncodeStr))
      } else if (this.$root.$mp && this.$root.$mp.query && this.$root.$mp.query.scene) {
        console.log('query scene=======>')
        console.log(this.$root.$mp.query.scene)
        let scene = decodeURIComponent(this.$root.$mp.query.scene)
        console.log(scene)
        let params = scene.split(',')
        let query = {}
        params.forEach(function (param) {
          let key = param.substring(0, param.indexOf(':'))
          let val = param.substring(param.indexOf(':') + 1)
          query[key] = val
        })
        console.log(query)
        return JSON.stringify(query)
      }
    },
    // 将内容添加到剪切板
    setClipboardData (str = '') {
      this.$utils.setClipboardData(str)
    }
  },
  mounted () {
    let that = this
    that.$nextTick(async () => {
      console.log('mounted')
      that.queryEncodeStr = that.getQuery()

      that.mountedNextTick()
    })
  },
  onHide () {
    // 前进一页的时候触发
    console.log('onHide')
  },
  onUnload () {
    // 后退和替换当前页的时候触发
    console.log('onUnload')
    this.resetComponent()
  }
}
