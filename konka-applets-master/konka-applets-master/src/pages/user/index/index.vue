<template>
    <div :class="['page', {'iphone-x': isIpx}]">
      <!-- <scroll-view scroll-y style="height: 100%;"> -->
        <div class="page-main">
            <div class="user-info">
                <div class="user-header">
                    <image :src="userInfo.avatar || defaultUserHeader" />
                </div>
                <div class="phone">
                    <div>
                        {{userInfo.nickname}}
                    </div>
                    <div v-if="userInfo.partner.isBind === 10" class="partner-code">
                        {{userInfo.partner.code}}
                    </div>
                </div>
            </div>

            <div v-if="userInfo.partner.isBind === 10" class="partner-info">
              <div class="bg-red"></div>
              <div class="partner-info-k">
                <header>
                  <span>累计收益K币 {{ userInfo.partner.totalKMoney || 0 }}</span>
                  <div @click="$utils.navigateTo('/pages/user/cash/main')">提现</div>
                </header>
                <main class="k">
                  <div @click="$utils.navigateTo('')">
                    <span>{{ userInfo.partner.availableWithdrawKMoney || 0 }}</span>
                    <p>可提现K币</p>
                  </div>
                  <div @click="$utils.navigateTo('/pages/user/accumulated-income/main', {status: 20})">
                    <span>{{ userInfo.partner.hasWithdrawKMoney || 0 }}</span>
                    <p>已提现K币</p>
                  </div>
                  <div @click="$utils.navigateTo('/pages/user/accumulated-income/main', {status: 10})">
                    <span>{{ userInfo.partner.waitSettlementKMoney || 0 }}</span>
                    <p>未结算K币</p>
                  </div>
                </main>
              </div>
              <div class="partner-info-order partner-info-k">
                <main class="k">
                  <div @click="myTeam">
                    <span>{{ userInfo.partner.myTeamCoun || 0 }}</span>
                    <p>我的团队</p>
                  </div>
                  <div  @click='distributionOrder'>
                    <span>{{ userInfo.partner.saleOrders || 0 }}</span>
                    <p>分销订单</p>
                  </div>
                  <div @click='monthSale'>
                    <span>{{ userInfo.partner.monthSales || 0 }}</span>
                    <p>本月销售额</p>
                  </div>
                  <div @click='yearSale'>
                    <span>{{ userInfo.partner.yearSales || 0 }}</span>
                    <p>本年销售额</p>
                  </div>
                </main>
              </div>
              <div class="partner-invite">
                <div @click="openPopupBox">
                  <i :class="['icon', 'iconfont', 'iconyaoqing']" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from(#ff697d), to(#fb4d64))'}"></i>
                  <span>邀请会员</span>
                </div>
                <div @click="$utils.showToast('该功能正在开发中')">
                  <i :class="['icon', 'iconfont', 'iconvip']" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from(#f9db1c), to(#febf3a))'}"></i>
                  <span>邀请合伙人</span>
                </div>
              </div>
            </div>

            <div class="my-orders">
                <div class="info-row">
                    <div class="row-name">我的订单</div>
                    <div class="row-value"></div>
                    <div class="row-icon" @click="toPathPage({path: '/pages/user/my-orders/main'})">全部订单</div>
                </div>
                <div class="order-types">
                    <div v-for="(each, n) in orderTypes" @click="toPathPage(each)" :key="n">
                        <div class="item-icon">
                            <div class="point" v-if='each.point>0' >{{each.point}}</div>
                            <i :class="['icon', 'iconfont', each.icon]"></i>
                        </div>
                        <div class="item-name">
                            {{each.name}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="my-orders">
               <div class="info-row">
                <div class="row-name">我的服务</div>
                <div class="row-value"></div>
                </div>
            </div>
            <div class="other-functions">
                <div class="function-item" v-for="(each, n) in functions" @click="toPathPage(each)" :key='n'>
                  <div class="item-icon">
                      <i :class="['icon', 'iconfont', each.icon]" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from('+each.color[0]+'), to('+each.color[1]+'))'}"></i>
                  </div>
                  <div class="item-name">
                      {{each.name}}
                  </div>
              </div>
              <div class="function-item mt60" v-for="(each, n) in functions2" :key='n' @click="toPathPage(each)">
                  <div class="item-icon">
                      <i :class="['icon', 'iconfont', each.icon]" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from('+each.color[0]+'), to('+each.color[1]+'))'}"></i>
                  </div>
                  <div class="item-name">
                      {{each.name}}
                  </div>
              </div>
            </div>
            <div v-if="userInfo.partner.isBind === 20" class="partner-login">
              <div class='btn' @click="$utils.navigateTo('/pages/user/partner-login/main')">合伙人登录</div>
            </div>
            <div v-if="userInfo.partner.isBind === 10" class="partner-login">
              <div class='btn' @click="handleClickLogout">退出登录</div>
            </div>
            <div style="height: 50px;"></div>
        </div>
      <!-- </scroll-view> -->
      <!-- 弹窗 -->
        <div :class="['popup-box', 'down-in', {'active': showPopupBox}]" @touchmove.stop="() => {}">
          <div class="popup-bg" @click="closePopupBox"></div>
          <div class="popup-content partner-popup">
            <div class="partner-base-info">
              <div class="info-tit">分享的人最美 <span class="close" @click="closePopupBox">x</span></div>
              <div class="share-box">
                <div class="box-item">
                  <button :style="{'backgroundColor':'#fff'}" open-type="share">
                    <div class="item-icon"><i :class="['icon', 'iconfont', 'iconweixin']" :style="{'color': 'green'}"></i></div>
                    <div class="item-name">分享至微信</div>
                  </button>
                </div>
                <div class="box-item" @click="showShareCode">
                  <div class="item-img"><img src="/static/image/applets.jpg" mode="aspectFit" alt="" srcset=""></div>
                  <div class="item-name">小程序码</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div :class="['popup-box', 'down-in', {'active': showShareCodeBox}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closeShareCodeBox"></div>
            <div class="popup-content">
                <div class="share-poster-image">
                    <img :src="userInfo.partner.shareCodeUrl" alt="">
                </div>
                <div>
                    <button class="submit" :disabled="shareCodeDownload" @click="savePoster">{{ shareCodeDownload ? '下载中...' : '一键保存' }}</button>
                </div>
            </div>
        </div>
        <div class="page-bottom">
            <tab-nav :navKey="'mine'"></tab-nav>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import TabNav from '@/components/tab-nav.vue'

  export default {
    mixins: [Basic],
    components: {
      TabNav
    },
    data () {
      return {
        showPopupBox: false,
        lockOpen: false,
        isPartner: false,
        showShareCodeBox: false,
        shareCodeDownload: false,
        code: '',
        orders: this.userInfo,
        functions: [
          {
            icon: 'icon-kanjia-',
            name: '我的砍价',
            color: ['#ff7a90', '#f56271'],
            path: ''
          },
          {
            icon: 'icon-pintuan-',
            name: '我的拼团',
            color: ['#ffde66', '#ffc654'],
            path: ''
          },
          {
            icon: 'icon-youhuiquan-',
            name: '我的优惠券',
            color: ['#ff7a90', '#f56271'],
            path: ''
          },
          {
            icon: 'icon-dizhiguanli-',
            name: '地址管理',
            color: ['#dbcaa0', '#d3c492'],
            path: '/pages/user/address/main'
          }
        ],
        functions2: [
          {
            icon: 'icon-fapiaoguanli-',
            name: '发票管理',
            color: ['#dbcaa0', '#d3c492'],
            path: '/pages/user/invoice/main'
          },
          {
            icon: 'icon-kefu-',
            name: '联系客服',
            color: ['#ff7a90', '#f56271'],
            path: '/pages/customer-service/main'
          },
          {
            icon: 'icon-bangzhuzhongxin-',
            name: '帮助中心',
            color: ['#7fb4f5', '#6ca0f5'],
            path: '/pages/helpers/main'
          },
          {
            icon: 'iconshezhi',
            name: '设置',
            color: ['#ffc654', '#ffde66'],
            // path: '/pages/partner/index/main',
            path: '',
            query: {}
          }
        ]
      }
    },
    computed: {
      orderTypes () {
        let arr = [
          {
            icon: 'icon-daifukuan-',
            name: '待付款',
            point: this.userInfo.orders.waitPay,
            path: '/pages/user/my-orders/main',
            query: {
              status: 10
            }
          },
          {
            icon: 'icon-daifahuo-',
            name: '待发货',
            point: this.userInfo.orders.waitSend,
            path: '/pages/user/my-orders/main',
            query: {
              status: 20
            }
          },
          {
            icon: 'icon-daishouhuo-',
            name: '待收货',
            point: this.userInfo.orders.hasSend,
            path: '/pages/user/my-orders/main',
            query: {
              status: 30
            }
          },
          {
            icon: 'icon-yiwancheng-1',
            name: '已完成',
            point: '',
            path: '/pages/user/my-orders/main',
            query: {
              status: 40
            }
          },
          {
            icon: 'icon-tuihuanhuo-',
            name: '退换货',
            point: this.userInfo.orders.refunds,
            path: '/pages/user/refund-and-exchange-orders/main',
            query: {
              status: 50
            }
          }
        ]
        return arr
      }
    },
    onShow () {
      this.loadUserInfo()
    },
    onShareAppMessage () {
      console.log('onShareAppMessage -> code: ', this.code)
      return {
        title: '康佳优品',
        path: `pages/index/main?code=${this.userInfo.partner.inviteCode}`,
        imageUrl: '/static/image/logo.png',
        success: function (shareTickets) {
          console.info(shareTickets + '成功')
          // 转发成功
        },
        fail: function (res) {
          console.log(res + '失败')
          // 转发失败
        },
        complete: function (res) {
          // 不管成功失败都会执行
        }
      }
    },
    methods: {
      openPopupBox () {
        this.showPopupBox = true
      },
      closePopupBox () {
        this.showPopupBox = false
      },
      loadUserInfo () {
        this.$utils.showLoading()
        let that = this
        let options = {
          url: '/current',
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(data => {
            this.code = data.code
            that.$utils.hideLoading()
            console.log(data)
            this.$store.commit('setUserInfo', data)
            that.isPartner = data.isPartner
            that.lockOpen = true
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`获取失败，${err}`)
          })
      },
      showShareCode () {
        this.$utils.showToast(`该功能正在开发中`)
        // this.showShareCodeBox = true
      },
      closeShareCodeBox () {
        this.showShareCodeBox = false
      },
      savePoster () {
        this.shareCodeDownload = true
        wx.downloadFile({
          url: this.userInfo.partner.shareCodeUrl,
          success: function (res) {
            this.saveFileToThumb(res.tempFilePath)
            this.shareCodeDownload = false
          }.bind(this),
          fail: function () {
            this.shareCodeDownload = false
          }.bind(this)
        })
      },
      saveFileToThumb (filePath) {
        wx.getSetting({
          success: function (res) {
            if (!res.authSetting['scope.writePhotosAlbum']) {
              wx.authorize({
                scope: 'scope.writePhotosAlbum',
                success: function () {
                  wx.saveImageToPhotosAlbum({
                    filePath: filePath,
                    success: function () {
                      wx.showToast({
                        title: '保存成功'
                      })
                      this.posterBox = false
                    }.bind(this)
                  })
                }.bind(this),
                fail: function () {
                  wx.showToast({
                    title: '保存小程序码失败，请授权写入'
                  })
                }
              })
            } else {
              wx.saveImageToPhotosAlbum({
                filePath: filePath,
                success: function () {
                  this.posterBox = false
                }.bind(this)
              })
            }
          }.bind(this)
        })
      },
      mountedNextTick () {
        console.log(this.orders, '加载个人中心')
        // this.loadUserInfo()
      },
      accountlatedIncome () {
        this.$utils.navigateTo('/pages/user/accumulated-income/main', {
          status: 10
        })
      },
      myTeam () {
        this.$utils.navigateTo('/pages/user/my-team/main')
      },
      distributionOrder () {
        this.$utils.navigateTo('/pages/user/distribution-order/main')
      },
      monthSale () {
        this.$utils.navigateTo('/pages/user/month-sale/main')
      },
      yearSale () {
        this.$utils.navigateTo('/pages/user/year-sale/main')
      },
      toAddress () {
        this.$utils.navigateTo('/pages/user/address/main')
      },
      toPathPage (info = {}) {
        if (!info.path) {
          this.$utils.showToast(`该功能正在开发中`)
          return
        }
        if (info.name === '合伙人') {
          if (this.lockOpen) {
            if (this.isPartner === 10) {
              info.path = '/pages/partner/index/main'
            } else if (this.isPartner === 20) {
              info.path = '/pages/partner/accept-recommend/main'
            } else if (this.isPartner === 30) {
              this.$utils.error(`您的合伙人申请正在审核中，请稍后！`)
              return
            }
          } else {
            return
          }
        }
        this.$utils.navigateTo(info.path, info.query || {})
      },
      // 邀请人
      hanldeClickInvite () {
      },
      // 退出合伙人登录
      async handleClickLogout () {
        const options = {
          url: '/partner/logout',
          method: 'put'
        }
        try {
          this.$utils.showLoading()
          const data = await this.$utils.requestServer(options)
          this.$utils.hideLoading()
          this.$utils.showToast('退出成功')
          console.log('退出成功', data)
          this.$store.commit('setUserInfo', data)
        } catch (error) {
          this.$utils.hideLoading()
          wx.showToast({ title: error || '退出登录失败', icon: 'none' })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
    .partner-code{
        font-size: 28px;
    }

    .partner-code:before{
        content: '合伙人编码:';
        font-size: 28px;
        display: inline-block;
        margin-right: 5px;
    }

    .share-poster-image{
        text-align: center;
        padding-top: 10px;
    }

    .share-poster-image img{
        width: 600px;
        height: 915px;
        vertical-align: middle;
    }
    .page, .page-main {
        background-color: #f7f7f7;
        padding-bottom: 20px;
    }

    .user-info {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        padding: 30px;
        box-sizing: border-box;
        background-color: #ff0000;
    }

    .partner-info {
      position: relative;
      min-height: 600px;
      padding: 0 30px;
      .bg-red {
        background-color: #ff0000;
        height: 77px;
        position: absolute;
        top: -1px;
        left: 0;
        right: 0;
      }
      .partner-info-k {
        position: relative;
        z-index: 1;
        // width: 100%;
        // height: 268px;
        background-color: #ffffff;
        border-radius: 20px;
        padding: 40px 30px;
        padding-bottom: 0;
        header {
          height: 50px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 22px;
          span {
            color: #666666;
            font-size: 28px;
          }
          div {
            width: 120px;
            height: 50px;
            line-height: 50px;
            border-radius: 25px;
            border: solid 1px #e6e6e6;
            text-align: center;
            color: #333333;
            font-size: 24px;
            padding: 0;
          }
        }
        .k {
          display: flex;
          color: #666666;
          font-size: 24px;
          div {
            display: flex;
            flex: 1;
            padding: 40px 0 40px 0;
            align-items: center;
            justify-content: center;
            flex-direction: column;
          }
          p {
            margin-top: 22px;
          }
          span {
            color: #333333;
          }
        }
      }
      .partner-info-order {
        margin-top: 20px;
        padding: 0;
      }
      .partner-invite {
        display: flex;
        margin: 20px 0;
        div {
          width: 50%;
          display: flex;
          justify-content: center;
          align-items: center;
          padding: 45px 0;
          background-color: #ffffff;
          border-radius: 20px;
          .iconfont {
            font-size: 50px;
            /*background-image: -webkit-gradient(linear, left top, right bottom, from(#ff7a90), to(#f56271));*/
            -webkit-background-clip: text; /*必需加前缀 -webkit- 才支持这个text值 */
            -webkit-text-fill-color: transparent; /*text-fill-color会覆盖color所定义的字体颜色： */
          }
          span {
            color: #666666;
            font-size: 24px;
            margin-left: 20px;
          }
        }
      }
    }

    .user-header {
        @extend .fixed-flex-item;

        width: 120px;
        height: 120px;
        border-radius: 60px;
        margin-right: 34px;
    }
    .phone {
        @extend .elastic-flex-item;

        font-size: 40px;
        color: $white;
    }

    .my-orders {
        margin-top: 20px;
        padding-left: 20px;
        box-sizing: border-box;
        background-color: #ffffff;

        .row-name {
            font-size: 30px;
            color: $deep_black;
        }
        .row-icon {
            font-size: 26px;
        }
    }

    .order-types {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .justify-between;

        padding: 32px 20px 32px 0;
        box-sizing: border-box;


        .item-icon {
          position: relative;
            @extend .row-flex;
            @extend .align-item-center;
            @extend .justify-center;

            width: 80px;
            height: 80px;
            border-radius: 40px;
            margin-bottom: 10px;
            text-align: center;
            color: $deep_black;
            // background-color: #fafafa;
            .point{
              display: flex;
              overflow: hidden;
              width: auto;
              height: 12px;
              right: -2px;
              top: 2px;
              padding: 8px 7px;
              color: red;
              -webkit-box-pack: center;
              justify-content: center;
              -webkit-box-align: center;
              align-items: center;
              font-size: 24px;
              position: absolute;
              border: 1px solid red;
              border-radius: 20px;
              background-color: rgb(255, 255, 255)
            }
            .iconfont {
                font-size: 60px;
            }
        }
        .item-name {
            font-size: 24px;
            color: #888888;
            text-align: center;
        }
    }
    .other-functions {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .flex-wrap;
        padding: 30px 0;
        // padding-left: 30px;
        // padding-right: 30px;
        // padding-bottom: 43px;
        box-sizing: border-box;
        background-color: #ffffff;

        .function-item {
            width: calc(100% / 4);
            // margin-top: 60px;
            display: inline-block;
        }

        .item-icon {
            @extend .row-flex;
            @extend .align-item-center;
            @extend .justify-center;

            width: 80px;
            height: 80px;
            margin: 0 auto 10px auto;
            text-align: center;
            color: $deep_black;

            .iconfont {
                font-size: 50px;
                /*background-image: -webkit-gradient(linear, left top, right bottom, from(#ff7a90), to(#f56271));*/
                -webkit-background-clip: text; /*必需加前缀 -webkit- 才支持这个text值 */
                -webkit-text-fill-color: transparent; /*text-fill-color会覆盖color所定义的字体颜色： */
            }
        }
        .item-name {
            font-size: 24px;
            color: #888888;
            text-align: center;
        }

    }
    .mt60{
      margin-top: 60px;
    }
    .partner-login{
      padding: 0 20px 70px 20px;
      margin-top: 50px;
      .btn{
        width: 100%;
        height: 88px;
        line-height: 88px;
        font-size: 28px;
        background-color: #ffffff;
        text-align: center;
        color: #f56271;
        border-radius: 44px;
        border: solid 1px #f56271;
      }
    }
    .partner-base-info{
      width: 750px;
      box-sizing: border-box;
      background-color: #fff;
      .info-tit{
        text-align: center;
        padding: 40px 0;
        font-size: 30px;
        position: relative;
        border-bottom:1px solid #efefef;
        .close{
          position: absolute;
          right: 0;
          top: 10px;
          color: #000;
          height: 100px;
          font-size: 50px;
          line-height: 100px;
          width: 100px;
          display: inline-block;
        }
      }
      .share-box{
        display: flex;
        width: 100%;
        text-align: center;
        padding: 200px 0;
        .box-item{
          width: 50%;
          padding:0 80px;
          flex: 1;
          image{
            width: 100px;
            height: 100px;
          }
          .item-icon{
            .iconfont{
              font-size: 100px;
            }
          }
          .item-name{
            margin-top: 10px;
            font-size: 28px;
          }
        }
      }
    }

</style>
