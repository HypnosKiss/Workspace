<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <tab :tabs="tabs" :defaultTab="activeTab" @change="changeTab"></tab>

            <div class="orders">
                <div v-for="(each, n) in orders" :class="['order', statusClass[each.status]]">
                    <div class="order-title">
                        <span class="elastic-flex-item font-green font-red font-light-black">订单号：KJD20181218151244101</span>
                        <span class="fixed-flex-item font-light-black font-24">2018-12-12 22:33:44</span>
                    </div>
                    <!--产品列表-->
                    <div class="product-list">
                        <div class="product">
                            <div class="product-image fixed-flex-item">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                       mode="aspectFit"/>
                            </div>
                            <div class="elastic-flex-item column-flex justify-center">
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
                                            <span class="font-24 font-light-black status">{{each.statusText}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="order-total">
                        <span>合计：</span>
                        <span class="price">¥</span>
                        <span class="price">5080.00</span>
                        <span class="left-padding">共2件商品</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import Tab from '@/components/tab.vue'

  export default {
    mixins: [Basic],
    components: {
      Tab
    },
    data () {
      return {
        activeTab: 10,
        tabs: [
          {
            key: 10,
            name: '待成团'
          },
          {
            key: 20,
            name: '拼团成功'
          },
          {
            key: 30,
            name: '拼团失败'
          }
        ],
        statusClass: {
          10: 'wait',
          20: 'success',
          30: 'fail'
        },
        orders: [
          {
            status: 10,
            statusText: '待成团'
          },
          {
            status: 20,
            statusText: '拼团成功'
          },
          {
            status: 30,
            statusText: '拼团失败'
          }
        ]
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

    .order {
        padding-left: 30px;
        margin-top: 20px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .order-title {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        padding-right: 30px;
        height: 80px;
        font-size: 24px;
        color: $black;
    }
    .product-list {
        @extend .border-bottom;

        padding-top: 0;

        .product {
            padding-left: 0;
            border-bottom: 0;
        }
        .product-title {
            @extend .line-clamp-1-ellipsis;
        }
        .product-sub-title {
            @extend .line-clamp-1-ellipsis;
        }
        .product-sales-info {
            @extend .elastic-flex-item;

            height: auto;
        }
        .number {
            font-size: 26px;
            color: $light_black;
        }
        .present-price .price {
            font-size: 26px;
        }

    }
    .wait .status {
        color: $light_black;
    }
    .success .status {
        color: $green;
    }
    .fail .status {
        color: $red;
    }
    .order-total {

        padding-right: 30px;
        height: 96px;
        line-height: 96px;
        font-size: 26px;
        text-align: left;
        color: $black;
        box-sizing: border-box;

        .price {
            color: $red;
        }
    }
</style>
