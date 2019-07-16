<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <tab-sort @change="changeSortType"></tab-sort>
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
                                <div class="row-flex align-item-end">
                                    <div class="condition">
                                        <span>3人拼</span>
                                    </div>
                                    <div class="present-price">
                                        <span>¥</span>
                                        <span class="price">1699</span>
                                        <!--.00-->
                                    </div>
                                </div>
                                <div class="row-flex align-item-end">
                                    <div class="sale">
                                        <span>已拼3140件</span>
                                    </div>
                                </div>
                            </div>
                            <div class="elastic-flex-item">
                                <div class="text-right">
                                    <button class="buy-now" @click="toProductDetail">立即开团</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import TabSort from '@/components/tab-sort.vue'

  export default {
    mixins: [Basic],
    components: {
      TabSort
    },
    data () {
      return {
      }
    },
    methods: {
      mountedNextTick () {
      },
      changeSortType (key, type) {
        console.log('key====>', key)
        console.log('type====>', type)
      },
      toProductDetail () {
        this.$utils.navigateTo('/pages/make-group-detail/main')
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
