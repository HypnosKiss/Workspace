<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="order">
                <div class="order-title">
                    <span class="elastic-flex-item font-green font-red font-light-black">订单号：KJD20181218151244101</span>
                    <span :class="['fixed-flex-item', {'font-light-black': info.status===10}, {'font-green': info.status===20}, {'font-red': info.status===30}]">{{info.statusText}}</span>
                </div>
                <!--商品列表-->
                <div class="product-list">
                    <div class="product">
                        <div class="product-image fixed-flex-item">
                            <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                   mode="aspectFit"/>
                        </div>
                        <div class="elastic-flex-item column-flex">
                            <div class="product-title fixed-flex-item">
                                康佳R2新变频生态康佳R2新变频生态康佳R2新变频生态康佳R2新变频生态
                            </div>
                            <div class="product-sales-info elastic-flex-item align-item-end">
                                <div class="product-price elastic-flex-item">
                                    <div class="row-flex align-item-end bottom-gap-10">
                                        <div class="sale elastic-flex-item">
                                            规格一 规格二
                                        </div>
                                        <div class="number fixed-flex-item">
                                            ×2
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-total">
                    <span>共2件商品，合计：</span>
                    <span class="price">¥</span>
                    <span class="price">5080.00</span>
                    <!--.00-->
                </div>
            </div>

            <div class="info-list order-info">
                <div class="other-info">
                    <div class="other-info-row">
                        <div class="key-item">服务单号：</div>
                        <div class="value-item">4502435748603275233475</div>
                    </div>
                    <div class="other-info-row">
                        <div class="key-item">申请时间：</div>
                        <div class="value-item">2018-12-12 15:32:22</div>
                    </div>
                    <div class="other-info-row">
                        <div class="key-item">服务类型：</div>
                        <div class="value-item">换货</div>
                    </div>
                    <div class="other-info-row">
                        <div class="key-item">退货方式：</div>
                        <div class="value-item">上门取件</div>
                    </div>
                    <div class="other-info-row">
                        <div class="key-item">收货地址：</div>
                        <div class="value-item">东莞市东城街道主山社区东城中路南163号新基地360互联网产业园A栋501</div>
                    </div>
                    <div class="other-info-row">
                        <div class="key-item">联系人：</div>
                        <div class="value-item">张明远</div>
                    </div>
                    <div class="other-info-row">
                        <div class="key-item">联系电话：</div>
                        <div class="value-item">15099999999</div>
                    </div>
                </div>
            </div>

            <div class="leaving-messages">
                <div class="message">
                    <div class="title">
                        问题描述
                    </div>
                    <div class="content">
                        收到的货有损坏，不想要了。
                    </div>
                </div>
                <div class="message">
                    <div class="title">
                        卖家留言
                    </div>
                    <div class="content">
                        尊敬的客户，很抱歉出现这种情况，如情况属实，我们 会为您处理。
                    </div>
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
        info: this.defaultInfo()
      }
    },
    methods: {
      mountedNextTick () {
        this.getDetail()
      },
      defaultInfo () {
        return {
          status: 10,
          statusText: '退换货中'
        }
      },
      getDetail () {
        let that = this
        if (!that.query.code) {
          return
        }
        let options = {
          url: `/refund-order/${that.query.code}`,
          method: 'get'
        }
        that.$utils.showLoading('获取详情中')
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.info = data
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`获取详情失败，${err}`)
          })
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


    .info-list {
        margin-top: 20px;
        padding-left: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .other-info {
        padding: 20px 0;
    }
    .other-info-row {
        @extend .row-flex;
        @extend .align-item-center;

        height: 60px;
        font-size: 26px;
        padding-right: 30px;
        color: $light_black;
    }
    .order-info {
        .other-info-row {
            @extend .align-item-start;

            height: max-content;
            padding: 10px 20px 10px 0;
        }
        .key-item {
            @extend .fixed-flex-item;
            width: 200px;
        }
        .value-item {
            @extend .elastic-flex-item;
            word-break: break-all;
        }
    }

    .leaving-messages {
        margin-top: 20px;
        padding: 20px 30px;
        box-sizing: border-box;
        background-color: #ffffff;

        .message {
            margin-bottom: 30px;
        }
        .title {
            font-size: 28px;
            color: $black;
            line-height: 40px;
            margin-bottom: 10px;
        }
        .content {
            font-size: 28px;
            color: $deep_black;
            line-height: 40px;
        }
    }
</style>
