<template>
    <div :class="['popup-box', 'scale-in', {'active': getPhoneNumberStatus}]" @touchmove.stop="() => {}">
        <div class="popup-bg" @click="closePopupBox"></div>
        <div class="popup-content">
            <div class="message">
                使用更多功能需要获取您的用户信息
            </div>
            <button class="oauth-button" open-type="getUserInfo" @getuserinfo="getUserInfo">授权</button>
        </div>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex'

  export default {
    name: 'get-phone-number',
    computed: {
      ...mapGetters([
        'getPhoneNumberStatus',
        'userInfo',
        'getPhoneNumberToPage'
      ])
    },
    methods: {
      getUserInfo (e) {
        console.log('getUserInfo')
        // let that = this

        console.log(e)
        console.log(e.mp.detail)
        console.log(e.mp.detail.errMsg)
        console.log(e.mp.detail.iv)
        console.log(e.mp.detail.encryptedData)

        if (e.mp.detail.errMsg !== 'getUserInfo:ok') {
          return
        }
        this.userInfo.avatar = e.mp.detail.userInfo.avatarUrl
        this.userInfo.nickname = e.mp.detail.userInfo.nickName
        console.log(this.userInfo) // 获取store的数据
        // 存到微信缓存本地 存30天
        this.$utils.setUserInfo(this.userInfo, 60 * 60 * 24 * 30)
        this.$utils.showLoading(`获取信息中`)

        let options = {
          url: '/current',
          method: 'put',
          data: {
            // openId: that.$utils.getOpenid(),
            encryptData: e.mp.detail.encryptedData,
            iv: e.mp.detail.iv
          }
        }
        this.$utils.requestServer(options)
          .then(data => {
            console.log('test=>>>>' + this.getPhoneNumberToPage)
            this.$utils.showToast(`成功获取信息`)
            // this.$utils.navigateTo('/pages/user/index/main')
            // this.closePopupBox()
            wx.navigateTo({
              url: this.getPhoneNumberToPage || '/pages/user/index/main',
              success: () => {
              },
              complete: () => {
                this.$utils.hideLoading()
                this.closePopupBox()
              }
            })
            // that.$store.commit('setAccessToken', data.accessToken)
            // that.$utils.showToast(`成功绑定手机号`)
          })
          .catch(err => {
            this.$utils.hideLoading()
            this.$utils.error(`获取信息失败，${err}`)
          })
      },
      closePopupBox () {
        this.$store.commit('setPhoneNumberStatus', false)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .popup-box {
        z-index: 99;
    }
    .popup-content {
        @extend .column-flex;
        @extend .align-item-stretch;

        width: 630px;
        height: max-content;
        padding: 45px;
        border-radius: 4px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .message {
        text-align: center;
        font-size: 30px;
        color: $black;
        padding: 0 30px 45px 30px;
        box-sizing: border-box;
    }
    .oauth-button {
        width: 100%;
        height: 90px;
        line-height: 90px;
        text-align: center;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 30px;
        color: #ffffff;
        background-color: $theme_sub_color;

    }
</style>
