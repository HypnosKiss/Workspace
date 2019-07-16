<template>
    <div :class="[{'iphone-x': isIpx}]">
        <!--<div class="page-main">-->
            <div class="banner">
                <img src="../../../../static/image/my-recommend-banner.jpg" mode="widthFix" />
            </div>

            <div class="rule">
                <div class="title">
                    推荐规则
                </div>
                <div class="content">
                    推荐算法是计算机专业中的一种算法，通过一些数学算法，推测出用户可能喜欢的东西，目前应用推荐算法比较好的地方主要是网络，其中淘宝做的比较好。所谓推荐算法就是利用用户的一些行为，通过一些数学算法，推测出用户可能喜欢的东西。推荐算法主要分为6种。
                </div>
            </div>

            <div class="input-area">
                <div class="input-line">
                    <input placeholder="输入公司名称" placeholder-style="color: #cccccc;" v-model="companyName"/>
                </div>
                <div class="input-line">
                    <input placeholder="输入姓名" placeholder-style="color: #cccccc;" v-model="userName"/>
                </div>
                <div class="input-line">
                    <input placeholder="输入手机号" placeholder-style="color: #cccccc;"  v-model="telNumber"/>
                </div>
                <div class="input-line">
                    <input placeholder="省、市、县区域（请选择）" disabled placeholder-style="color: #cccccc;" v-model="addressText" @click="chooseAddress" v-if="addressScope" />
                    <button :class="['row-value', {'no-address': !addressText}]" open-type="openSetting" @opensetting="afterOpenSetting" v-else>请选择地址</button>
                </div>
                <div class="input-line">
                    <textarea auto-height="true" placeholder="请输入详细地址" placeholder-style="color: #cccccc;" v-model="detailInfo"></textarea>
                </div>
            </div>

            <div class="button-group">
                <!--<button  @click="sendMsg">立即推荐合伙人</button>-->
                <button open-type="share" :disabled="nicai" :class="[{'noBtn': nicai}]">分享邀请合伙人加入</button>
            </div>
        <!--</div>-->
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    components: {
    },
    onLoad (options) {
    },
    data () {
      return {
        userName: '',
        telNumber: '',
        addressText: '',
        detailInfo: '',
        provinceText: '',
        cityText: '',
        countyText: '',
        companyName: '',
        code: ''
      }
    },
    computed: {
      nicai () {
        return !(this.companyName && this.userName && this.telNumber && this.provinceText && this.cityText && this.countyText && this.detailInfo)
      }
    },
    methods: {
      mountedNextTick () {
        this.code = this.query.code
        this.$store.dispatch('getWxSetting')
      },
      afterOpenSetting (res) {
        let detail = res.mp.detail

        if (detail.errMsg !== 'openSetting:ok') {
          this.$utils.error('设置授权出错')
          return
        }
        if (detail.authSetting[`scope.address`]) {
          this.$store.dispatch('getWxSetting')
          this.chooseAddress()
          return
        }
        this.$utils.error('请授权小程序使用微信地址')
      },
      chooseAddress () {
        let that = this
        wx.chooseAddress({
          success (res) {
            console.log(res)
            that.userName = that.userName ? that.userName : res.userName
            that.telNumber = that.telNumber ? that.telNumber : res.telNumber
            that.addressText = res.provinceName + res.cityName + res.countyName
            that.provinceText = res.provinceName
            that.cityText = res.cityName
            that.countyText = res.countyName
            that.detailInfo = res.detailInfo
          }
        })
      },
      sendMsg () {
        // let that = this
        this.$utils.navigateTo('/pages/partner/apply/main', {
          info: {
            name: this.userName,
            phone: this.telNumber,
            provinceText: this.provinceText,
            cityText: this.cityText,
            countyText: this.countyText,
            address: this.detailInfo,
            companyName: this.companyName
          }
        })
      },
      share () {

      }
    },
    onShareAppMessage: function (res) {
      // if (!(this.companyName && this.userName && this.telNumber && this.provinceText && this.cityText && this.countyText && this.detailInfo)) {
      //   this.$utils.error('请将邀请信息填写完整')
      // }
      console.log(res)
      if (res.from === 'button') {
        // 来自页面内转发按钮
        console.log(res.target)
        return {
          title: '成为我的合伙人吧！',
          path: `/pages/partner/accept-recommend/main?code=${this.code}&&cn=${this.companyName}&&un=${this.userName}&&tn=${this.telNumber}&&pt=${this.provinceText}&&ct=${this.cityText}&&cts=${this.countyText}&&ad=${this.detailInfo}`,
          imageUrl: '',
          success: function (res) {
            this.$utils.navigateBack()
          }
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
    .iphone-x {
        padding-bottom: 68px;
    }
    .banner {
        line-height: 0;

        image {
            width: 100%;
        }
    }
    .rule {
        padding: 15px 30px;
        box-sizing: border-box;

        .title {
            font-size: 30px;
            color: $deep_black;
        }
        .content {
            margin-top: 15px;
            text-indent: 52px;
            font-size: 26px;
            line-height: 40px;
            color: $black;
        }
    }
    .input-area {
        padding: 30px;
        box-sizing: border-box;
    }
    .input-line {
        @extend .row-flex;
        @extend .align-item-center;

        margin-bottom: 20px;
        width: 100%;
        border: 1px solid #e5e5e5;

        input {
            @extend .elastic-flex-item;

            height: 80px;
            line-height: 80px;
            font-size: 28px;
            padding: 0 30px;
            box-sizing: border-box;
            background-color: #ffffff;
        }

        button {
            @extend .elastic-flex-item;

            height: 80px;
            line-height: 80px;
            font-size: 28px;
            padding: 0 30px;
            box-sizing: border-box;
            color: #cccccc;
            background-color: #ffffff;
            text-align: left;
        }

        textarea {
            @extend .elastic-flex-item;

            min-height: 150px;
            font-size: 28px;
            line-height: 40px;
            padding: 30px;
            box-sizing: border-box;
            background-color: #ffffff;
        }
    }

    .button-group {
        padding: 30px;
        box-sizing: border-box;

        button {
            width: 100%;
            height: 88px;
            line-height: 88px;
            margin: 0;
            margin-bottom: 20px;
            font-size: 28px;
            border-radius: 4px;
            border: 1px solid $theme_sub_color;
            box-sizing: border-box;
            background-color: $theme_sub_color;
        }
        .noBtn {
            color: white;
            background-color: #999999;
            border: 0;
        }
    }
</style>
