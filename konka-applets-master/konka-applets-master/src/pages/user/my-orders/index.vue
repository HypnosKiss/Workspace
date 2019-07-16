<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
            <tab :tabs="tabs" :defaultTab="keywords.status" @change="changeTab"></tab>

            <div v-for="(each, n) in showList" :class="['order', each.cssClass]" :key="n">
                <div class="order-number" @click="toOrderDetail(each)">
                    <span class="elastic-flex-item">订单号：{{each.code}}</span>
                    <span class="fixed-flex-item">{{each.statusText}}</span>
                </div>
                <!--商品列表-->
                <div class="product-list" @click="toOrderDetail(each)">
                    <div class="product">
                        <div class="product-image fixed-flex-item">
                            <image :src="each.products[0].imageUrl"
                                   mode="aspectFit"/>
                        </div>
                        <div class="elastic-flex-item column-flex">
                            <div class="product-title fixed-flex-item">
                                {{each.products[0].title}}
                            </div>
                            <div class="product-sales-info elastic-flex-item align-item-end">
                                <div class="product-price elastic-flex-item">
                                    <div class="row-flex align-item-end bottom-gap-10">
                                        <div class="sale elastic-flex-item">
                                            {{each.products[0].specificationsText}}
                                        </div>
                                        <div class="number fixed-flex-item">
                                            ×{{each.products[0].num}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-total">
                    <span v-if="each.cssClass === 'unpaid'">共{{each.products.length}}件商品，需支付：</span>
                    <span v-else>共{{each.products.length}}件商品，合计：</span>
                    <span class="price">¥</span>
                    <span class="price">{{each.productTotalPrice}}</span>
                    <!--.00-->
                </div>
                <div class="order-operates" v-if="each.cssClass === 'unpaid'">
                    <button @click="cancelOrder(each)">取消订单</button>
                    <button class="obvious" @click="payOrder(each)">去支付</button>
                </div>
                <div class="order-operates" v-if="each.cssClass === 'waiting'">
                    <!--<button @click="toRefundAndExchange(each)">申请退款</button>-->
                    <button class="obvious" @click="handleUrgentDelivery(each)">催发货</button>
                </div>
                <div class="order-operates" v-if="each.cssClass === 'delivery'">
                    <!-- <button>延迟收货</button> -->
                    <button @click="$utils.showToast(`该功能正在开发中`)">查看物流</button>
                    <button class="obvious" @click="receive(each)">确认收货</button>
                </div>
                <div class="order-operates order-end" v-if="each.cssClass === 'end'">
                    <!--<button v-if="each.isRefund === 20" @click="toRefundAndExchange(each)">申请售后</button>-->
                    <button disabled v-if="each.isEvaluation === 20">已评价</button>
                    <button class="red" v-if="each.isEvaluation === 10" @click="toPingJia(each)">立即评价</button>
                    <button class="obvious" @click="toPlaceOrder(each)">再次购买</button>
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
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import List from '@/mixins/list.js'
  import Tab from '@/components/tab.vue'

  export default {
    mixins: [Basic, List],
    components: {
      Tab
    },
    data () {
      return {
        getListPath: '/order',
        tabs: [
          {
            key: '',
            name: '全部'
          },
          {
            key: 10,
            name: '待付款'
          },
          {
            key: 20,
            name: '待发货'
          },
          {
            key: 30,
            name: '待收货'
          },
          {
            key: 40,
            name: '待评价'
          }
        ],
        statusMap: {
          10: [5, 10],
          20: [30],
          30: [40, 50],
          40: [60, 70, 80]
        },
        statusClass: {
          10: 'unpaid',
          20: 'waiting',
          30: 'delivery',
          40: 'end'
        }
      }
    },
    computed: {
      showList () {
        let list = this.$utils.cloneObject(this.list)
        let statusMap = this.statusMap
        let statusClass = this.statusClass

        let findClass = (status) => {
          console.log('findClass===>', status)
          let result = ''
          for (let n in statusMap) {
            if (statusMap[n] && statusMap[n].includes(status)) {
              console.log('[n]===>', n)
              console.log('statusMap[n]===>', statusMap[n])
              result = statusClass[n]
              break
            }
          }
          // Object.keys(statusMap).map(n => {
          //   if (statusMap[n] && statusMap[n].includes(status)) {
          //     console.log('[n]===>', n)
          //     console.log('statusMap[n]===>', statusMap[n])
          //     result = statusClass[n]
          //     // break
          //     // return
          //   }
          // })
          console.log('result===>', result)
          return result
        }

        for (let n = 0; n < list.length; n++) {
          list[n].cssClass = findClass(list[n].status)
        }

        console.log('my-orders: showList', list)

        return list
      }
    },
    onShow () {

    },
    methods: {
      mountedNextTick () {
        let that = this
        that.defaultList()
        this.startLoading()
      },
      defaultKeywords () {
        console.log('defaultKeywords')
        return {
          status: this.query.status || '',
          code: ''
        }
      },
      changeTab (val) {
        this.keywords.status = val
        this.changeKeyword()
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
            that.changeKeyword()
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`取消订单失败，${err}`)
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
            that.changeKeyword()
          },
          fail (res) {
            console.log('支付失败')
          }
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
            // that.changeKeyword()
            // 确定收货后直接跳转评价页面
            that.toPingJia(info)
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`确认收货失败，${err}`)
          })
      },
      // 申请退货
      toRefundAndExchange (info = {}) {
        let code = info.code || ''
        this.$utils.navigateTo('/pages/user/refund-and-exchange/main', {
          code: code
        })
      },
      // 再次购买
      toPlaceOrder (info = {}) {
        const products = []
        info.products.forEach(function (product) {
          products.push({
            imageUrl: product.imageUrl,
            productSpecificationCode: product.productSpecificationsCode,
            num: product.num,
            name: product.title,
            price: product.price,
            productCode: product.productCode,
            productSpecificationText: product.specificationsText
          })
        })
        this.$store.commit('setPlaceProducts', products)
        this.$utils.navigateTo('/pages/place-order/main')
      },
      // 订单详情
      toOrderDetail (info = {}) {
        let code = info.code || ''
        this.$utils.navigateTo('/pages/user/order-detail/main', {
          orderCode: code
        })
      },
      toPingJia (info = {}) {
        let code = info.code || ''
        //   console.log('awm===>', info)

        this.$utils.navigateTo('/pages/user/comment/main', {
          productCode: code
        })
      },
      // 催发货 每个只能催3次
      handleUrgentDelivery (each) {
        // console.log(each)
        const data = this.$utils.getStorageSync('urgentDelivery')
        let arr = []
        if (!data) {
          this.$utils.setStorageSync('urgentDelivery', JSON.stringify([{code: each.code, count: 1}]), 60 * 60 * 24 * 30)
          this.sendMessage()
          // console.log('!data ', this.$utils.getStorageSync('urgentDelivery'))
          return
        } else {
          arr = JSON.parse(data)
          const index = arr.findIndex(item => item.code === each.code)
          if (index !== -1) {
            if (arr[index].count > 2) {
              this.$utils.showToast('单笔订单每日不超过3次催发货')
            } else {
              arr[index].count++
              this.sendMessage()
            }
          } else {
            // 没找到订单
            console.log(arr)
            arr.push({ code: each.code, count: 1 })
            this.sendMessage()
          }
        }
        // console.log('催发货 ', arr)
        this.$utils.setStorageSync('urgentDelivery', JSON.stringify(arr), 60 * 60 * 24 * 30)
      },
      async sendMessage () {
        const options = {
          url: '/customer-service-message',
          method: 'post',
          data: {content: `请尽快发货`, messageType: 10}
        }
        // this.$utils.showLoading()
        try {
          await this.$utils.requestServer(options)
          this.$utils.showToast('已提醒商家发货')
          // this.$utils.hideLoading()
        } catch (error) {
          // this.$utils.hideLoading()
          this.$utils.showToast('催发货失败：' + error)
          console.log(error)
        }
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
    }
    .iphone-x {
        .page-main {
            padding-bottom: 68px;
        }
    }
    .order {
        padding-left: 30px;
        margin-top: 20px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .order-number {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        height: 80px;
        padding-right: 20px;
        font-size: 24px;
        color: $black;
        box-sizing: border-box;
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
            line-height: 40px;
            /*height: 100px;*/
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
        @extend .border-bottom;

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
    .order-operates {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .justify-end;

        padding: 20px 0;

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
    .order-end {
      // justify-content:space-between;
      button.red {
        border: 1px solid $theme_color;
        color: $theme_color;
      }
    }

    .order.end .order-total {
        border-bottom: 0;
    }
</style>
