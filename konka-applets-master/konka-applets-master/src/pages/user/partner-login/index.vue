<template>
  <div :class="['partner-login', {'iphone-x': isIpx}]">
    <div class="page-main">
      <header>
        <img src="/static/image/logo.png" alt="" srcset="">
      </header>
      <main>
        <form onsubmit="return false">
          <div  v-if="!isResetPassword" class="login-box">
            <div v-if="isPhoneLogin" class="wrap">
              <div class="input border-b">
                <label for="phone">手机号</label>
                <input type="number" id="phone" v-model="phone" placeholder="请输入手机号" @input="handlePhoneInput">
              </div>
              <div class="input code border-b">
                <label for="code">验证码</label>
                <input type="number" id="code" v-model="code" placeholder="请输入验证码">
                <send-code-btn
                        @send-code="getCode"
                        @time-reset="smsTimeReset"
                        v-bind:is-can-send-code="isCanSendCode"
                        v-bind:sms-id="codeId"
                ></send-code-btn>
              </div>
              <div @click="isPhoneLogin = !isPhoneLogin" class="type">账号登录</div>
            </div>
            <div v-else class="wrap">
              <div class="input border-b">
                <label for="phone">手机号</label>
                <input type="number" id="phone" v-model="phone" placeholder="请输入手机号" @input="handlePhoneInput">
              </div>
              <div class="input code border-b">
                <label for="password">密码</label>
                <input v-if="isShowPassword" :password="!isShowPassword"  type="text" id="password" v-model="password" placeholder="仅支持6位字母、数字">
                <input v-else type="text" :password="!isShowPassword" id="password" v-model="password" placeholder="仅支持6位字母、数字">
                <i @click="isShowPassword = !isShowPassword" :class="['icon', 'iconfont', {'iconeye': isShowPassword}, {'iconeye1': !isShowPassword}]" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from(#ed1c24), to(#ed1c24))'}"></i>
              </div>
              <div class="type type2">
                <div @click="switchResetPassword">忘记密码</div>
                <div @click="isPhoneLogin = !isPhoneLogin">手机号登录</div>
              </div>
            </div>
            <button :disabled="submitDisabled" :class="['btn-login', {isNotActive: submitDisabled}]" @click="handleSubmit">登录</button>
            <button class="btn-login wx-login" open-type="getPhoneNumber" @getphonenumber="getUserInfo">微信手机登录</button>
          </div>
          <div v-if="isResetPassword" class="reset-password-box">
            <div class="wrap">
              <div class="input border-b">
                <label for="phone">手机号</label>
                <input type="number" id="phone" v-model="phone" placeholder="请输入手机号" @input="handlePhoneInput">
              </div>
              <div class="input code border-b">
                <label for="code">验证码</label>
                <input type="number" id="code" v-model="code" placeholder="请输入验证码">
                <send-code-btn
                        @send-code="getCode"
                        @time-reset="smsTimeReset"
                        v-bind:is-can-send-code="isCanSendCode"
                        v-bind:sms-id="codeId"
                ></send-code-btn>
              </div>
              <div class="input code border-b">
                <label for="password">新密码</label>
                <input v-if="isShowPassword" :password="!isShowPassword"  type="text" id="password" v-model="password" placeholder="仅支持6位字母、数字">
                <input v-else type="text" :password="!isShowPassword" id="password" v-model="password" placeholder="仅支持6位字母、数字">
                <i @click="isShowPassword = !isShowPassword" :class="['icon', 'iconfont', {'iconeye': isShowPassword}, {'iconeye1': !isShowPassword}]" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from(#ed1c24), to(#ed1c24))'}"></i>
              </div>
              <div class="type type2">
                <div @click="switchResetPassword">返回登录</div>
              </div>
              <button :disabled="isCanResetPassword" :class="['btn-login', {isNotActive: isCanResetPassword}]" @click="resetPassword">重置密码</button>
            </div>
          </div>
        </form>
      </main>
      <footer @click="$utils.navigateTo('/pages/user/partner-register/main')">立即注册</footer>
    </div>
  </div>
</template>

<script>
import Basic from '@/mixins/basic.js'
import SendCodeBtn from '../../../components/send-code-btn'

export default {
  components: {SendCodeBtn},
  mixins: [Basic],
  data () {
    return {
      isResetPassword: false,
      // 手机验证码登陆
      isPhoneLogin: true,
      phone: '',
      code: '',
      password: '',
      disabled: true,
      // 定时器
      timer: null,
      // 倒计时秒数
      countdown: 60,
      buttonMsg: '验证码',
      // 验证码id
      codeId: '',
      // 登录
      submitDisabled: true,
      isShowPassword: true,
      codeTime: false
    }
  },
  computed: {
    isCanSendCode () {
      return this.codeTime === false && this.phone !== '' && this.phone.length === 11
    },
    isCanResetPassword () {
      return !(this.phone !== '' && this.codeId !== '' && this.password !== '' && this.code !== '' && this.phone.length === 11)
    }
  },
  methods: {
    resetPassword () {
      const options = {
        url: `/partner/reset-password`,
        method: 'post',
        data: {
          mobile: this.phone,
          codeId: this.codeId,
          code: this.code,
          password: this.password
        }
      }
      this.$utils.showLoading()
      this.$utils.requestServer(options).then(() => {
        this.$utils.hideLoading()
        wx.showToast({
          title: '重置成功',
          success: () => {
            this.switchResetPassword()
          }
        })
      }).catch(() => {
        this.$utils.hideLoading()
      })
    },
    switchResetPassword () {
      this.isResetPassword = !this.isResetPassword
      this.codeId = ''
      this.code = ''
    },
    smsTimeReset () {
      this.codeTime = false
    },
    // 获取验证码
    getCode () {
      const options = {
        url: `/send-sms-code`,
        method: 'post',
        data: {
          mobile: this.phone
        }
      }
      this.$utils.showLoading()
      this.codeTime = true
      this.$utils.requestServer(options).then((response) => {
        this.$utils.hideLoading()
        this.codeId = response.codeId
      }).catch(() => {
        this.$utils.hideLoading()
      })
    },
    handlePhoneInput () {
      if (this.phone.length === 11 && !this.timer) {
        this.disabled = false
      } else {
        this.disabled = true
      }
    },
    // login
    async handleSubmit () {
      // 手机验证码登录
      if (this.isPhoneLogin) {
        const options = {
          url: `/partner/mobile-login`,
          method: 'post',
          data: {
            mobile: this.phone,
            code: this.code,
            codeId: this.codeId
          }
        }
        this.$utils.showLoading()
        try {
          const data = await this.$utils.requestServer(options)
          this.$utils.hideLoading()
          console.log('手机号登陆 ', data)
          if (data) {
            wx.showToast({
              title: '登陆成功',
              success: () => {
                wx.navigateBack({delta: 1})
              }
            })
          }
        } catch (error) {
          console.log('手机验证码错误：', error)
          this.$utils.hideLoading()
          wx.showToast({ title: error || '登录失败，请注册合伙人', icon: 'none' })
        }
      } else {
        // 账号密码登录
        const password = /^(\w){6,6}$/
        if (!password.test(this.password)) {
          this.$utils.showToast('密码格式不正确，请重新输入')
          return
        }
        const options = {
          url: `/partner/account-login`,
          method: 'post',
          data: {
            username: this.phone,
            password: this.password
          }
        }
        this.$utils.showLoading()
        try {
          const data = await this.$utils.requestServer(options)
          this.$utils.hideLoading()
          console.log('账号密码登陆 ', data)
          if (data) {
            wx.showToast({
              title: '登陆成功',
              success: () => {
                wx.navigateBack({delta: 1})
              }
            })
          }
        } catch (error) {
          this.$utils.hideLoading()
          wx.showToast({ title: error || '登录失败，请注册合伙人', icon: 'none' })
        }
      }
    },
    // 判断登录是否可以点击
    isLogin () {
      if (this.isPhoneLogin) {
        if (this.code && this.phone) {
          this.submitDisabled = false
        } else {
          this.submitDisabled = true
        }
      } else {
        if (this.password && this.phone) {
          this.submitDisabled = false
        } else {
          this.submitDisabled = true
        }
      }
    },
    // 微信手机登录
    async getUserInfo (e) {
      console.log('微信手机登录---')
      console.log(e)
      console.log(e.mp.detail)
      if (e.mp.detail.errMsg !== 'getPhoneNumber:ok') {
        return
      }
      const options = {
        url: '/partner/wx-mobile-login',
        method: 'post',
        data: {
          // openId: that.$utils.getOpenid(),
          encryptData: e.mp.detail.encryptedData,
          // vi: e.mp.detail.iv,
          iv: e.mp.detail.iv
        }
      }
      this.$utils.showLoading()
      try {
        const data = await this.$utils.requestServer(options)
        this.$utils.hideLoading()
        console.log(data)
        if (data) {
          wx.showToast({
            title: '登陆成功',
            success: () => {
              wx.navigateBack({delta: 1})
            }
          })
        }
      } catch (error) {
        wx.showToast({ title: error || '登录失败，请注册合伙人', icon: 'none' })
      }
    }
  },
  watch: {
    code (val) {
      this.isLogin()
    },
    phone (val) {
      this.isLogin()
    },
    password () {
      this.isLogin()
    }
  },
  onHide () {
    // this.code = this.phone = this.password = ''
  }
}
</script>

<style lang="scss" scoped>
  .page-main {
    position: static;
  }
  .partner-login {
    background-color: #f7f7f7;
    padding: 94px 74px 54px 74px;
    min-height: 100vh;
    header {
      margin-bottom: 60px;
      display: flex;
      justify-content: center;
      img {
        width: 341px;
        height: 139px;
      }
    }
    main {
      form {
        div.input {
          height: 107px;
          // border-bottom: 1px solid #888;
          display: flex;
          align-items: center;
          label {
            width: 140px;
            text-align: left;
            color: #333333;
            font-size: 30px;
          }
          input {
            color: #333333;
            font-size: 30px;
            height: 107px;
            flex: 1;
          }
          button {
            width: 120px;
            height: 60px;
            background-color: #e5e5e5;
            border-radius: 6px;
            color: #ffffff;
            font-size: 26px;
            border: 0;
            padding: 0;
            line-height: 60px;
          }
          button.isActive {
            background-color: #f56271;
          }
          i.iconfont {
            font-size: 50px;
            /*background-image: -webkit-gradient(linear, left top, right bottom, from(#ff7a90), to(#f56271));*/
            -webkit-background-clip: text; /*必需加前缀 -webkit- 才支持这个text值 */
            -webkit-text-fill-color: transparent; /*text-fill-color会覆盖color所定义的字体颜色： */
          }
        }
        div.type {
          margin: 20px 0 50px 0;
          padding: 10px 0 10px 0;
          color: #f56271;
          font-size: 28px;
          display: flex;
          justify-content: flex-end;
        }
        div.type2 {
          justify-content: space-between;
          div:first-child {
            color: #999999;
          }
        }
        button.btn-login {
          width: 600px;
          height: 88px;
          background-color: #ed1c24;
          border-radius: 44px;
          font-size: 36px;
          color: #ffffff;
          text-align: center;
          line-height: 88px;
          border: 0;
          padding: 0;
        }
        button.isNotActive {
          opacity: .5;
        }
        button.wx-login {
          margin-top: 20px;
        }
      }
    }
    footer {
      color: #333333;
      font-size: 28px;
      position: absolute;
      bottom: 50px;
      left: 50%;
      transform: translateX(-50%);
    }
  }
</style>



