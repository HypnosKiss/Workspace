
const uuidv4 = require('uuid/v4')
// const CryptoJs = require('crypto-js')
const Md5 = require('crypto-js/md5')
const Utf8 = require('crypto-js/enc-utf8')
const Base64 = require('crypto-js/enc-base64')
const HmacSHA256 = require('crypto-js/hmac-sha256')

let routerReseting = false
let toastTimeout = null

let requesting = {}

class Utils {
  justTest (txt) {
    console.log(txt)
  }

  hadWxScope (scopeName = '') {
    return new Promise((resolve, reject) => {
      if (!scopeName) {
        resolve(false)
        return
      }
      wx.getSetting({
        success (res) {
          console.log(res.authSetting)
          // res.authSetting = {
          //   "scope.userInfo": true,
          //   "scope.userLocation": true
          // }
          if (!res.authSetting) {
            resolve(false)
            return
          }
          resolve(res.authSetting[`scope.${scopeName}`])
        }
      })
    })
  }

  /**
   * 显示对话框（有确定取消按钮）
   * @param options
   */
  showModal (options = {}) {
    if (typeof options === 'string') {
      wx.showModal({
        content: options,
        showCancel: false
      })
    } else {
      wx.showModal(options)
    }
  }

  confirm (message = '') {
    return new Promise((resolve, reject) => {
      wx.showModal({
        title: '提示',
        content: message,
        success (res) {
          if (res.confirm) {
            // console.log('用户点击确定')
            // resolve('clear')
            resolve(true)
          } else if (res.cancel) {
            // console.log('用户点击取消')
            // reject(new Error('cancel'))
            resolve(false)
          }
          resolve(false)
        }
      })
    })
  }

  success (options = {}, callback = () => {}) {
    if (typeof options === 'string') {
      wx.showModal({
        title: '成功',
        content: options,
        showCancel: false,
        success (res) {
          if (res.confirm) {
            // console.log('用户点击确定')
            callback()
          } else if (res.cancel) {
            // console.log('用户点击取消')
          }
        }
      })
    } else {
      wx.showModal(options)
    }
  }

  error (options = {}, callback = () => {}) {
    if (typeof options === 'string') {
      wx.showModal({
        title: '提示',
        content: options,
        showCancel: false,
        success (res) {
          if (res.confirm) {
            // console.log('用户点击确定')
            callback()
          } else if (res.cancel) {
            // console.log('用户点击取消')
          }
        }
      })
    } else {
      wx.showModal(options)
    }
  }

  showLoading (options = {title: 'loading'}) {
    if (typeof options === 'string') {
      wx.showLoading({
        // title最多7个汉字
        title: `${options}`,
        mask: true
      })
    } else {
      wx.showLoading(options)
    }
  }

  hideLoading () {
    wx.hideLoading()
  }

  /**
   * 显示消息提示框(默认3秒后消失)
   * @param options
   * @param callback
   * @param time
   */
  showToast (options = {}, callback = () => {}, time = 3000) {
    let that = this
    that.hideToast()
    clearTimeout(toastTimeout)
    if (typeof options === 'string') {
      wx.showToast({
        // title最多7个汉字
        title: `${options}`,
        icon: 'none',
        mask: true
      })
    } else {
      wx.showToast(options)
    }
    toastTimeout = setTimeout(function () {
      that.hideToast()
      callback()
    }, time)
  }

  hideToast () {
    wx.hideToast()
  }

  getValue (e) {
    return (e && e.mp && e.mp.detail && e.mp.detail.value) ? e.mp.detail.value : ''
  }

  /**
   * 将数据存储在本地缓存中指定的 key 中。会覆盖掉原来该 key 对应的内容。数据存储生命周期跟小程序本身一致，即除用户主动删除或超过一定时间被自动清理，否则数据都一直可用。单个 key 允许存储的最大数据长度为 1MB，所有数据存储上限为 10MB。
   * @param name
   * @param val
   * @param time
   */
  setStorageSync (name = '', val = null, time = 60 * 60 * 2) {
    if (name === '') {
      return
    }
    wx.setStorageSync(name, {
      expired: (new Date()).getTime() + time * 1000,
      value: val
    })
  }

  getStorageSync (name = '') {
    if (name === '') {
      return ''
    }
    let storage = wx.getStorageSync(name)
    if (storage.expired && (storage.expired > (new Date()).getTime())) {
      return storage.value
    } else {
      this.removeStorageSync(name)
      return ''
    }
  }

  updateStorageSync (name = '') {
    if (name === '') {
      return
    }
    this.setStorageSync(name, this.getStorageSync(name))
  }

  removeStorageSync (name = '') {
    if (name === '') {
      return
    }
    wx.removeStorageSync(name)
  }

  setSessionId (sessionId) {
    this.setStorageSync(`sessionId`, sessionId)
  }

  updateSessionId () {
    this.updateStorageSync(`sessionId`)
  }

  getSessionId () {
    return this.getStorageSync(`sessionId`)
  }

  removeSessionId () {
    this.removeStorageSync(`sessionId`)
  }

  setBasicDefault (basicDefault) {
    console.log('setBasicDefault')
    console.log(basicDefault)
    this.setStorageSync(`basicDefault`, basicDefault)
  }

  updateBasicDefault () {
    this.updateStorageSync(`basicDefault`)
  }

  getBasicDefault () {
    return this.getStorageSync(`basicDefault`)
  }

  removeBasicDefault () {
    this.removeStorageSync(`basicDefault`)
  }

  setAccessToken (token) {
    this.setStorageSync(`accessToken`, token)
  }

  updateAccessToken () {
    this.updateStorageSync(`accessToken`)
  }

  getAccessToken () {
    return this.getStorageSync(`accessToken`)
  }

  removeAccessToken () {
    this.removeStorageSync(`accessToken`)
  }

  setOpenid (openid) {
    this.setStorageSync(`openid`, openid)
  }

  updateOpenid () {
    this.updateStorageSync(`openid`)
  }

  getOpenid () {
    return this.getStorageSync(`openid`)
  }

  removeOpenid () {
    this.removeStorageSync(`openid`)
  }

  setUserInfo (userInfo, time) {
    // time 存的时间
    this.setStorageSync(`userInfo`, userInfo, time)
  }

  updateUserInfo () {
    this.updateStorageSync(`userInfo`)
  }

  getUserInfo () {
    return this.getStorageSync(`userInfo`)
  }

  removeUserInfo () {
    this.removeStorageSync(`userInfo`)
  }

  setClipboardData (str = '') {
    if (!str) {
      return
    }
    let that = this
    wx.setClipboardData({
      data: str,
      success (res) {
        that.showToast('复制成功')
      }
    })
  }

  /**
   * 阻止路由跳转
   */
  routerPrevent () {
    routerReseting = true
  }

  /**
   * 恢复路由跳转
   */
  routerRecovery () {
    routerReseting = false
  }

  /**
   * 将对象转换为路由query参数字符串
   * @param query
   * @returns {string}
   */
  pathQueryToStr (query = {}) {
    console.log('pathQueryToStr', query)
    let result = ''
    if (Object.keys(query).length > 0) {
      // for (let n in query) {
      //   result += `${n}=${query[n]}&`
      // }
      // result = result.substr(0, result.length - 1)

      // console.log(JSON.stringify(query))
      result = '?queryEncodeStr=' + encodeURIComponent(this.base64Encode(JSON.stringify(query)))
      // console.log('result===>' + result)
    }
    return result
  }

  /**
   * 关闭当前页面，跳转到应用内的某个页面，但是不允许跳转到 tabbar 页面。
   * @param url
   * @param query
   * @returns {Promise<any>}
   */
  redirectTo (url = '', query = {}) {
    let that = this
    return new Promise((resolve, reject) => {
      if (!url) {
        console.error('没有存入url')
        reject(new Error('没有存入url'))
        return
      }
      if (typeof query !== 'object') {
        console.error('query必须为Object')
        reject(new Error('query必须为Object'))
        return
      }
      if (routerReseting) {
        console.error('禁止短时间内重复触发跳转')
        reject(new Error('禁止短时间内重复触发跳转'))
        return
      }

      that.routerPrevent()
      wx.redirectTo({
        url: `${url}${that.pathQueryToStr(query)}`,
        success () {
          resolve()
        },
        fail (err) {
          reject(new Error(err))
        },
        complete () {
          that.routerRecovery()
        }
      })
    })
  }

  /**
   * 保留当前页面，跳转到应用内的某个页面，但是不能跳到 tabbar 页面。使用 wx.navigateBack 可以返回到原页面。
   * @param url
   * @param query
   * @returns {Promise<any>}
   */
  navigateTo (url = '', query = {}) {
    let that = this
    return new Promise((resolve, reject) => {
      if (!url) {
        reject(new Error('没有存入url'))
        return
      }
      if (typeof query !== 'object') {
        reject(new Error('query必须为Object'))
        return
      }
      if (routerReseting) {
        reject(new Error('禁止短时间内重复触发跳转'))
        return
      }

      that.routerPrevent()
      wx.navigateTo({
        url: `${url}${that.pathQueryToStr(query)}`,
        success () {
          resolve()
        },
        fail (err) {
          reject(new Error(err))
        },
        complete () {
          that.routerRecovery()
        }
      })
    })
  }

  /**
   * 关闭所有页面，打开到应用内的某个页面
   * @param url
   * @param query
   * @returns {Promise<any>}
   */
  reLaunchTo (url = '', query = {}) {
    let that = this
    return new Promise((resolve, reject) => {
      if (!url) {
        reject(new Error('没有存入url'))
        return
      }
      if (typeof query !== 'object') {
        reject(new Error('query必须为Object'))
        return
      }
      if (routerReseting) {
        reject(new Error('禁止短时间内重复触发跳转'))
        return
      }
      that.routerPrevent()
      wx.reLaunch({
        url: `${url}${that.pathQueryToStr(query)}`,
        success () {
          resolve()
        },
        fail (err) {
          reject(new Error(err))
        },
        complete () {
          that.routerRecovery()
        }
      })
    })
  }

  navigateBack (num = 1) {
    let that = this
    return new Promise((resolve, reject) => {
      if (typeof num !== 'number') {
        reject(new Error('num必须为number'))
        return
      }
      if (!num) {
        reject(new Error('没有存入num'))
        return
      }
      if (routerReseting) {
        reject(new Error('禁止短时间内重复触发跳转'))
        return
      }
      this.routerPrevent()
      wx.navigateBack({
        delta: num,
        success () {
          resolve()
        },
        fail (err) {
          reject(new Error(err))
        },
        complete () {
          that.routerRecovery()
        }
      })
    })
  }

  setLastPath (path = '', query = {}) {
    if (path === '') {
      return
    }
    // this.removeStorageSync(`lastPath`);
    this.setStorageSync(`lastPath`, {
      path: path,
      query: query
    })
  }

  getLastPath () {
    let lastPath = this.getStorageSync(`lastPath`)
    console.log('lastPath============>' + lastPath)
    this.removeStorageSync(`lastPath`)
    return lastPath
  }

  /**
   * 退出登录
   */
  logout () {
    this.removeSessionId()
    this.removeBasicDefault()
    this.removeAccessToken()
    this.removeOpenid()
    this.removeUserInfo()
  }

  /**
   * 将请求增加到当前正在请求的对象集中
   * @param identification
   * @param request
   */
  addRequestingItem (identification, request) {
    if (!identification) {
      return
    }
    requesting[identification] = request
  }

  /**
   * 将请求终止，并从当前正在请求的对象集中删除
   * @param identification
   * @param request
   */
  deleteRequestingItem (identification) {
    console.log(`${identification}=====>deleteRequestingItem`)
    if (!identification) {
      return
    }
    // console.log('identification===>', identification)
    console.log('requesting===>', requesting)
    if (requesting[identification]) {
      if (requesting[identification].abort) {
        console.log(`${identification}=====>`, requesting[identification])
        console.log(`${identification}=====>`, requesting[identification].abort)
        console.log(`${identification}=====>执行abort`)
        requesting[identification].abort()
      }
      delete requesting[identification]
    }
  }

  requestUrl () {
    return process.env.NODE_ENV === 'production' ? `https://kkyp.konka.com` : `https://kkypuat.konka.com`
  }

  requestServer (options, identification = null) {
    let that = this
    // 增加签名头
    options = Object.assign(options, this.signature(options))

    return new Promise((resolve, reject) => {
      let request = wx.request({
        url: this.requestUrl() + options.url,
        data: options.data,
        header: Object.assign({
          'content-type': 'application/json'
        }, options.headers || {}),
        method: options.method,
        success (res) {
          // let body = res.data
          let body = that.keyIndexToLowercase(res.data)
          if (body.code === 0) {
            resolve(body.data)
          } else {
            let logoutMessages = [
              'header_x_access_token_invalid',
              'WxApplets Request Must Need Session Id',
              'Unauthorized'
            ]
            if (body.code === 401 || logoutMessages.includes(body.message)) {
              // 登录超时
              console.log('登录超时')
              let vuex = require('../store/index.js').default
              console.log(vuex)
              vuex.dispatch('logout')
              // that.logout()
              return
            }
            // throw body.message
            console.log('错误')
            reject(body.message)
          }
        },
        fail (err) {
          console.error(err)
          reject(err.errMsg || '请求出错')
        },
        complete () {
          if (identification) {
            that.deleteRequestingItem(identification)
          }
          that.updateAccessToken()
          that.updateOpenid()
          that.updateSessionId()
        }
      })
      if (identification) {
        that.addRequestingItem(identification, request)
      }
    })
  }

  signature (data) {
    const commonHeaders = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
    const method = data.method ? data.method.toUpperCase() : 'GET'
    const body = Object.keys(data.data ? data.data : {}).length > 0 ? JSON.stringify(data.data) : ''

    const bodyMd5 = body.length > 0 ? Md5(body).toString(Base64) : ''

    const url = '/api' + data.url
    const signHeadersList = Object.assign({
      // 'X-Ca-Request-Mode': 'debug',
      'X-Ca-Applets-Id': 'konka-applets',
      'X-Ca-Applets-Session-Id': this.getSessionId(),
      'X-Ca-Timestamp': this.utcTimestamp(),
      'X-Ca-Nonce': uuidv4()
    }, data.headers)
    if (bodyMd5) {
      signHeadersList['Content-MD5'] = bodyMd5
    }
    let accessToken = this.getAccessToken()
    if (accessToken) {
      signHeadersList['X-Access-Token'] = accessToken
    }

    let headerStr = ''
    let signHeaders = ''
    Object.keys(signHeadersList).sort().map(function (header) {
      signHeaders += header + ','
      headerStr += header + ':' + signHeadersList[header] + '\n'
    })
    signHeaders = signHeaders.substr(0, signHeaders.length - 1)
    const signStr = method + '\n' +
      commonHeaders['Content-Type'] + '\n' +
      bodyMd5 + '\n' +
      commonHeaders['Accept'] + '\n' +
      signHeadersList['X-Ca-Timestamp'] + '\n' +
      headerStr +
      url
    let headers = Object.assign(commonHeaders, signHeadersList)
    let signSecret = process.env.NODE_ENV === 'production' ? 'bP3rDBwmIsNKs5$%n3Dog^gPi&RC5Tu9Z7krW*9M%tr4rrfgf5Xau6vp*8m#Pt%0AB5JN3q&pAT4pvkrClvSJqAdn!g5iMAK7Zq&'
      : 'NevJKwg1DzKmU4jxvjQ7tAqigjc2IRvPMgHS9pwRGeEgoP11uz2sPsHr3BkOzo4rLwvXsjphKNWGfvrLSpdWcHgoJoOrr7UgD9w'
    // let signSecret = 'bP3rDBwmIsNKs5$%n3Dog^gPi&RC5Tu9Z7krW*9M%tr4rrfgf5Xau6vp*8m#Pt%0AB5JN3q&pAT4pvkrClvSJqAdn!g5iMAK7Zq&'
    headers['X-Ca-Signature'] = HmacSHA256(signStr, signSecret).toString(Base64)
    headers['X-Ca-Signature-Headers'] = signHeaders

    let result = {
      method: method.toLowerCase(),
      headers: headers,
      url: url
    }
    if (body) {
      result.data = body
    }

    return result
  }

  // 将对象中的key的首字母转换为小写
  keyIndexToLowercase (obj = {}) {
    let result = {}
    for (let n in obj) {
      let key = n.substring(0, 1).toLowerCase() + n.substring(1, n.length)
      result[key] = obj[n]
    }
    return result
  }

  /**
   * 自定义函数
   */
  formatNumber (n) {
    const str = n.toString()
    return str[1] ? str : `0${str}`
  }

  formatTime (date) {
    const year = date.getFullYear()
    const month = date.getMonth() + 1
    const day = date.getDate()

    const hour = date.getHours()
    const minute = date.getMinutes()
    const second = date.getSeconds()

    const t1 = [year, month, day].map(this.formatNumber).join('/')
    const t2 = [hour, minute, second].map(this.formatNumber).join(':')

    return `${t1} ${t2}`
  }

  formatDate (date) {
    const year = date.getFullYear()
    const month = date.getMonth() + 1
    const day = date.getDate()

    const t1 = [year, month, day].map(this.formatNumber).join('-')

    return `${t1}`
  }

  utcTimestamp () {
    let utcDate = new Date()
    let utc = {
      yy: utcDate.getUTCFullYear(),
      mm: utcDate.getUTCMonth() + 1,
      dd: utcDate.getUTCDate(),

      h: utcDate.getUTCHours(),
      m: utcDate.getUTCMinutes(),
      s: utcDate.getUTCSeconds()
    }

    for (let n in utc) {
      if (utc[n].toString().length === 1) {
        utc[n] = `0${utc[n]}`
      }
    }

    // console.log(`${utc.yy}-${utc.mm}-${utc.dd}T${utc.h}:${utc.m}:${utc.s}Z`)
    return `${utc.yy}-${utc.mm}-${utc.dd}T${utc.h}:${utc.m}:${utc.s}Z`
  }

  /**
   * 克隆对象
   * @param obj
   * @returns {any}
   */
  cloneObject (obj = {}) {
    return JSON.parse(JSON.stringify(obj))
  }

  /**
   * 判断对象是否为空对象
   * @param e
   * @returns {boolean}
   */
  isEmptyObject (e) {
    return Object.keys(e).length === 0
  }

  /**
   * 将字符串转换为base64字符串
   * @param str
   * @returns {*}
   */
  base64Encode (str) {
    return Base64.stringify(Utf8.parse(str))
  }
  base64Decode (base64) {
    return Utf8.stringify(Base64.parse(base64))
  }

  objToCheckBoxOptions (obj = {}) {
    let result = []
    for (let n in obj) {
      result.push({
        key: n,
        name: obj[n]
      })
    }
    return result
  }

  checkBoxValueToName (obj, values) {
    let result = ''
    for (let n = 0, length = values.length; n < length; n++) {
      result += `${obj[values[n]]},`
    }
    result = result.substring(0, result.length - 1)
    return result
  }

  roundTo (change, digits) {
    console.log(change.toFixed(2))
    return Math.round((change * Math.pow(10, digits)) / Math.pow(10, digits), digits)
  }
}

export default new Utils()
