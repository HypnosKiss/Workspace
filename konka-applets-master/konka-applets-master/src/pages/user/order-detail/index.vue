<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="order-status">
                <div class="elastic-flex-item" v-if="orderDetail.status == 5">待付款</div>
                <div class="elastic-flex-item" v-if="orderDetail.status == 30">已付款，待发货</div>
                <div class="elastic-flex-item" v-if="orderDetail.status == 50">已发货，待收货</div>
                <div class="elastic-flex-item" v-if="orderDetail.status == 60">交易完成</div>
                <div class="fixed-flex-item text-right" v-if="orderDetail.status == 5">
                    <div class="font-24">请在23时59分内完成付款</div>
                    <div class="font-24">否则系统将会取消订单</div>
                </div>
            </div>
            <div class="left-padding white-bg">
                <div class="consignee border-bottom" v-if="orderDetail.status == 50 || orderDetail.status == 60">
                    <div class="fixed-flex-item">
                        <i class="icon iconfont icon-kuaidi-"></i>
                    </div>
                    <div class="elastic-flex-item overflow-hidden">
                        <div class="row-flex align-item-center bottom-gap-20">
                            <span class="name">顺丰快递</span>
                        </div>
                        <div class="row-flex align-item-center address">
                            <span class="fixed-flex-item">单号：</span>
                            <span class="elastic-flex-item word-break">{{orderDetail.trackingNumber || '--'}}</span>
                            <span class="fixed-flex-item font-red" @click="setClipboardData(orderDetail.trackingNumber)"
                                  v-if="orderDetail.trackingNumber">复制</span>
                        </div>
                    </div>
                </div>
                <div class="consignee">
                    <div class="fixed-flex-item">
                        <i class="icon iconfont icon-dizhiguanli-"></i>
                    </div>
                    <div class="elastic-flex-item overflow-hidden">
                        <div class="row-flex align-item-center bottom-gap-20">
                            <span class="name">{{orderDetail.clientName}}</span>
                            <span class="phone">{{orderDetail.clientPhone}}</span>
                        </div>
                        <div class="row-flex align-item-center address">
                            {{orderDetail.fullAddress}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="address-line">
                <img src="../../../../static/image/address_line.jpg" mode="widthFix"/>
            </div>

            <div class="order">
                <div class="order-number">
                    <span>订单号：{{orderDetail.code}}</span>
                </div>
                <!--商品列表-->
                <div class="product-list" v-for="(items,index) in orderDetail.products" :key="index">
                    <div class="product">
                        <div class="product-image fixed-flex-item">
                            <image :src="items.imageUrl" mode="aspectFit"/>
                        </div>
                        <div class="elastic-flex-item column-flex">
                            <div class="product-title fixed-flex-item">
                                {{items.title}}
                            </div>
                            <div class="product-sales-info elastic-flex-item align-item-end">
                                <div class="product-price elastic-flex-item">
                                    <div class="row-flex align-item-end bottom-gap-10">
                                        <div class="sale elastic-flex-item">
                                            {{items.specificationsText}}
                                        </div>
                                        <div class="number fixed-flex-item">
                                            ×{{items.num}}
                                        </div>
                                    </div>
                                    <div class="row-flex align-item-end">
                                        <div class="present-price">
                                            <span>¥</span>
                                            <span class="price">{{items.price}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="elastic-flex-item">
                                    <div class="text-right">
                                        <button @click="toRefundAndExchange(items.productCode)" class="buy-now" v-if="orderDetail.status === 30">申请退款</button>
                                        <button @click="toRefundAndExchange(items.productCode)" class="buy-now" v-if="orderDetail.status === 60">申请售后</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-list order-info">
                <div class="other-info border-bottom">
                    <div class="other-info-row">
                        <div class="fixed-flex-item">商品金额</div>
                        <div class="elastic-flex-item text-right">
                            <span>¥</span>
                            <span class="price">{{orderDetail.productTotalPrice}}</span>
                        </div>
                    </div>
                    <div class="other-info-row">
                        <div class="fixed-flex-item">快递运费</div>
                        <div class="elastic-flex-item text-right">
                            <span>¥</span>
                            <span class="price">{{orderDetail.freight}}</span>
                        </div>
                    </div>
                    <div class="other-info-row">
                        <div class="fixed-flex-item">优惠</div>
                        <div class="elastic-flex-item text-right">
                            <span>¥</span>
                            <span class="price">-{{orderDetail.discountedPrice}}</span>
                        </div>
                    </div>
                </div>
                <div class="order-total">
                    <span>共{{orderTotalNum}}件商品，合计：</span>
                    <span class="price">¥</span>
                    <span class="price">{{orderDetail.actuallyPayPrice}}</span>
                    <!--.00-->
                </div>
            </div>

            <div class="info-list order-info">
                <div class="other-info">
                    <div class="other-info-row">
                        <div class="fixed-flex-item font-30 font-black">订单信息</div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status != 50 && orderDetail.status != 60">
                        <div class="key-item">下单时间：</div>
                        <div class="value-item">{{orderDetail.createdAt}}</div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status != 50 && orderDetail.status != 60">
                        <div class="key-item">付款方式：</div>
                        <div class="value-item">微信支付</div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status != 5">
                        <div class="key-item">订单编号：</div>
                        <div class="value-item">{{orderDetail.code}}</div>
                        <div class="fixed-flex-item left-padding">
                            <span class="font-red" @click="setClipboardData(orderDetail.code)">复制</span>
                        </div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status != 5">
                        <div class="key-item">微信交易号：</div>
                        <div class="value-item">{{orderDetail.payNumber}}</div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status != 5">
                        <div class="key-item">创建时间：</div>
                        <div class="value-item">{{orderDetail.createdAt}}</div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status != 5">
                        <div class="key-item">付款时间：</div>
                        <div class="value-item">{{orderDetail.payAt}}</div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status != 5 && orderDetail.status != 30">
                        <div class="key-item">发货时间：</div>
                        <div class="value-item">{{orderDetail.freightAt}}</div>
                    </div>
                    <div class="other-info-row" v-if="orderDetail.status == 60 ">
                        <div class="key-item">成交时间：</div>
                        <div class="value-item">2018-12-12 15:32:22</div>
                    </div>
                    <div class="other-info-row">
                        <div class="key-item">备注：</div>
                        <div class="value-item">{{orderDetail.remarks || '暂无'}}</div>
                    </div>
                </div>
            </div>

            <div class="order-operates" v-if="orderDetail.status == 5">
                <button @click="toService">联系客服</button>
                <button @click="cancelOrder(orderDetail)">取消订单</button>
                <button class="obvious" @click="payOrder(orderDetail)">去付款</button>
            </div>
            <div class="order-operates" v-if="orderDetail.status == 50">
                <!--<button>拒收货</button>-->
                <!--<button>查看物流</button>-->
                <button class="obvious" @click="receive(orderDetail)">确认收货</button>
            </div>
            <div class="order-operates" v-if="orderDetail.status == 60">
                <!--<button disabled>查看物流</button>-->
                <button @click="toRefundAndExchange(orderDetail)">申请售后</button>
                <button @click="toComment(orderDetail)">评价</button>
            </div>

        </div>
    </div>
</template>

<script>
    import Basic from '@/mixins/basic.js'

    export default {
      mixins: [Basic],
      data () {
        return {
          orderDetail: [],
          orderTotalNum: ''
        }
      },
      methods: {
        mountedNextTick () {
          let that = this
          that.getOrderDetail()
        },
        toInvoice () {
          this.$utils.navigateTo('/pages/user/invoice/main')
        },
        toAddress () {
          this.$utils.navigateTo('/pages/user/address/main')
        },
        // 订单详情
        getOrderDetail () {
          let that = this
          if (!that.query.orderCode) {
            return
          }
          let options = {
            url: `/order/${that.query.orderCode}`,
            // url: `/order/KA201901200000D`,
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              this.orderTotalNum = data.products.length
              that.orderDetail = data
            })
            .catch(err => {
              that.$utils.error(`获取订单详情失败，${err}`)
            })
        },
        // 获取订单支付数据
        payOrder (info = {}) {
          let that = this
          that.$utils.showLoading()
          let options = {
            url: `/order/pay/${info.code}`,
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.$utils.hideLoading()
              // that.requestWxPayment(data)
              if (data.payType === 10) {
                that.requestWxPayment(data)
              } else {
                // 跳线下支付
                this.$store.commit('setOrderOffline', data)
                this.$utils.navigateTo('/pages/user/order-offline/main')
              }
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`获取订单支付数据失败，${err}`)
            })
        },
        // 调起微信支付
        requestWxPayment (options = {}) {
          let that = this
          wx.requestPayment({
            timeStamp: options.timeStamp,
            nonceStr: options.nonceStr,
            package: options.package,
            signType: options.signType,
            paySign: options.paySign,
            success (res) {
              console.log('支付成功')
              that.getOrderDetail()
            },
            fail (res) {
              console.log('支付失败')
            }
          })
        },
        // 取消订单
        async cancelOrder (info = {}) {
          let that = this
          if (!(await that.$utils.confirm('确定取消订单？'))) {
            return
          }
          that.$utils.showLoading()
          let options = {
            url: `/order/${info.code}/cancel`,
            method: 'put'
          }
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.$utils.hideLoading()
              that.$utils.success(`取消订单成功`)
              that.getOrderDetail()
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`取消订单失败，${err}`)
            })
        },
        // 确认收货
        async receive (info = {}) {
          let that = this
          if (!(await that.$utils.confirm('确认收货？'))) {
            return
          }
          that.$utils.showLoading()
          let options = {
            url: `/order/${info.code}/confirm-receipt`,
            method: 'put'
          }
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.$utils.hideLoading()
              that.$utils.success(`确认收货成功`)
              that.getOrderDetail()
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`确认收货失败，${err}`)
            })
        },
        // 评论订单
        toComment (info = {}) {
          let code = info.code || ''
          this.$utils.navigateTo('/pages/user/comment/main', {
            code: code
          })
        },
        // 申请退货
        toRefundAndExchange (code) {
          console.log(code)
          this.$utils.navigateTo('/pages/user/refund-and-exchange/main', {
            orderCode: this.orderDetail.code,
            productCode: code
          })
        },
        // 前往客服
        toService () {
          this.$utils.navigateTo('/pages/customer-service/main')
        }
      }
    }
</script>

<style lang="scss" scoped>
    .page,
    .page-main {
        background-color: #f7f7f7;
    }

    .page-main {
        padding-bottom: 0;
    }

    .iphone-x {
        .page-main {
            padding-bottom: 68px;
        }
    }

    .page-bottom {
        background-color: #ffffff;
    }

    .order-status {
        @extend .row-flex;
        @extend .align-item-center;

        height: 120px;
        padding: 0 30px;
        color: #ffffff;
        font-size: 28px;
        background-color: $theme_sub_color;
        box-sizing: border-box;
    }

    .consignee {
        @extend .row-flex;
        @extend .align-item-center;
        padding: 30px 30px 30px 0;
        margin-top: 0 !important;
        margin-bottom: 0;
        box-sizing: border-box;
        background-color: #ffffff;

        .name,
        .phone {
            font-size: 30px;
            color: $deep_black;
        }

        .name {
            margin-right: 30px;
        }

        .address {
            font-size: 26px;
            color: $light_black;
        }

        .icon-kuaidi- {
            margin-right: 30px;
            font-size: 60px;
            background-image: -webkit-gradient(linear, left top, right bottom, from(#adcfff), to(#7aadf5));
            -webkit-background-clip: text;
            /*必需加前缀 -webkit- 才支持这个text值 */
            -webkit-text-fill-color: transparent;
            /*text-fill-color会覆盖color所定义的字体颜色： */
        }

        .icon-dizhiguanli- {
            margin-right: 30px;
            font-size: 60px;
            background-image: -webkit-gradient(linear, left top, right bottom, from(#dbcaa0), to(#d3c492));
            -webkit-background-clip: text;
            /*必需加前缀 -webkit- 才支持这个text值 */
            -webkit-text-fill-color: transparent;
            /*text-fill-color会覆盖color所定义的字体颜色： */
        }
    }

    .address-line {
        line-height: 0;
        background-color: #ffffff;

        image {
            width: 100%;
        }
    }

    .order {
        padding-left: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }

    .order-number {
        @extend .row-flex;
        @extend .align-item-center;

        height: 80px;
        font-size: 24px;
        color: $black;
    }

    .product-list {
        padding-top: 0;

        .product {
            padding: 5px 30px 25px 0;
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

    .order-total {
        padding: 30px 30px 30px 0;
        font-size: 26px;
        text-align: right;
        color: $black;
        box-sizing: border-box;

        .price {
            color: $red;
        }
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

    .order-operates {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .justify-end;

        margin-top: 20px;
        padding: 20px 0;
        background-color: #ffffff;

        button {
            margin: 0 30px 0 0;
            padding: 0 32px;
            height: 60px;
            line-height: 60px;
            border-radius: 40px;
            box-sizing: border-box;
            text-align: center;
            font-size: 26px;
            color: $light_black;
            border: 1px solid $light_black;
            background-color: #ffffff;
        }

        button.obvious {
            border: 1px solid $theme_color;
            color: #ffffff;
            background-color: $theme_color;
        }

        button[disabled] {
            border: 1px solid #e5e5e5;
            color: #cccccc;
            background-color: #ffffff;
        }
    }
</style>
