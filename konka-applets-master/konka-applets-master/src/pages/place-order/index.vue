<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="consignee" @click="toAddress">
                <div class="elastic-flex-item overflow-hidden" v-if="orderAddress">
                    <div class="row-flex align-item-center bottom-gap-20">
                        <span class="name">{{orderAddress.name}}</span>
                        <span class="phone">{{orderAddress.phone}}</span>
                    </div>
                    <div class="row-flex align-item-center address">
                        {{orderAddress.provinceText}}{{orderAddress.cityText}}{{orderAddress.countyText}}{{orderAddress.address}}
                    </div>
                </div>
                <div class="elastic-flex-item font-30 font-deep-black" v-else>
                    请选择收货地址信息
                </div>
                <div class="fixed-flex-item">
                    <i class="icon iconfont icon-tiaozhuan-"></i>
                </div>
            </div>
            <div class="address-line">
                <img src="../../../static/image/address_line.jpg" mode="widthFix" />
            </div>

            <!--商品列表-->
            <div class="product-list">
                <div class="product" v-for="(each, n) in placeProducts" :key="n">
                    <div class="product-image fixed-flex-item">
                        <image :src="each.imageUrl" mode="aspectFit"/>
                    </div>
                    <div class="elastic-flex-item column-flex">
                        <div class="product-title fixed-flex-item">
                            {{each.name}}
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
                                <div class="row-flex align-item-end">
                                    <div class="present-price">
                                        <span>¥</span>
                                        <span class="price">{{each.num * each.price}}</span>
                                        <!--.00-->
                                    </div>
                                </div>
                            </div>
                            <!--<div class="elastic-flex-item">-->
                                <!--<div class="text-right">-->
                                    <!--<button class="buy-now">立即</button>-->
                                <!--</div>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-list">
                <div class="info-row">
                    <div class="row-name">配送方式</div>
                    <div class="row-value">顺丰快递</div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <!--<div class="info-row">-->
                    <!--<div class="row-name">使用优惠券</div>-->
                    <!--<div class="row-value">200元</div>-->
                    <!--<i class="icon iconfont icon-tiaozhuan- row-icon"></i>-->
                <!--</div>-->
                <div class="info-row" @click="toInvoice">
                    <div class="row-name">发票信息</div>
                    <div class="row-value" v-if="orderInvoice">{{orderInvoice.unitName}}</div>
                    <div class="row-value" v-else>请选择</div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="info-row">
                    <div class="row-name">备注:</div>
                    <div class="order-remark">
                        <input placeholder="请输入留言给商家的话" v-model="remark">
                    </div>
                </div>
                <div class="other-info">
                    <div class="other-info-row">
                        <div class="fixed-flex-item">商品金额</div>
                        <div class="elastic-flex-item font-red text-right">
                            <span>¥</span>
                            <span class="price">{{productsTotalPrice}}</span>
                        </div>
                    </div>
                    <div class="other-info-row">
                        <div class="fixed-flex-item">运费</div>
                        <div class="elastic-flex-item font-red text-right">
                            <span>¥</span>
                            <span class="price">{{freight}}</span>
                        </div>
                    </div>
                    <div class="other-info-row">
                        <div class="fixed-flex-item">优惠</div>
                        <div class="elastic-flex-item font-red text-right">
                            <span>¥</span>
                            <span class="price">{{discount}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-bottom">
            <div class="row-flex align-item-center">
                <div class="total-info">
                    共{{productsTotalNum}}件，实付款：
                    <span class="font-red">¥</span>
                    <span class="font-red">{{shouldPayPrice}}</span>
                </div>
                <div class="operate-btn">
                    <button @click="orderAndPay">立即支付</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    data () {
      return {
        remark: ''
      }
    },
    computed: {
      ...mapGetters([
        'placeProducts',
        'orderAddress',
        'orderInvoice',
        'partnerCode'
      ]),
      productsTotalPrice () {
        let price = 0
        let placeProducts = this.placeProducts
        console.log('partnerCode====>', this.partnerCode)
        console.log('placeProducts==>', placeProducts)
        for (let n = 0; n < placeProducts.length; n++) {
          price += placeProducts[n].price * placeProducts[n].num
        }
        return price.toFixed(2)
      },
      productsTotalNum () {
        let num = 0
        let placeProducts = this.placeProducts
        for (let n = 0; n < placeProducts.length; n++) {
          num += placeProducts[n].num
        }
        return num
      },
      freight () {
        return 0
      },
      discount () {
        return 0
      },
      shouldPayPrice () {
        return Number(this.productsTotalPrice + this.freight - this.discount).toFixed(2)
      }
    },
    methods: {
      toInvoice () {
        this.$utils.navigateTo('/pages/user/invoice/main', {
          ordering: true
        })
      },
      toAddress () {
        this.$utils.navigateTo('/pages/user/address/main', {
          ordering: true
        })
      },
      // 下单并调起支付
      orderAndPay () {
        let that = this

        // 转换购买产品的格式
        let placeProducts = this.placeProducts
        console.log('placeProducts=====--0>0', placeProducts)
        let products = []
        for (let n = 0; n < placeProducts.length; n++) {
          products.push({
            shoppingCartId: placeProducts[n].id || '',
            productSpecificationCodes: placeProducts[n].productSpecificationCode || '',
            num: placeProducts[n].num || 0
          })
        }

        that.$utils.showLoading()

        let options = {
          url: `/order`,
          method: 'post',
          data: {
            payType: 10, // 支付方式   可空
            distribution: 10, // 配送方式
            addressesCode: that.orderAddress.code, // 地址编码
            userCouponCode: '', // 优惠劵编码
            invoiceCode: that.orderInvoice.code, // 发票编码
            freight: that.freight, // 邮费
            products: products,
            partnerCode: this.partnerCode || '', // 合伙人编码
            remarks: this.remark, // 备注
            actuallyPayPrice: that.shouldPayPrice // 支付金额
          }
        }
        console.log('order-info===>', options)
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            // that.$utils.success(`保存订单信息成功`)
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
            that.$utils.error(`保存订单信息失败，${err}`)
          })
      },
      // 调起微信支付
      requestWxPayment (options = {}) {
        // options = {
        //   'appId': 'wx71fc390e9e0dc6d7',
        //   'timeStamp': '1547773828',
        //   'nonceStr': '5c41278415b6b',
        //   'package': 'prepay_id=wx18091027205023d8b28c04951727496206',
        //   'signType': 'MD5',
        //   'paySign': '9BD666A005586D529F7D7DDFF1FDF42B'
        // }
        let that = this
        wx.requestPayment({
          timeStamp: options.timeStamp,
          nonceStr: options.nonceStr,
          package: options.package,
          signType: options.signType,
          paySign: options.paySign,
          success (res) {
            that.$utils.success('支付成功', () => {
              that.$utils.redirectTo('/pages/user/my-orders/main')
            })
          },
          fail (res) {
            that.$utils.error('支付失败', () => {
              that.$utils.redirectTo('/pages/user/my-orders/main')
            })
          }
        })
      }
    },
    onHide () {
      // 前进一页的时候触发
      console.log('onHide')
      // 清除结算产品缓存
      // this.$store.commit('setPlaceProducts', [])
    },
    onUnload () {
      // 后退和替换当前页的时候触发
      console.log('onUnload')
      // 清除结算产品缓存
      // this.$store.commit('setPlaceProducts', [])
    }
  }
</script>

<style lang="scss" scoped>
    .order-remark{
        font-size: 28px;
        margin-left: 340px;
        width: 300px;
    }
    .page, .page-main {
        background-color: #f7f7f7;
    }
    .page-bottom {
        background-color: #ffffff;
    }
    .consignee {
        @extend .row-flex;
        @extend .align-item-center;
        padding: 30px;
        margin-bottom: 0;
        box-sizing: border-box;
        background-color: #ffffff;

        .name, .phone {
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
        .icon-tiaozhuan- {
            font-size: 30px;
            color: $light_black;
        }
    }
    .address-line {
        line-height: 0;
        background-color: #ffffff;

        image {
            width: 100%;
        }
    }

    .product-list {
        padding-top: 0;

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
        font-size: 28px;
        padding-right: 30px;
        color: $black;
    }


    .total-info {
        @extend .elastic-flex-item;

        font-size: 28px;
        color: $deep_black;
        padding: 0 30px;
        box-sizing: border-box;
    }
    .total-info .price {
        color: $red;
    }
    .operate-btn {
        @extend .fixed-flex-item;
    }
    .operate-btn button {
        width: 220px;
        height: 100px;
        line-height: 100px;
        font-size: 28px;
        color: #ffffff;
        background-color: $red;
        text-align: center;
    }
</style>
