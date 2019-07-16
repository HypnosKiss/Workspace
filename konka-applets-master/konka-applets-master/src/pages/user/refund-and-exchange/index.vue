<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">

            <div class="order">
                <div class="order-number">
                    <span>订单号：{{orderInfo.code}}</span>
                </div>
                <!--商品列表-->
                <div class="product-list">
                    <div class="product">
                        <div class="product-image fixed-flex-item">
                            <image :src="productInfo.imageUrl" mode="aspectFit"/>
                        </div>
                        <div class="elastic-flex-item column-flex">
                            <div class="product-title fixed-flex-item">
                                {{productInfo.title}}
                            </div>
                            <div class="product-sales-info elastic-flex-item align-item-end">
                                <div class="product-price elastic-flex-item">
                                    <div class="row-flex align-item-end bottom-gap-10">
                                        <div class="sale elastic-flex-item">
                                            {{productInfo.specificationsText}}
                                        </div>
                                        <div class="number fixed-flex-item">
                                            ×{{productInfo.num}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="order-total">-->
                    <!--<span>共{{orderInfo.products ? orderInfo.products.length : 0}}件商品，合计：</span>-->
                    <!--<span class="price">¥</span>-->
                    <!--<span class="price">{{info.productTotalPrice}}</span>-->
                    <!--&lt;!&ndash;.00&ndash;&gt;-->
                <!--</div>-->
            </div>

            <div class="info-list order-info">
                <div class="info-row">
                    <div class="row-name">申请退换货</div>
                    <div class="row-value row-flex align-item-center justify-end">
                        <div v-for="(each, n) in types" :key="n"
                             :class="['radio-item', {'active': info.type === each.key}]"
                             @click="changeType(each)">
                            <i class="icon iconfont icon-xuanze-weixuanzhong-"></i>
                            <i class="icon iconfont icon-xuanze-xuanzhong-"></i>
                            <span class="item-name">{{each.name}}</span>
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="row-name">退换货方式</div>
                    <div class="row-value row-flex align-item-center justify-end">
                        <div v-for="(each, n) in operationModes" :key="n"
                             :class="['radio-item', {'active': info.refundType === each.key}]"
                             @click="changeRefundType(each)">
                            <i class="icon iconfont icon-xuanze-weixuanzhong-"></i>
                            <i class="icon iconfont icon-xuanze-xuanzhong-"></i>
                            <span class="item-name">{{each.name}}</span>
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="row-name">退换货数量</div>
                    <div class="row-value row-flex align-item-center justify-end">
                        <input type="number" v-model="info.num" placeholder="请输入需要退换货数量">
                    </div>
                </div>
                <div class="problem-description">
                    <div class="title">问题描述</div>
                    <div class="content">
                        <textarea placeholder="请描述您的退换货原因"
                                placeholder-style="color: #cccccc;"
                                v-model="info.content"></textarea>
                        <div class="image-upload">
                            <image-upload
                                    :limit="9"
                                    :defaultImages="images"
                                    @change="changeImages"></image-upload>
                        </div>
                    </div>
                    <div class="shortcuts">
                        <div class="shortcut-item" v-for="(each, n) in contentExamples" :key="n"
                             @click="addToContent(each)">{{ each }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <cover-view class="page-bottom">
            <button class="submit-btn" @click="submit">提交</button>
        </cover-view>
    </div>
</template>

<script>
    import Basic from '@/mixins/basic.js'
    import ImageUpload from '@/components/image-upload.vue'

    export default {
      mixins: [Basic],
      components: {
        ImageUpload
      },
      data () {
        return {
          types: [
            {
              key: 10,
              name: '退货'
            },
            {
              key: 20,
              name: '换货'
            }
          ],
          operationModes: [
            {
              key: 10,
              name: '上门取件'
            }
            // {
            //   key: 20,
            //   name: '快递发回商家'
            // }
          ],
          contentExamples: [
            '货物损坏',
            '商品规格不符',
            '质量不好',
            '商品规格不符'
          ],
          orderInfo: {},
          info: this.defaultInfo()
        }
      },
      computed: {
        productInfo () {
          let returnProduct = {}
          if (this.orderInfo.products !== undefined) {
            this.orderInfo.products.forEach((product) => {
              if (product.productCode === this.query.productCode) {
                returnProduct = product
              }
            })
          }
          return returnProduct
        }
      },
      methods: {
        mountedNextTick () {
          this.getOrderDetail()
        },
        defaultInfo () {
          return {
            type: 10, // 类型  10退 20换
            refundType: 10, // 退货类型  10上门取件  20快递
            content: '', // 描述
            images: [],
            price: 0, // 退货金额
            num: 1,
            products: []
          }
        },
        getOrderDetail () {
          console.log('ddd')
          console.log(this.query)
          let that = this
          if (!that.query.orderCode) {
            return
          }
          let options = {
            url: `/order/${that.query.orderCode}`,
            method: 'get'
          }
          that.$utils.showLoading()
          that.$utils.requestServer(options)
            .then(data => {
              that.$utils.hideLoading()
              that.orderInfo = data
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`获取订单详情失败，${err}`)
            })
        },
        changeType (data = {}) {
          if (!data.key) {
            return
          }
          let info = this.$utils.cloneObject(this.info)
          info.type = data.key
          this.info = info
        },
        changeRefundType (data = {}) {
          if (!data.key) {
            return
          }
          let info = this.$utils.cloneObject(this.info)
          info.refundType = data.key
          this.info = info
        },
        changeImages (images = []) {
          let contentImages = []
          for (let n = 0; n < images.length; n++) {
            contentImages.push({
              image: images[n].filename,
              order: n
            })
          }
          this.info.images = contentImages
        },
        addToContent (content = '') {
          if (!content) {
            return
          }
          let info = this.$utils.cloneObject(this.info)
          info.content += content
          this.info = info
        },
        submit () {
          let that = this

          let options = {
            url: `/refund-order`,
            method: 'post',
            data: Object.assign(that.info, {
              orderCode: this.query.orderCode,
              productCode: this.query.productCode
            })
          }
          that.$utils.showLoading()
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.$utils.hideLoading()
              wx.redirectTo({'url': '/pages/user/refund-and-exchange-orders/main'})
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`申请售后失败，${err}`)
            })
        }
      }
    }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }

    .page-main {
        overflow: hidden;
    }

    .page-bottom {
        background-color: #ffffff;
        z-index: 10;
    }

    .order {
        margin-top: 20px;
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
        /*padding-top: 0;*/
        background-color: #ffffff;

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

    .order-total {
        padding-right: 30px;
        padding-bottom: 30px;
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

    .info-row:nth-last-of-type(1) {
        border-bottom: 0;
    }

    .radio-item {
        margin-right: 80px;

        .item-name {
            color: $light_black;
        }
    }

    .radio-item.active {
        .item-name {
            color: $theme_color;
        }
    }

    .radio-item:nth-last-of-type(1) {
        margin-right: 0;
    }

    .problem-description {
        padding-right: 30px;
        padding-bottom: 30px;
        box-sizing: border-box;

        .title {
            height: 90px;
            line-height: 90px;

            font-size: 28px;
            color: $black;
        }

        .content {
            padding: 20px 20px 0 20px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            background-color: #fafafa;
        }

        textarea {
            width: 100%;
            line-height: 45px;
            border: 0;
            outline: 0;
            margin: 0 0 20px 0;
            font-size: 28px;
            min-height: 140px;
        }

        .shortcuts {
            padding: 24px 30px 0 0;
            box-sizing: border-box;
        }

        .shortcut-item {
            font-size: 24px;
            color: $light_black;
            padding: 5px 34px;
            margin-right: 30px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #cccccc;
            box-sizing: border-box;
            word-break: break-all;
            display: inline-block;
        }
    }

    .submit-btn {
        line-height: 96px;
        font-size: 28px;
        color: #ffffff;
        background-color: $theme_color;
    }

</style>
