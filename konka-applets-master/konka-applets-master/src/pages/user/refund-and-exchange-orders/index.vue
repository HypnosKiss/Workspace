<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <scroll-view :class="['page-main','comment-list','position-relative', 'occupy-all']" :scroll-y="true"
                         @scrolltolower="getNextPage">
                <div v-for="(each, n) in showList" :class="['order']" :key="n">
                    <div class="order-title">
                        <span class="elastic-flex-item font-green font-red font-light-black">订单号：{{each.orderCode}}</span>
                        <span :class="['fixed-flex-item', {'font-light-black': each.status===10}, {'font-green': each.status===20}, {'font-red': each.status===30}]">{{each.statusText}}</span>
                    </div>
                    <!--商品列表-->
                    <div class="product-list">
                        <div class="product">
                            <div class="product-image fixed-flex-item">
                                <image :src="each.productIamgeUrl" mode="aspectFit"/>
                            </div>
                            <div class="elastic-flex-item column-flex">
                                <div class="product-title fixed-flex-item">
                                    {{each.productTitle}}
                                </div>
                                <div class="product-sales-info elastic-flex-item align-item-end">
                                    <div class="product-price elastic-flex-item">
                                        <div class="row-flex align-item-end bottom-gap-10">
                                            <div class="sale elastic-flex-item">
                                                {{each.productSpecificationText}}
                                            </div>
                                            <div class="number fixed-flex-item">
                                                ×{{each.num}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="order-total">
                        <!--<span>共2件商品，合计：</span>-->
                        <span class="price">¥</span>
                        <span class="price">{{each.price}}</span>
                        <!--.00-->
                    </div>
                </div>
                <div class="no-list-data" v-if="noSearchResult">
                    没有找到相关数据
                </div>
                <div class="no-more-list-data" v-if="!noSearchResult && noMoreListData">
                    已无更多数据
                </div>
            </scroll-view>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import List from '@/mixins/list.js'

  export default {
    mixins: [Basic, List],
    components: {},
    data () {
      return {
        orders: [
          {
            status: 10,
            statusText: '退换货中'
          },
          {
            status: 20,
            statusText: '退换货成功'
          },
          {
            status: 30,
            statusText: '退换货失败'
          },
          {
            status: 20,
            statusText: '退换货成功'
          },
          {
            status: 20,
            statusText: '退换货成功'
          }
        ],
        getListPath: '/refund-order'
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.defaultList()
        that.startLoading()
      },
      toInvoice () {
        this.$utils.navigateTo('/pages/user/invoice/main')
      },
      toAddress () {
        this.$utils.navigateTo('/pages/user/address/main')
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }

    .page-main {
        width: 100%;
        height: 100%;
        padding-bottom: 0;
        box-sizing: border-box;
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

        padding-top: 0;

        .product {
            padding-left: 0;
            padding-bottom: 20px;
            border-bottom: 0;
        }
        .product-title {
            @extend .line-clamp-2-ellipsis;
            height: 70px;
        }
        .number {
            font-size: 26px;
            color: $light_black;
        }
        .present-price .price {
            font-size: 26px;
        }
    }

    .order-total {

        padding-right: 30px;
        padding-bottom: 20px;
        font-size: 26px;
        text-align: right;
        color: $black;
        box-sizing: border-box;

        .price {
            color: $red;
        }
    }
</style>
