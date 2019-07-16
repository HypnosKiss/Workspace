<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <tab :tabs="tabs" :defaultTab="activeTab" @change="changeTab"></tab>
            <div v-if="activeTab === 'ongoing'">
                <!--顶部提示-->
                <div class="tips">
                    <div class="message">
                        抢购中，先下单先得
                    </div>
                    <div class="countdown">
                        距结束：08:22:11
                    </div>
                </div>
                <!--产品列表-->
                <div class="product-list">
                    <div class="product" v-for="n in 10" :key="n">
                        <div class="product-image fixed-flex-item">
                            <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                   mode="aspectFit"/>
                        </div>
                        <div class="elastic-flex-item">
                            <div class="product-title">
                                康佳R2新变频生态电视
                            </div>
                            <div class="product-sub-title">
                                悬浮屏/全生态人工智能
                            </div>
                            <div class="product-sales-info">
                                <div class="product-price fixed-flex-item">
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
                                <div class="elastic-flex-item">
                                    <div class="text-right">
                                        <button class="buy-now" @click="toProductDetail">立即购买</button>
                                    </div>
                                    <div class="row-flex align-item-center">
                                        <span class="had-sale elastic-flex-item text-right">已售20%</span>
                                        <div class="progress-bar-box fixed-flex-item">
                                            <progress-bar
                                                    :width="100"
                                                    :height="12"
                                                    :progress="70"
                                                    :progressColor="'#f56271'"></progress-bar>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="activeTab === 'start-soon'">
                <!--顶部提示-->
                <div class="tips">
                    <div class="message">
                        即将开抢，先下单先得
                    </div>
                    <div class="countdown">
                        距开始：08:22:11
                    </div>
                </div>
                <!--产品列表-->
                <div class="product-list">
                    <div class="product" v-for="n in 10" :key="n">
                        <div class="product-image fixed-flex-item">
                            <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                   mode="aspectFit"/>
                        </div>
                        <div class="elastic-flex-item">
                            <div class="product-title">
                                康佳R2新变频生态电视
                            </div>
                            <div class="product-sub-title">
                                悬浮屏/全生态人工智能
                            </div>
                            <div class="product-sales-info">
                                <div class="product-price fixed-flex-item">
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
                                <div class="elastic-flex-item">
                                    <div class="text-right">
                                        <button class="buy-now">立即预约</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="activeTab === 'notice'">
                更多预告
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import Tab from '@/components/tab.vue'
  import ProgressBar from '@/components/progress-bar.vue'

  export default {
    mixins: [Basic],
    components: {
      Tab,
      ProgressBar
    },
    data () {
      return {
        activeTab: 'ongoing',
        tabs: [
          {
            key: 'ongoing',
            name: '正在疯抢'
          },
          {
            key: 'start-soon',
            name: '即将开抢'
          },
          {
            key: 'notice',
            name: '更多预告'
          }
        ]
      }
    },
    methods: {
      mountedNextTick () {
      },
      toSearchPage () {
        this.$utils.navigateTo('/pages/search/main')
      },
      changeTab (key) {
        console.log('key====>', key)
        this.activeTab = key
      },
      toProductDetail () {
        this.$utils.navigateTo('/pages/instant-kill-detail/main')
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }

    .tips {
        @extend .row-flex;
        @extend .align-item-center;
        padding: 16px 30px;
        box-sizing: border-box;
    }

    .tips .message {
        @extend .elastic-flex-item;
        font-size: 26px;
        color: $black;
    }

    .tips .countdown {
        @extend .fixed-flex-item;
        height: 50px;
        line-height: 50px;
        border-radius: 25px;
        padding: 0 26px;
        box-sizing: border-box;
        border: 1px solid $red;
        font-size: 24px;
        text-align: center;
        color: $red;
    }
</style>
