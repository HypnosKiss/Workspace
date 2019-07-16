<template>
    <div :class="['page', 'product-detail', {'iphone-x': isIpx}]">
        <div class="page-main">
            <!--banner区域-->
            <div class="banners">
                <banner
                        :width="750"
                        :height="750"
                        :images="bannerImages"
                        :indicatorColor="'#DFCBB6'"
                        :indicatorActiveColor="'#7d380e'"
                        @clickImage="toBannerDetail"></banner>
            </div>

            <!--商品基本信息-->
            <div class="base-info">
                <div class="title">
                    {{info.title}}
                </div>
                <button v-if="userInfo.partner.isBind === 10" class="share" @click="saleBtn">
                    <img src="../../../static/image/share.svg" alt="">
                </button>
                <div class="row-flex align-item-end" style="clear:both;">
                    <div class="present-price">
                        <span>¥</span>
                        <span class="price">{{presentPrice}}</span>
                        <div class="kb-price" v-if="userInfo.partner.isBind === 10">{{info.commissionAmount}}</div>
                    </div>
                </div>
                <div class="row-flex align-item-end">
                    <div class="original-price">
                        <span>¥</span>
                        <span class="price">{{originalPrice}}</span>
                        <!--.00-->
                    </div>
                </div>
            </div>

            <!--优惠券-->
            <scroll-view class="scroll-x-view" scroll-x="true" hidden>
                <div class="row-coupon-list">
                    <div class="coupon discount-coupon">
                        <div class="price fixed-flex-item">
                            <span class="number">9</span>
                            <span class="spot">.</span>
                            <span class="float">5</span>
                            <span class="symbol">折</span>
                        </div>
                        <div class="info elastic-flex-item">
                            <div class="name">优惠券的名称</div>
                            <div class="condition">满3000可用减少1000元</div>
                        </div>
                    </div>
                    <div class="coupon price-coupon">
                        <div class="price fixed-flex-item">
                            <span class="number">2000</span>
                            <span class="symbol">¥</span>
                        </div>
                        <div class="info elastic-flex-item">
                            <div class="name">优惠券的名称</div>
                            <div class="condition">满3000可用减少1000元</div>
                        </div>
                    </div>
                </div>
            </scroll-view>

            <!--评论-->
            <div class="section">
                <div class="section-title">
                    评论（{{info.evaluationNum}}）
                </div>
                <div class="section-content comment-list">
                    <div class="comment" v-for="(each, n) in oneEvaluation" :key="n">
                        <div class="user-info">
                            <div class="user-header">
                                <image :src="each.avatar || defaultUserHeader"/>
                            </div>
                            <div class="user-nickname">
                                {{each.nickname || '用户'}}
                            </div>
                            <div class="create-date">
                                {{each.updatedAt}}
                            </div>
                        </div>
                        <div class="row-flex align-item-center">
                            <div class="specifications">
                                <span class="item">{{each.specificationText}}</span>
                            </div>
                            <div class="achievement">
                                <i class="icon iconfont icon-pingjia-xuanzhong- active" v-for="m in each.rate"
                                   :key="m"></i>
                                <i class="icon iconfont icon-pingjia-xuanzhong-" v-for="m in (5 - each.rate)"
                                   :key="m"></i>
                            </div>
                        </div>
                        <div class="comment-content">
                            {{each.content}}
                        </div>
                    </div>
                    <div class="more-comment" v-if="oneEvaluation.length > 0">
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
                    <div class="image-item" v-for="(each, n) in content" :key="n">
                        <image :src="each.imageUrl" mode="widthFix"/>
                    </div>
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
            <button v-if="userInfo.partner.isBind === 20" class="buy-later" @click="openPopupBox('later')">加入购物车
            </button>
            <button v-if="userInfo.partner.isBind === 20" class="buy-now" @click="openPopupBox('now')">立即购买</button>
            <button v-if="userInfo.partner.isBind === 10" class="buy-later" @click="buyBtn">买</button>
            <button v-if="userInfo.partner.isBind === 10" class="buy-now" @click="saleBtn">卖</button>
        </div>

        <!--弹窗-->
        <div :class="['popup-box', 'down-in', {'active': showPopupBox}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closePopupBox"></div>
            <div class="popup-content">
                <div class="product-base-info">
                    <div class="product-image fixed-flex-item">
                        <div class="product-thum">
                            <image :src="mainImageUrl"/>
                        </div>
                    </div>
                    <div class="elastic-flex-item overflow-hidden">
                        <div class="product-price">
                            <div class="present-price">
                                <span>¥</span>
                                <span class="price">{{presentPrice}}</span>
                                <!--.00-->
                            </div>
                            <div class="original-price">
                                <span>¥</span>
                                <span class="price">{{originalPrice}}</span>
                                <!--.00-->
                            </div>
                        </div>
                        <div class="tips">
                            {{specificationText}}
                        </div>
                    </div>
                </div>
                <scroll-view class="product-groups" scroll-y="true">
                    <div class="group" v-for="(each, n) in productSpecification" :key="n">
                        <div class="group-title">
                            <div class="group-name">{{each.name}}</div>
                        </div>
                        <div class="group-content">
                            <!--<span class="option active">规格一</span>-->

                            <span v-for="(item, m) in each.subSpecification" :key="m"
                                  :class="['option', {'active': item.checked}]" @click="choosseSpecification(n, item)">{{item.name}}</span>
                        </div>
                    </div>
                    <div class="group">
                        <div class="group-title">
                            <div class="group-name">数量</div>
                            <div class="number-input-box">
                                <number-input :min="1" :max="specificationInfo.stock" v-model="buyNum"></number-input>
                            </div>
                        </div>
                    </div>
                </scroll-view>
                <div>
                    <button v-if="userInfo.partner.isBind === 10" class="add-to-cart" :disabled="!presentPrice"
                            @click="addToCart">加入购物车
                    </button>
                    <button v-if="userInfo.partner.isBind === 10" class="order-now" :disabled="!presentPrice"
                            @click="orderNow">立即购买
                    </button>
                    <button v-if="userInfo.partner.isBind === 20" class="submit" :disabled="!presentPrice"
                            @click="buyIt">{{submitText}}
                    </button>
                </div>
            </div>
        </div>
        <!--分享弹窗-->
        <div :class="['popup-box', 'down-in', {'active': showSaleBox}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closeSaleBox"></div>
            <div class="popup-content partner-popup">
                <div class="partner-base-info">
                    <div class="info-tit"><span class="close" @click="closeSaleBox"></span></div>
                    <div class="share-info">{{ info.commissionAmount }}</div>
                    <div class="share-tips">{{ info.commissionAmount }}</div>
                    <div class="share-box">
                        <button class="share-item" @click="closeSaleBox" open-type="share">
                            <div class="share-item-icon green">
                                <img src="../../../static/image/share-wechat.svg" alt="">
                            </div>
                            <div class="share-item-text">分享至微信</div>
                        </button>
                        <div class="share-item" @click="openPosterBox">
                            <div class="share-item-icon blue">
                                <img src="../../../static/image/share-image.svg" alt="">
                            </div>
                            <div class="share-item-text">商品海报</div>
                        </div>
                        <div class="share-item" @click="openCodeBox">
                            <div class="share-item-icon red">
                                <img src="../../../static/image/share-code.svg" alt="">
                            </div>
                            <div class="share-item-text">小程序码</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--海报弹窗-->
        <div :class="['popup-box', 'down-in', {'active': posterBox}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closePosterBox"></div>
            <div class="popup-content">
                <div class="share-poster-image">
                    <img :src="posterInfo.fileUrl" alt="">
                </div>
                <div>
                    <button class="submit" :disabled="posterDownload" @click="savePoster">{{ posterDownload ? '下载中...' :
                        '一键保存' }}
                    </button>
                </div>
            </div>
        </div>
        <!--小程序码弹窗-->
        <div :class="['popup-box', 'down-in', {'active': codeBox}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closeCodeBox"></div>
            <div class="share-code-box">
                <div class="share-code-title">
                    <span class="share-code-close" @click="closeCodeBox"></span>
                </div>
                <div class="share-code-image">
                    <img :src="info.shareCodeUrl" alt="">
                </div>
                <div class="share-code-tips">扫一扫识别二维码</div>
            </div>
        </div>
        <get-phone-number></get-phone-number>
    </div>
</template>

<script>
    import Basic from '@/mixins/basic.js'
    import Banner from '@/components/banner.vue'
    import NumberInput from '@/components/number-input.vue'
    import GetPhoneNumber from '@/components/get-phone-number.vue'
    import {mapGetters} from 'vuex'

    export default {
      mixins: [Basic],
      components: {
        Banner,
        NumberInput,
        GetPhoneNumber
      },
      data () {
        return {
          info: this.defaultInfo(),
          specification: {
            // '父code': '子code'
          },
          buyNum: 1,
          showPopupBox: false,
          buyType: '',
          showSaleBox: false,
          posterBox: false,
          codeBox: false,
          posterDownload: false,
          posterInfo: {}
        }
      },
      computed: {
        ...mapGetters([
          'userInfo'
        ]),
        bannerImages () {
          return this.info.image || []
        },
        minPrice () {
          let result = 0
          let productSpecifications = this.info.productSpecifications || []
          for (let n = 0; n < productSpecifications.length; n++) {
            if (productSpecifications[n].price < result) {
              result = productSpecifications[n].price
            }
          }
          return result
        },
        maxPrice () {
          let result = 0
          let productSpecifications = this.info.productSpecifications || []
          for (let n = 0; n < productSpecifications.length; n++) {
            if (result < productSpecifications[n].price) {
              result = productSpecifications[n].price
            }
          }
          return result
        },
        defaultPresentPrice () {
          let result = '暂无价格'
          if (this.minPrice === this.maxPrice) {
            if (this.minPrice !== 0) {
              result = this.minPrice
            }
          } else {
            result = `${this.minPrice}~${this.maxPrice}`
          }
          return result
        },
        specificationInfo () {
          let result = {}
          let specification = Object.values(this.specification).sort()
          let productSpecifications = this.info.productSpecifications || []
          for (let n = 0; n < productSpecifications.length; n++) {
            //   console.log('所选====>', JSON.stringify(specification))
            //   console.log('已有====>', JSON.stringify(productSpecifications[n].specificationCodes.sort()))
            if (JSON.stringify(specification) === JSON.stringify(productSpecifications[n].specificationCodes.sort())) {
              result = productSpecifications[n]
              break
            }
          }
          return result
        },
        presentPrice () {
          return this.specificationInfo.price || this.defaultPresentPrice
        },
        originalPrice () {
          return this.specificationInfo.guidePrice || this.defaultPresentPrice
        },
        mainImageUrl () {
          return this.info.mainImageUrl || ''
        },
        content () {
          return this.info.content || []
        },
        productSpecification () {
          let specification = Object.values(this.specification)
          let specificationArray = this.$utils.cloneObject(this.info.specificationArray || [])

          for (let n = 0; n < specificationArray.length; n++) {
            let items = specificationArray[n].subSpecification
            for (let m = 0; m < items.length; m++) {
              items[m].checked = specification.includes(items[m].code)
            }
          }

          return specificationArray
        },
        specificationText () {
          let result = []
          let specification = Object.values(this.specification)
          let productSpecification = this.productSpecification || []

          for (let n = 0; n < productSpecification.length; n++) {
            let items = productSpecification[n].subSpecification
            for (let m = 0; m < items.length; m++) {
              if (specification.includes(items[m].code)) {
                result[n] = items[m].name
                break
              }
            }
          }
          if (result.length === 0) {
            return result[0]
          } else {
          }
          return result.join('，') || '请选择规格'
        },
        oneEvaluation () {
          return this.info.oneEvaluation || []
        },
        submitText () {
          return this.presentPrice ? '确认' : '缺货中'
        }
      },
      onShareAppMessage () {
        let baseUrl = '/pages/product-detail/main' + this.$utils.pathQueryToStr({productCode: this.info.code})
        if (this.userInfo.partner.isBind === 10) baseUrl += '&partnerCode=' + this.userInfo.partner.inviteCode
        console.log(baseUrl)
        return {
          title: this.info.title,
          path: baseUrl,
          imageUrl: this.info.mainImageUrl
        }
      },
      methods: {
        mountedNextTick () {
          let that = this
          that.getProductDetail()
          console.log('product query=====>')
          console.log(this.query)
          let partnerCode = this.$root.$mp.query.partnerCode
          partnerCode = this.query.U ? this.query.U : partnerCode
          console.log('this.$root.$mp.query', partnerCode)
          if (partnerCode) {
            this.$store.commit('setPartnerCode', partnerCode)
          }
        },
        defaultInfo () {
          return {}
        },
        getProductDetail () {
          let that = this
          let productCode = this.query.productCode
          productCode = this.query.P ? this.query.P : productCode
          if (!productCode) {
            return
          }
          let options = {
            url: `/product/${productCode}`,
            method: 'get'
          }
          that.$utils.showLoading()
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.$utils.hideLoading()
              that.info = data

              // 默认选中所有选项中的第一个
              let specification = {}
              for (let n = 0; n < data.specificationArray.length; n++) {
                specification[data.specificationArray[n].code] = data.specificationArray[n].subSpecification[0].code
              }
              that.specification = specification
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`获取产品详情失败，${err}`)
            })
        },
        choosseSpecification (start, item) {
          // console.log('start===>', start)
          // console.log('code===>', code)
          let specification = this.$utils.cloneObject(this.specification)
          specification[item.parentCode] = item.code
          this.specification = specification
        },
        saleBtn () {
          this.showSaleBox = true
        },
        closeSaleBox () {
          this.showSaleBox = false
        },
        openPosterBox () {
          let options = {
            url: `/product-poster/${this.info.code}`,
            method: 'get'
          }
          this.$utils.showLoading()
          this.$utils.requestServer(options).then(response => {
            console.log(response)
            this.$utils.hideLoading()
            this.posterInfo = response
            this.posterBox = true
            this.showSaleBox = false
          }).catch(error => {
            this.$utils.hideLoading()
            this.$utils.error(`生成海报失败，${error}`)
          })
        },
        closePosterBox () {
          this.posterBox = false
        },
        savePoster () {
          this.posterDownload = true
          wx.downloadFile({
            url: this.posterInfo.fileUrl,
            success: function (res) {
              this.saveFileToThumb(res.tempFilePath)
              this.posterDownload = false
            }.bind(this),
            fail: function () {
              this.posterDownload = false
            }.bind(this)
          })
        },
        saveFileToThumb (filePath) {
          wx.getSetting({
            success: function (res) {
              if (!res.authSetting['scope.writePhotosAlbum']) {
                wx.authorize({
                  scope: 'scope.writePhotosAlbum',
                  success: function () {
                    wx.saveImageToPhotosAlbum({
                      filePath: filePath,
                      success: function () {
                        wx.showToast({
                          title: '保存成功'
                        })
                        this.posterBox = false
                      }.bind(this)
                    })
                  }.bind(this),
                  fail: function (error) {
                    console.log(error)
                    wx.showToast({
                      title: '保存海报失败，请授权写入'
                    })
                  }
                })
              } else {
                wx.saveImageToPhotosAlbum({
                  filePath: filePath,
                  success: function () {
                    wx.showToast({
                      title: '保存成功'
                    })
                    this.posterBox = false
                  }.bind(this),
                  fail: function (error) {
                    console.log(error)
                    wx.showToast({
                      title: '保存海报失败，请授权写入'
                    })
                  }
                })
              }
            }.bind(this)
          })
        },
        openCodeBox () {
          this.codeBox = true
          this.showSaleBox = false
        },
        closeCodeBox () {
          this.codeBox = false
        },
        buyBtn () {
          this.showPopupBox = true
        },
        openPopupBox (buyType = 'later') {
          this.buyType = buyType
          this.showPopupBox = true
        },
        closePopupBox () {
          this.buyType = ''
          this.showPopupBox = false
        },
        buyIt () {
          if (this.buyType === 'later') {
            // 将所选产品增加到购物车
            // this.$utils.showModal('将所选产品增加到购物车')
            this.addToCart()
          }
          if (this.buyType === 'now') {
            // 立即购买
            // this.$utils.showModal('立即购买')
            this.orderNow()
          }
        },
        addToCart () {
          let that = this

          if (!that.hadLogin) {
            that.openGetPhoneNumber()
            return
          }
          that.$utils.showLoading()

          let options = {
            url: `/shopping-cart`,
            method: 'post',
            data: {
              'productSpecificationCode': that.specificationInfo.code, // 产品规格编码
              'num': that.buyNum // 数量
            }
          }
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.$utils.hideLoading()
              that.closePopupBox()
              that.$utils.error(`添加购物车成功`)
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`添加购物车失败，${err}`)
            })
        },
        orderNow () {
          let specificationInfo = this.specificationInfo
          console.log('specificationInfo------->', specificationInfo)
          // console.log(JSON.stringify(specificationInfo))
          let products = [
            {
              productCode: specificationInfo.productCode,
              productSpecificationCode: specificationInfo.code,
              num: this.buyNum,
              name: this.info.title,
              price: specificationInfo.price,
              imageUrl: specificationInfo.imageUrl,
              productSpecificationText: this.specificationText
            }
          ]
          this.$store.commit('setPlaceProducts', products)
          this.$utils.navigateTo('/pages/place-order/main')
        },
        toProductComment () {
          this.$utils.navigateTo('/pages/product-comment/main', {productCode: this.query.productCode})
        },
        toCustomerService () {
          this.$utils.navigateTo('/pages/customer-service/main')
        },
        toCart () {
          this.$utils.navigateTo('/pages/cart/main')
        }
      },
      onHide () {
        // 前进一页的时候触发
        console.log('onHide')
      },
      onUnload () {
        // 后退和替换当前页的时候触发
        console.log('onUnload')
        this.resetComponent()
      }
    }
</script>

<style lang="scss" scoped>
    .info-tit {
        overflow: hidden;
    }

    .info-tit .close:before {
        content: '\00D7';
        font-size: 80px;
        color: #090909;
    }

    .info-tit .close {
        line-height: 80px;
        display: block;
        float: right;
        margin-right: 10px;
        margin-top: 10px;
        width: 80px;
        height: 80px;
        font-size: 0;
    }

    .share-poster-image {
        text-align: center;
        padding-top: 10px;
    }

    .share-poster-image img {
        width: 600px;
        height: 915px;
        vertical-align: middle;
    }

    .share-code-box {
        position: fixed;
        width: 600px;
        height: 500px;
        left: 50%;
        margin-left: -300px;
        top: 50%;
        margin-top: -250px;
        background: #ffffff;
    }

    .share-code-title {
        overflow: hidden;
        height: 60px;
        font-size: 0;
    }

    .share-code-close {
        float: right;
        display: block;
        line-height: 60px;
        width: 60px;
        height: 60px;
        font-size: 0;
    }

    .share-code-close:before {
        content: '\00D7';
        font-size: 60px;
        color: #090909;
    }

    .share-code-image {
        text-align: center;
        font-size: 0;
        line-height: 300px;
        margin-top: 30px;
    }

    .share-code-image img {
        width: 250px;
        height: 250px;
        display: inline-block;
        vertical-align: middle;
    }

    .share-code-tips {
        text-align: center;
        color: #373737;
        font-size: 28px;
        margin-top: 20px;
    }

    .share-info {
        display: block;
        color: $red;
        text-align: center;
        font-size: 40px;
        padding-bottom: 40px;
    }

    .share-tips {
        text-align: center;
        display: block;
        color: #676767;
        font-size: 25px;
        padding-bottom: 30px;
    }

    .share-box {
        border-top: 1px solid #ECECEC;
        padding-top: 80px;
        font-size: 0;
    }

    .share-item {
        width: 33.3%;
        display: inline-block;
        vertical-align: middle;
        text-align: center;
        padding-bottom: 50px;
        background: none;
    }

    .share-item-icon {
        width: 170px;
        height: 170px;
        line-height: 170px;
        border-radius: 85px;
        text-align: center;
        display: inline-block;
    }

    .share-item-icon.green {
        background: #f7fcf5;
    }

    .share-item-icon.red {
        background: #fff5f4;
    }

    .share-item-icon.blue {
        background: #f6fbff;
    }

    .share-item-icon img {
        width: 80px;
        height: 80px;
        display: inline-block;
        vertical-align: middle;
    }

    .share-item-text {
        padding-top: 50px;
        line-height: 40px;
        font-size: 26px;
        text-align: center;
    }


    .share-info:before {
        content: '赚';
    }

    .share-info:after {
        content: 'K币';
    }

    .share-tips:before {
        content: '只要你的好友通过你的链接购买此产品，你就能赚到至少';
    }

    .share-tips:after {
        content: 'K币';
    }

    .kb-price {
        background: #CF9154;
        color: #ffffff;
        display: inline-block;
        vertical-align: baseline;
        margin-left: 20px;
        padding: 0 5px;
        font-size: 26px;
    }

    .add-to-cart {
        width: 50%;
        display: inline-block;
        height: 96px;
        line-height: 96px;
        background-color: #ffa30f;
        text-align: center;
        color: #ffffff;
        font-size: 28px;
    }

    .order-now {
        width: 50%;
        display: inline-block;
        height: 96px;
        line-height: 96px;
        background-color: $red;
        text-align: center;
        color: #ffffff;
        font-size: 28px;
    }

    .kb-price:before {
        content: '可赚';
    }

    .kb-price:after {
        content: 'K币';
    }

    .page, .page-main {
        background-color: #f7f7f7;
    }

    /*优惠券*/
    .row-coupon-list {
        @extend .row-flex;
        @extend .align-item-center;
        width: max-content;
        height: auto;
        padding: 20px;
    }

    .coupon {
        @extend .row-flex;
        @extend .align-item-center;
        width: 430px;
        height: 141px;
        padding: 0 70px 0 30px;
        box-sizing: border-box;
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-image: url("../../../static/image/coupon_bg.png");
        margin-right: 20px;
    }

    .coupon:nth-last-of-type(1) {
        margin-right: 0;
    }

    .coupon .info {
        overflow: hidden;
    }

    .coupon .info .name {
        @extend .line-clamp-1-ellipsis;
        font-size: 26px;
        color: #000000;
        margin-bottom: 15px;
    }

    .coupon .info .condition {
        @extend .line-clamp-1-ellipsis;
        font-size: 22px;
        color: $light_black;
    }

    .coupon .price {
        color: $red;
        margin-right: 20px;
    }

    .price-coupon .price .number {
        font-size: 60px;
        font-weight: bold;
    }

    .price-coupon .price .symbol {
        font-size: 24px;
        vertical-align: top;
    }

    .discount-coupon .price .number {
        font-size: 60px;
        font-weight: bold;
    }

    .discount-coupon .price .spot {
        font-size: 60px;
        font-weight: bold;
    }

    .discount-coupon .price .float {
        font-size: 30px;
        vertical-align: text-top;
    }

    .discount-coupon .price .symbol {
        font-size: 24px;
        vertical-align: bottom;
    }

    .title {
        width: 600px;
        float: left;
    }

    .share {
        float: right;
        background: none;
        padding: 0;
    }

    .share img {
        width: 50px;
        height: 50px;
    }
</style>
