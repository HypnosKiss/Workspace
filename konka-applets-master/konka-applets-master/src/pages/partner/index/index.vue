<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <!--用户信息-->
            <div class="user-info">
                <div class="user-header">
                    <image :src="avatar" />
                </div>
                <div class="elastic-flex-item column-flex justify-center">
                    <div class="phone">
                        {{ info.name }}
                    </div>
                    <div class="my-id" v-if="info.parentCode">
                        我的推荐ID：{{ info.parentCode }}
                    </div>
                </div>
            </div>

            <!--基础信息-->
            <div class="total-info">
                <div class="total-line">
                    <div class="elastic-flex-item column-flex align-item-center justify-center">
                        <div class="font-red">
                            <span class="font-28">¥</span>
                            <span class="font-40">{{ info.availableMoney }}</span>
                        </div>
                        <div class="font-light-black">
                            <span class="font-24">可提现佣金</span>
                        </div>
                    </div>
                    <div class="fixed-flex-item split-line-transparent"></div>
                    <div class="elastic-flex-item column-flex align-item-center justify-center text-right">
                        <button class="take-cash" @click="openPopupBox">提现</button>
                    </div>
                </div>
                <div class="total-line">
                    <div class="elastic-flex-item column-flex align-item-center justify-center">
                        <div class="font-black">
                            <span class="font-24">¥</span>
                            <span class="font-32">{{ info.settledMoney }}</span>
                        </div>
                        <div class="font-light-black">
                            <span class="font-24">已提现佣金</span>
                        </div>
                    </div>
                    <div class="fixed-flex-item split-line"></div>
                    <div class="elastic-flex-item column-flex align-item-center justify-center">
                        <div class="font-black">
                            <span class="font-24">¥</span>
                            <span class="font-32">{{ info.frozenMoney }}</span>
                        </div>
                        <div class="font-light-black">
                            <span class="font-24">未结算佣金</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-list">
                <div class="info-row" @click="goToTeam">
                    <div class="row-name">我的团队</div>
                    <div class="row-value"></div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="info-row" @click="goToOrder">
                    <div class="row-name">分销订单</div>
                    <div class="row-value"></div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="info-row" @click="goToMember">
                    <div class="row-name">我要推荐</div>
                    <div class="row-value"></div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="info-row" @click="goToHistory">
                    <div class="row-name">提现记录</div>
                    <div class="row-value"></div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
            </div>

            <!--弹窗-->
            <div :class="['popup-box', 'scale-in', {'active': showPopupBox}]" @touchmove.stop="() => {}">
                <div class="popup-bg" @click="closePopupBox"></div>
                <div class="popup-content">
                    <i class="icon iconfont icon-shanchu close" @click="closePopupBox"></i>
                    <div class="message">
                        您还不是合伙人！
                    </div>
                    <button class="clear-btn" @click="goToBecomePartner">立即申请</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    components: {
    },
    data () {
      return {
        showPopupBox: false,
        avatar: '',
        isPartner: false,
        inviteCode: '',
        info: {
          code: '',
          userCode: '',
          availableMoney: '',
          totalMoney: '',
          frozenMoney: '',
          settledMoney: ''
        }
      }
    },
    computed: {
    },
    methods: {
      mountedNextTick () {
        this.$utils.showLoading()
        let that = this
        let options = {
          url: '/current',
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(data => {
            that.avatar = data.avatar
            // console.log('akm===>', data)
            this.inviteCode = data.code
            if (data.isPartner !== 10) {
              this.goToBecomePartner()
            } else {
              that.isPartner = true
              let optionses = {
                url: '/partner',
                method: 'get'
              }
              that.$utils.requestServer(optionses)
                .then(datas => {
                  that.$utils.hideLoading()
                  // console.log('oo=>', datas)
                  that.inviteCode = datas.code
                  that.info = datas
                  // this.$utils.navigateTo('/pages/partner/index/main')
                })
                .catch(err => {
                  that.$utils.hideLoading()
                  that.$utils.error(`获取失败，${err}`)
                })
            }
            // this.$utils.navigateTo('/pages/partner/index/main')
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      },
      openPopupBox () {
        if (this.isPartner) {
          // this.showPopupBox = true
          this.$utils.navigateTo('/pages/partner/take-cash/main', {
            limitMoney: this.info.availableMoney
          })
        } else {
          this.showPopupBox = true
        }
      },
      closePopupBox () {
        this.showPopupBox = false
      },
      goToBecomePartner () {
        this.$utils.navigateTo('/pages/partner/accept-recommend/main')
        setTimeout(() => {
          this.showPopupBox = false
        }, 800)
      },
      toAddress () {
        if (!this.isPartner) this.$utils.navigateTo('/pages/partner/accept-recommend/main')
        this.$utils.navigateTo('/pages/user/address/main')
      },
      goToHistory () {
        if (!this.isPartner) this.$utils.navigateTo('/pages/partner/accept-recommend/main')
        this.$utils.navigateTo('/pages/partner/take-cash-histories/main')
      },
      goToMember () {
        if (!this.isPartner) this.$utils.navigateTo('/pages/partner/accept-recommend/main')
        this.$utils.navigateTo('/pages/partner/my-recommend/main', {
          code: this.inviteCode
        })
      },
      goToTeam () {
        if (!this.isPartner) this.$utils.navigateTo('/pages/partner/accept-recommend/main')
        this.$utils.navigateTo('/pages/partner/my-team/main', {
          code: this.inviteCode
        })
      },
      goToOrder () {
        if (!this.isPartner) this.$utils.navigateTo('/pages/partner/accept-recommend/main')
        this.$utils.navigateTo('/pages/partner/my-team-orders/main')
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }

    .user-info {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        padding: 0 40px 40px 40px;
        box-sizing: border-box;
        background-color: #ffffff;
    }

    .user-header {
        @extend .fixed-flex-item;

        width: 120px;
        height: 120px;
        border-radius: 60px;
        margin-right: 34px;
    }
    .phone {
        margin-bottom: 5px;
        font-size: 40px;
        color: $deep_black;
    }
    .my-id {
        font-size: 28px;
        color: $black;
    }

    .total-info {
        margin-top: 20px;
        padding: 0 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .total-line {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        height: 160px;
    }
    .total-line:nth-last-of-type(1) {
        border-bottom: 0;
    }
    .split-line {
        height: 80px;
        border: 0;
        border-left: 1px solid #e5e5e5;
    }
    .split-line-transparent {
        height: 80px;
        border: 0;
        border-left: 1px solid transparent;
    }
    .take-cash {
        margin-right: 30px;
        width: 160px;
        height: 60px;
        line-height: 60px;
        border-radius: 30px;
        text-align: center;
        color: #ffffff;
        font-size: 28px;
        background-color: $theme_color;
    }
    .info-list {
        @extend .border-bottom;

        margin-top: 20px;
        padding-left: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .info-row:nth-last-of-type(1) {
        border-bottom: 0;
    }


    .popup-content {
        @extend .column-flex;
        @extend .align-item-stretch;

        position: relative;
        width: 630px;
        height: max-content;
        padding: 45px;
        border-radius: 4px;
        box-sizing: border-box;
        background-color: #ffffff;
        min-height: 360px;
    }
    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        color: $light_black;
    }
    .message {
        @extend .elastic-flex-item;
        padding: 60px 0;
        text-align: center;
        font-size: 34px;
        color: $black;
    }
    .clear-btn {
        @extend .fixed-flex-item;

        width: 100%;
        height: 80px;
        line-height: 80px;
        text-align: center;
        font-size: 28px;
        color: #ffffff;
        border-radius: 4px;
        background-color: $red;
    }
</style>
