<template>
    <div :class="['page', 'product-detail', {'iphone-x': isIpx}]">
        <div class="page-main">
            <!--banner区域-->
            <div class="banners">
                <banner
                        :width="750"
                        :height="750"
                        :images="bannerImages"
                        :indicatorColor="'rgba(255, 255, 255, 0.5)'"
                        :indicatorActiveColor="'#ffffff'"
                        @clickImage="toBannerDetail"></banner>
            </div>

            <!--商品基本信息-->
            <div class="base-info border-bottom">
                <div class="title">
                    康佳LED55R2 55吋新变频生态电视
                </div>
                <div class="row-flex align-item-end bottom-gap-25">
                    <div class="present-price">
                        <span>¥</span>
                        <span class="price">1699</span>
                        <!--.00-->
                    </div>
                </div>
                <div class="progress-bar-box">
                    <progress-bar
                            :width="690"
                            :height="12"
                            :progress="35"
                            :progressColor="'#ff8e35'"></progress-bar>
                </div>
                <div class="row-flex align-item-end">
                    <div class="elastic-flex-item">
                        <span class="font-24 font-light-black">已砍399.99元</span>
                    </div>
                    <div class="fixed-flex-item">
                        <span class="font-24 font-light-black">还剩699.01元</span>
                    </div>
                </div>
            </div>

            <div class="seek-help">
                <button @click="openPopupBox">喊好友砍一刀</button>
                <div class="countdown">
                    还剩 21:38:22 结束
                </div>
            </div>

            <div class="section">
                <div class="section-title">
                    帮砍记录
                </div>
                <div class="section-content bargain-histories">
                    <div class="history" v-for="n in 10" :key="n">
                        <div class="user-header">
                            <image src="http://img5.imgtn.bdimg.com/it/u=3906842785,3298929596&fm=26&gp=0.jpg" />
                        </div>
                        <div class="elastic-flex-item font-28 font-black">
                            长颈鹿脖计长
                        </div>
                        <div class="fixed-flex-item font-28 font-red">
                            砍掉12.98元
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--弹窗-->
        <div :class="['popup-box', 'scale-in', {'active': showPopupBox}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closePopupBox"></div>
            <div class="popup-content">
                <div class="user-header">
                    <image src="http://img5.imgtn.bdimg.com/it/u=3906842785,3298929596&fm=26&gp=0.jpg" />
                </div>
                <div class="message">
                    谢谢你，帮我砍了<span class="price">25.88</span>元
                </div>
                <button class="clear-btn" @click="closePopupBox">确定</button>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import Banner from '@/components/banner.vue'
  import ProgressBar from '@/components/progress-bar.vue'

  export default {
    mixins: [Basic],
    components: {
      Banner,
      ProgressBar
    },
    data () {
      return {
        bannerImages: [
          {
            imageUrl: 'http://img3.imgtn.bdimg.com/it/u=2825323393,1568492388&fm=26&gp=0.jpg'
          },
          {
            imageUrl: 'http://img3.imgtn.bdimg.com/it/u=2825323393,1568492388&fm=26&gp=0.jpg'
          },
          {
            imageUrl: 'http://img3.imgtn.bdimg.com/it/u=2825323393,1568492388&fm=26&gp=0.jpg'
          }
        ],
        showPopupBox: false
      }
    },
    methods: {
      mountedNextTick () {
      },
      openPopupBox () {
        this.showPopupBox = true
      },
      closePopupBox () {
        this.showPopupBox = false
      },
      toProductComment () {
        this.$utils.navigateTo('/pages/product-comment/main')
      },
      toCustomerService () {
        this.$utils.showModal('跳转到客服')
        // this.$utils.navigateTo('/pages/service/main')
      },
      toCart () {
        this.$utils.showModal('跳转到购物车')
        // this.$utils.redirectTo('/pages/cart/main')
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }
    .product-detail .base-info .title {
        margin-bottom: 30px;
    }
    .product-detail .base-info .present-price {
        font-size: 26px;
    }
    .product-detail .base-info .present-price .price {
        font-size: 32px;
    }
    .seek-help {
        padding: 30px;
        box-sizing: border-box;
    }
    .seek-help button {
        width: 100%;
        height: 88px;
        line-height: 88px;
        background-color: $red;
        color: #ffffff;
        text-align: center;
        font-size: 28px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .seek-help .countdown {
        color: $light_black;
        text-align: center;
        font-size: 26px;
    }

    /*帮砍记录*/
    .product-detail .section {
        padding-bottom: 0;
    }
    .bargain-histories {
        padding: 0 0 0 30px;
        box-sizing: border-box;
    }
    .history {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;
        padding: 20px 30px 20px 0;
        box-sizing: border-box;
    }
    .history:nth-last-of-type(1) {
        border-bottom: 0;
    }
    .history .user-header {
        margin-right: 20px;
    }

    /*弹窗*/
    .product-detail .popup-content {
        width: 510px;
        height: max-content;
        padding: 45px;
        border-radius: 16px;
        box-sizing: border-box;
        background-color: $red;
    }
    .popup-content .user-header {
        margin: 0 auto 32px auto;
        box-shadow: 0 0 1px 5px #eeeeee;
    }
    .popup-content .message {
        font-size: 28px;
        color: #ffffff;
        text-align: center;
        margin-bottom: 41px;
    }
    .popup-content .message .price {
        color: #fff100;
    }
    .popup-content .clear-btn {
        width: 100%;
        height: 72px;
        line-height: 72px;
        border-radius: 4px;
        text-align: center;
        font-size: 28px;
        color: #663d14;
        background-color: #faf064;
    }
</style>
