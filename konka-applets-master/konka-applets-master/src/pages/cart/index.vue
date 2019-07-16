<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="operate-line fixed-flex-item">
                <span class="operate-item" @click="endEdit" v-if="editing">完成</span>
                <span class="operate-item" @click="startEdit" v-else>编辑</span>
            </div>
            <div class="elastic-flex-item position-relative">
                <scroll-view class="position-absolute occupy-all" scroll-y="true">
                    <div class="product-list">
                        <div class="product" v-for="(each, n) in showProducts" :key="n">
                            <div class="check" @click="chooseProduct(each)">
                                <i class="icon iconfont icon-xuanze-xuanzhong-" v-if="each.checked"></i>
                                <i class="icon iconfont icon-xuanze-weixuanzhong-" v-else></i>
                            </div>
                            <div class="product-image fixed-flex-item">
                                <image :src="each.imageUrl" mode="aspectFit"/>
                            </div>
                            <div class="elastic-flex-item column-flex">
                                <div class="product-title fixed-flex-item">
                                    {{each.name}}
                                </div>
                                <div class="product-sales-info elastic-flex-item">
                                    <div class="product-price elastic-flex-item">
                                        <div class="row-flex align-item-end">
                                            <div class="specifications">
                                                <span>{{each.productSpecificationText}}</span>
                                            </div>
                                        </div>
                                        <div class="row-flex align-item-end">
                                            <div class="present-price">
                                                <span>¥</span>
                                                <span class="price">{{each.price}}</span>
                                                <!--.00-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fixed-flex-item">
                                        <div class="number-input-box">
                                            <number-input
                                                    :min="1"
                                                    :size="'small'"
                                                    v-model="products[n].num"></number-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </scroll-view>
            </div>
            <div class="cart-operate">
                <div class="row-flex align-item-center">
                    <div class="choose-all" @click="chooseAll">
                        <i class="icon iconfont icon-xuanze-xuanzhong-" v-if="hadChooseAll"></i>
                        <i class="icon iconfont icon-xuanze-weixuanzhong-" v-else></i>
                        <span>全选</span>
                    </div>
                    <div class="total-price" v-if="!editing">
                        <span>合计：</span>
                        <span class="price">¥</span>
                        <span class="price">{{totalPrice}}</span>
                    </div>
                    <div class="operate-btn">
                        <button :disabled="checkedProducts.length === 0" @click="deleteChoose" v-if="editing">删除所选</button>
                        <button :disabled="checkedProducts.length === 0" @click="toPlaceOrder" v-else>去结算</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-bottom">
            <tab-nav :navKey="'cart'"></tab-nav>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import NumberInput from '@/components/number-input.vue'
  import TabNav from '@/components/tab-nav.vue'

  export default {
    mixins: [Basic],
    components: {
      NumberInput,
      TabNav
    },
    data () {
      return {
        operateType: 'settlement',
        editing: false,
        chooseIds: [],
        products: []
      }
    },
    computed: {
      showProducts () {
        let chooseIds = this.chooseIds
        let products = this.$utils.cloneObject(this.products)
        for (let n = 0; n < products.length; n++) {
          products[n].checked = chooseIds.includes(products[n].id)
        }
        return products
      },
      totalPrice () {
        let total = 0
        let chooseIds = this.chooseIds
        let products = this.products
        for (let n = 0; n < products.length; n++) {
          if (chooseIds.includes(products[n].id)) {
            total += products[n].price * products[n].num
          }
        }
        return total.toFixed(2)
      },
      checkedProducts () {
        let result = []
        let products = this.$utils.cloneObject(this.showProducts)
        for (let n = 0; n < products.length; n++) {
          if (products[n].checked) {
            result.push(products[n])
          }
        }
        return result
      },
      hadChooseAll () {
        return (this.chooseIds.length === this.products.length) && (this.products.length > 0)
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        // that.$store.dispatch('getUserInfo')
        that.getCart()
      },
      // 获取购物车信息
      getCart () {
        let that = this
        let options = {
          url: `/shopping-cart`,
          method: 'get'
        }
        that.$utils.showLoading()
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.products = data
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`获取购物车详情失败，${err}`)
          })
      },
      // 开始编辑
      startEdit () {
        this.editing = true
      },
      // 结束编辑
      endEdit () {
        this.editing = false
        this.chooseIds = []
      },
      // 选中产品
      chooseProduct (info = {}) {
        console.log('chooseProduct')
        if (!info.id) {
          return
        }
        let chooseIds = this.$utils.cloneObject(this.chooseIds)
        let start = chooseIds.indexOf(info.id)
        if (start > -1) {
          chooseIds.splice(start, 1)
        } else {
          chooseIds.push(info.id)
        }
        this.chooseIds = chooseIds
      },
      // 选中所有
      chooseAll () {
        let chooseIds = []

        if (this.chooseIds.length === this.products.length) {

        } else {
          for (let n = 0; n < this.products.length; n++) {
            chooseIds.push(this.products[n].id)
          }
        }
        this.chooseIds = chooseIds
      },
      // 删除所选
      deleteChoose () {
        let that = this

        let deleteIt = async () => {
          // if (!(await that.$utils.confirm('确定删除所选？'))) {
          //   return
          // }
          let options = {
            url: `/shopping-cart/batch-delete`,
            method: 'put',
            data: {
              ids: that.chooseIds
            }
          }
          that.$utils.showLoading()
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.$utils.hideLoading()
              that.endEdit()
              that.getCart()
            })
            .catch(err => {
              that.$utils.hideLoading()
              that.$utils.error(`删除失败，${err}`)
            })
        }

        that.$utils.confirm('确定删除所选？')
          .then(() => {
            console.log('用户点击确定')
            deleteIt()
          })
          .catch(() => {
            console.log('用户点击取消')
          })
      },
      // 跳转到结算页
      toPlaceOrder () {
        if (this.checkedProducts.length === 0) {
          return
        }
        this.$store.commit('setPlaceProducts', this.checkedProducts)
        this.$utils.navigateTo('/pages/place-order/main')
      }
    },
    onShow () {

    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }

    .page-main {
        @extend .column-flex;
        @extend .align-item-stretch;

        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }

    .icon-xuanze-weixuanzhong- {
        font-size: 40px;
        color: $light_black;
    }

    .icon-xuanze-xuanzhong- {
        font-size: 40px;
        color: $red;
    }

    /*顶部操作栏*/
    .operate-line {
        @extend .row-reverse-flex;
        @extend .align-item-center;

        height: 80px;
        background-color: #ffffff;
    }

    .operate-item {
        font-size: 28px;
        color: $red;
        margin-right: 30px;
    }

    /*产品列表区域*/
    .product-list .product {
        padding: 30px 20px;
    }

    .check {
        line-height: 180px;
        margin-right: 18px;
    }

    .specifications {
        font-size: 24px;
        color: $light_black;
        margin-bottom: 22px;
    }

    .product-list .product-price .present-price .price {
        font-size: 26px;
    }

    .number-input-box {
        width: 160px;
        height: 48px;
    }

    /*底部操作栏*/
    .cart-operate {
        @extend .fixed-flex-item;
        @extend .position-relative;
        @extend .border-top;
        height: 96px;
        background-color: #ffffff;
    }

    .choose-all {
        @extend .elastic-flex-item;
        @extend .row-flex;
        @extend .align-item-center;

        font-size: 28px;
        color: $light_black;
    }

    .choose-all .iconfont {
        margin: 0 18px 0 20px;
    }

    .total-price {
        @extend .fixed-flex-item;

        width: 300px;
        font-size: 28px;
        color: $deep_black;
    }

    .total-price .price {
        color: $red;
    }

    .operate-btn {
        @extend .fixed-flex-item;
    }
    .operate-btn button {
        width: 220px;
        height: 96px;
        line-height: 96px;
        font-size: 28px;
        color: #ffffff;
        background-color: $red;
        text-align: center;
    }
    .operate-btn button[disabled] {
        background-color: rgba(243, 48, 54, 0.4);
    }

    .product-title{
        width: 380px;
        height: 80px;
        word-wrap: break-word;
        line-height: 40px;
        text-overflow:"" !important;
    }
</style>
