<template>
  <div :class="['partner-register', {'iphone-x': isIpx}]">
    <div class="page-main">
      <header>
        <img :src="userInfo.avatar || defaultUserHeader" alt="" srcset="">
        <div>{{ userInfo.nickname }}</div>
      </header>
      <main>
        <form onsubmit="return false">
          <div class="input border-b activationCode">
            <label for="activationCode">激活码</label>
            <input @blur="handleBlurCode" type="text" id="activationCode" v-model="activationCode" placeholder="请输入激活码">
            <span v-if="codeToInfo">{{ codeToInfo }}</span>
          </div>
          <div class="input border-b">
            <label for="phone">手机号</label>
            <input type="number" id="phone" @input="handlePhoneInput" v-model="phone" placeholder="请输入手机号">
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
            <!--<button :disabled="disabled" :class="{isActive: !disabled}" @click="getCode">{{ buttonMsg }}</button>-->
          </div>
          <div class="input code border-b">
            <label>密码</label>
            <input v-if="isShowPassword" :password="!isShowPassword" type="text" v-model="password" placeholder="仅支持6位字母、数字">
            <input v-else type="text" :password="!isShowPassword" v-model="password" placeholder="仅支持6位字母、数字">
            <i @click="isShowPassword = !isShowPassword" :class="['icon', 'iconfont', {'iconeye': isShowPassword}, {'iconeye1': !isShowPassword}]" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from(#ed1c24), to(#ed1c24))'}"></i>
          </div>
          <!--<div class="input border-b">-->
            <!--<label>地区</label>-->
            <!--<picker-->
              <!--mode="region"-->
              <!--:value="region"-->
              <!--@change="bindRegionChange"-->
            <!--&gt;-->
              <!--<view v-if="region.length" class="picker">-->
                <!--{{region[0]}}，{{region[1]}}，{{region[2]}}-->
              <!--</view>-->
              <!--<view v-else class="picker">-->
                <!--<span style="color: #888;">请选择地区</span>-->
              <!--</view>-->
            <!--</picker>-->
          <!--</div>-->

          <button :disabled="submitDisabled" :class="['btn-login', {isNotActive: submitDisabled}]" @click="handleSubmit">0元成为合伙人</button>
        </form>
      </main>
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
      activationCode: '',
      phone: '',
      code: '',
      password: '',
      isShowPassword: true,
      disabled: true,
      // 定时器
      timer: null,
      // 倒计时秒数
      countdown: 60,
      buttonMsg: '验证码',
      // 验证码id
      codeId: '',
      // 地区选择
      region: [],
      // 激活码对应信息
      codeToInfo: '',
      codeTime: false
    }
  },
  computed: {
    submitDisabled () {
      return !(this.code && this.phone && this.password && this.activationCode)
    },
    isCanSendCode () {
      return this.codeTime === false && this.phone !== '' && this.phone.length === 11
    }
  },
  methods: {
    handlePhoneInput () {
      if (this.phone.length === 11 && !this.timer) {
        this.disabled = false
      } else {
        this.disabled = true
      }
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
    async handleSubmit () {
      const password = /^(\w){6,6}$/
      if (!password.test(this.password)) {
        this.$utils.showToast('密码格式不正确，请重新输入')
        return
      }

      const options = {
        url: `/partner/active-code-register`,
        method: 'post',
        data: {
          mobile: this.phone,
          activeCode: this.activationCode,
          codeId: this.codeId,
          code: this.code,
          password: this.password,
          province: this.region[0],
          city: this.region[1],
          county: this.region[2]
        }
      }
      this.$utils.showLoading()
      try {
        const data = await this.$utils.requestServer(options)
        console.log(data)
        this.$utils.hideLoading()
        if (data) {
          wx.showToast({
            title: '激活成功',
            success: () => {
              // this.userInfo = data
              this.$store.commit('setUserInfo', data)
              setTimeout(() => {
                wx.navigateBack({delta: 2})
              }, 1000)
            }
          })
        }
      } catch (error) {
        this.$utils.hideLoading()
        wx.showToast({ title: error || '注册失败', icon: 'none' })
      }
    },
    // 地区更改
    bindRegionChange (e) {
      console.log('picker发送选择改变，携带值为', e.mp.detail.value)
      this.region = e.mp.detail.value
    },
    // 输入完激活码,校验
    async handleBlurCode (e) {
      // console.log(e) g1kZ6H 天虹商场股份有限公司
      if (!e.mp.detail.value) return
      const options = {
        url: `/partner/active-code/${e.mp.detail.value}`,
        method: 'get'
      }
      this.$utils.showLoading()
      try {
        const data = await this.$utils.requestServer(options)
        console.log(data)
        this.codeToInfo = data.clientName
        this.$utils.hideLoading()
      } catch (error) {
        console.log(error)
        this.codeToInfo = error
        this.$utils.hideLoading()
      }
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
  .partner-register {
    background-color: #f7f7f7;
    padding: 103px 74px 0 74px;
    min-height: 100vh;
    color: #333333;
    font-size: 30px;
    header {
      text-align: center;
      margin-bottom: 30px;
      img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
      }
      div {
        margin-top: 20px;
        color: #333333;
        font-size: 24px;
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
        div.activationCode {
          position: relative;
          span {
            position: absolute;
            bottom: 0;
            color: #f56271;
            left: 140px;
            font-size: 24px;
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
          margin-top: 80px;
        }
        button.isNotActive {
          opacity: .5;
        }
      }
    }
  }
</style>

