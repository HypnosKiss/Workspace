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
            <div class="base-info">
                <div class="row-flex align-item-center bottom-gap-10">
                    <div class="present-price">
                        <span>¥</span>
                        <span class="price">1699</span>
                        <!--.00-->
                    </div>
                    <div class="pin">
                        <span class="icon-pin">拼</span>
                        <span>4人拼</span>
                    </div>
                    <div class="share">
                        <i class="icon iconfont icon-fenxiang-"></i>
                        <span>分享</span>
                    </div>
                </div>
                <div class="row-flex align-item-end bottom-gap-25">
                    <div class="original-price">
                        <span>¥</span>
                        <span class="price">2000</span>
                        <!--.00-->
                    </div>
                </div>
                <div class="title">
                    康佳LED55R2 55吋新变频生态电视
                </div>
            </div>

            <!--正在拼单的伙伴-->
            <div class="section">
                <div class="section-title">
                    正在拼单的伙伴，可直接参与
                </div>
                <div class="section-content partners">
                    <div class="partner" v-for="n in 3">
                        <div class="user-header">
                            <image src="http://img5.imgtn.bdimg.com/it/u=3906842785,3298929596&fm=26&gp=0.jpg" />
                        </div>
                        <div class="user-nickname">
                            干脆面干脆面干脆面干脆面干脆面干脆面
                        </div>
                        <div class="group-info">
                            <div class="font-26 font-black">还差9人拼成</div>
                            <div class="font-24 font-light-black">剩余23:23:23</div>
                        </div>
                        <div class="fixed-flex-item">
                            <button class="join-group">去参团</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--我的伙伴-->
            <div class="section">
                <div class="section-title row-flex align-item-center">
                    <div class="elastic-flex-item">
                        拼团中，还差<span class="font-red">2</span>人
                    </div>
                    <div class="share fixed-flex-item">
                        <i class="icon iconfont icon-fenxiang-"></i>
                        <span>分享</span>
                    </div>
                </div>
                <div class="section-content partners">
                    <div class="partner" v-for="n in 3">
                        <div class="user-header">
                            <image src="http://img5.imgtn.bdimg.com/it/u=3906842785,3298929596&fm=26&gp=0.jpg" />
                        </div>
                        <div class="user-nickname">
                            干脆面干脆面干脆面干脆面干脆面干脆面
                        </div>
                    </div>
                </div>
            </div>

            <!--评论-->
            <div class="section">
                <div class="section-title">
                    评论（252）
                </div>
                <div class="section-content comment-list">
                    <div class="comment">
                        <div class="user-info">
                            <div class="user-header">
                                <image src="http://img5.imgtn.bdimg.com/it/u=3906842785,3298929596&fm=26&gp=0.jpg" />
                            </div>
                            <div class="user-nickname">
                                干脆面干脆面干脆面
                            </div>
                            <div class="create-date">
                                2018-09-09 23:23:23
                            </div>
                        </div>
                        <div class="row-flex align-item-center">
                            <div class="specifications">
                                <span class="item">规格一</span>
                                <span class="item">规格二</span>
                                <span class="item">规格三</span>
                                <span class="item">规格四</span>
                            </div>
                            <div class="achievement">
                                <i class="icon iconfont icon-pingjia-xuanzhong- active"></i>
                                <i class="icon iconfont icon-pingjia-xuanzhong- active"></i>
                                <i class="icon iconfont icon-pingjia-xuanzhong- active"></i>
                                <i class="icon iconfont icon-pingjia-xuanzhong- active"></i>
                                <i class="icon iconfont icon-pingjia-xuanzhong-"></i>
                            </div>
                        </div>
                        <div class="comment-content">
                            康佳生态电视，品质很好，无边框OLED
                        </div>
                    </div>
                    <div class="more-comment">
                        <button @click="toProductComment">全部评论</button>
                    </div>
                </div>
            </div>

            <!--商品详情-->
            <div class="section">
                <div class="section-title">
                    商品详情
                </div>
                <div class="section-content product-content">
                    <image src="https://www.konka.com/public/images/97/38/62/1aa2b910ea5304881e14fdd6f45c6e8b6f80171f.jpg?13460_OW1198_OH2060" mode="widthFix" />
                    <image src="https://www.konka.com/public/images/ee/87/32/3a413a2283174ef3652098b9dbc1259b9f17d4df.jpg?45062_OW1198_OH2061" mode="widthFix" />
                </div>
            </div>
        </div>

        <!--底部按钮-->
        <div class="page-bottom">
            <div class="others">
                <div class="cart" @click="toCart">
                    <i class="icon iconfont icon-gouwuche-xiangqing-"></i>
                    <div class="name">购物车</div>
                </div>
                <div class="service" @click="toCustomerService">
                    <i class="icon iconfont icon-kefu-xiangqing-"></i>
                    <div class="name">客服</div>
                </div>
            </div>
            <div class="buy-later" @click="openPopupBox('now')">
                <div><span>¥</span><span>1699</span></div>
                <div><span>单独购买</span></div>
            </div>
            <div class="buy-now" @click="openPopupBox('group')">
                <div><span>¥</span><span>1499</span></div>
                <div><span>发起拼单</span></div>
            </div>
        </div>

        <!--弹窗-->
        <div :class="['popup-box', 'down-in', {'active': showPopupBox}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closePopupBox"></div>
            <div class="popup-content">
                <div class="product-base-info">
                    <div class="product-image fixed-flex-item">
                        <div class="product-thum">
                            <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800" />
                        </div>
                    </div>
                    <div class="elastic-flex-item overflow-hidden">
                        <div class="product-price">
                            <div class="present-price">
                                <span>¥</span>
                                <span class="price">1699</span>
                                <!--.00-->
                            </div>
                            <div class="original-price">
                                <span>¥</span>
                                <span class="price">2000</span>
                                <!--.00-->
                            </div>
                        </div>
                        <div class="tips">
                            规格一规格一规格一规格一
                        </div>
                    </div>
                </div>
                <scroll-view class="product-groups" scroll-y="true">
                    <div class="group">
                        <div class="group-title">
                            <div class="group-name">规格</div>
                        </div>
                        <div class="group-content">
                            <span class="option active">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                        </div>
                    </div>
                    <div class="group">
                        <div class="group-title">
                            <div class="group-name">规格</div>
                        </div>
                        <div class="group-content">
                            <span class="option active">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                        </div>
                    </div>
                    <div class="group">
                        <div class="group-title">
                            <div class="group-name">规格</div>
                        </div>
                        <div class="group-content">
                            <span class="option active">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                        </div>
                    </div>
                    <div class="group">
                        <div class="group-title">
                            <div class="group-name">规格</div>
                        </div>
                        <div class="group-content">
                            <span class="option active">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                            <span class="option">规格一</span>
                        </div>
                    </div>
                    <div class="group">
                        <div class="group-title">
                            <div class="group-name">数量</div>
                            <div class="number-input-box">
                                <number-input :min="1" :value="1"></number-input>
                            </div>
                        </div>
                    </div>
                </scroll-view>
                <div>
                    <button class="submit">确认</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import Banner from '@/components/banner.vue'
  import NumberInput from '@/components/number-input.vue'

  export default {
    mixins: [Basic],
    components: {
      Banner,
      NumberInput
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
        showPopupBox: false,
        buyType: ''
      }
    },
    methods: {
      mountedNextTick () {
      },
      openPopupBox (buyType = 'later') {
        this.buyType = buyType
        this.showPopupBox = true
      },
      closePopupBox () {
        this.buyType = ''
        this.showPopupBox = false
      },
      toProductComment () {
        this.$utils.navigateTo('/pages/product-comment/main')
      },
      toCustomerService () {
        this.$utils.navigateTo('/pages/customer-service/main')
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
    .product-detail .base-info {
        padding-top: 20px;
    }
    .product-detail .base-info .present-price {
        flex-grow: 0;
        flex-shrink: 0;
        color: $red;
        font-size: 30px;
        padding-right: 30px;
    }
    .pin {
        @extend .elastic-flex-item;
        @extend .row-flex;
        @extend .align-item-center;
        font-size: 28px;
        color: $theme_sub_color;
    }
    .icon-pin {
        width: 40px;
        height: 40px;
        line-height: 40px;
        border-radius: 20px;
        border-top-right-radius: 0;
        font-size: 24px;
        color: #ffffff;
        background-color: $theme_sub_color;
        text-align: center;
        margin-right: 16px;
    }
    .share {
        @extend .row-flex;
        @extend .align-item-center;
        font-size: 28px;
        color: $deep_black;
    }
    .share .iconfont {
        color: $theme_sub_color;
        font-size: 50px;
        margin-right: 20px;
    }
    .product-detail .section-content.partners {
        padding-right: 0;
    }
    .partner {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        padding: 20px 30px 20px 0;

        .user-header {
            @extend .fixed-flex-item;

            margin-right: 20px;
        }
        .user-nickname {
            @extend .elastic-flex-item;
            @extend .line-clamp-1-ellipsis;
            font-size: 28px;
            color: $deep_black;
        }
        .group-info {
            @extend .fixed-flex-item;
            @extend .column-flex;
            @extend .align-item-center;

            margin-right: 15px;
        }
        .join-group {
            padding: 5px 20px;
            border-radius: 4px;
            background-color: $theme_color;
            color: #ffffff;
            font-size: 26px;
        }
    }
    .partner:nth-of-type(1) {
        padding-top: 0;
    }
    .partner:nth-last-of-type(1) {
        padding-bottom: 0;
        border-bottom: 0;
    }
    .comment-list .comment .user-info {
        padding-top: 0;
    }
    .product-detail .page-bottom .buy-later, .product-detail .page-bottom .buy-now {
        @extend .column-flex;
        @extend .justify-center;

        line-height: initial;
    }
</style>
