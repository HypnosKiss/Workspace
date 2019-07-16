<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div v-for="(each,index) in info" :key="index">
                <div class="product">
                    <div class="product-image fixed-flex-item">
                        <image :src="each.imageUrl"
                               mode="aspectFit"/>
                    </div>
                    <div class="elastic-flex-item column-flex align-item-stretch">
                        <div class="font-28 font-deep-black font-bold">商品评分</div>
                        <div class="star-line row-flex align-item-center">
                            <i class="icon iconfont icon-pingjia-weixuanzhong- active" @click="star(index, 1)"></i>
                            <i class="icon iconfont icon-pingjia-weixuanzhong-" :class="[{'active':each.stars>=2}]"  @click="star(index, 2)"></i>
                            <i class="icon iconfont icon-pingjia-weixuanzhong-" :class="[{'active':each.stars>=3}]"  @click="star(index, 3)"></i>
                            <i class="icon iconfont icon-pingjia-weixuanzhong-" :class="[{'active':each.stars>=4}]"  @click="star(index, 4)"></i>
                            <i class="icon iconfont icon-pingjia-weixuanzhong-" :class="[{'active':each.stars>=5}]"  @click="star(index, 5)"></i>
                        </div>
                    </div>
                </div>

                <div class="comment">
                    <div class="title">商品评价</div>
                    <div class="content">
                        <textarea auto-height="true" placeholder="对这件商品有什么评价" placeholder-style="color: #cccccc;" v-model="each.content"></textarea>
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

  export default {
    mixins: [Basic],
    data () {
      return {
        info: [],
        code: ''
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        console.log(that.query.productCode)
        that.$utils.showLoading()
        let options = {
          url: `/order/${that.query.productCode}`,
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(data => {
            that.$utils.hideLoading()
            console.log(data)
            that.info = data.products
            that.code = that.query.productCode
            for (let i in that.info) {
              that.info[i].stars = 1
              that.info[i].content = ''
            }
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`获取订单信息失败，${err}`)
          })
      },
      submit () {
        let that = this
        let products = []
        for (let i in that.info) {
          if (that.info[i].content === '' || that.info[i].content === ' ') {
            that.$utils.error(`请将评论填写完整！`)
            return
          } else {
            let o = {
              'productCode': that.info[i].productCode,
              'rate': that.info[i].stars,
              'content': that.info[i].content
            }
            products.push(o)
          }
        }
        let options = {
          url: `/evaluation/${that.code}`,
          method: 'post',
          data: {
            products: products
          }
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.redirectTo('/pages/product-detail/main', {productCode: products[0].productCode})
          })
          .catch(err => {
            that.$utils.error(`评论提交失败，${err}`)
          })

        // that.$utils.showModal('点击了提交')
        //   let options
      },
      star (index, i) {
        let shit = this.$utils.cloneObject(this.info)
        shit[index].stars = i
        this.info = shit
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
    }

    .product {
        @extend .row-flex;
        @extend .align-item-center;

        margin-top: 20px;
        padding: 20px 30px;
        border-bottom: 0;
        background-color: #ffffff;
        box-sizing: border-box;
    }
    .product-image {
        @extend .position-relative;
        width: 240px;
        height: 180px;
        line-height: 0;
        margin-right: 30px;
        background-color: #f5f5f5;
    }

    .product-image image {
        @extend .position-absolute;
        max-width: 96%;
        max-height: 96%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .star-line {
        margin-top: 30px;
    }
    .icon-pingjia-weixuanzhong- {
        font-size: 40px;
        color: #cccccc;
    }
    .icon-pingjia-weixuanzhong-.active {
        color: #ffa30f;
    }

    .comment {
        margin-top: 20px;
        padding: 30px;
        box-sizing: border-box;
        background-color: #ffffff;

        .title {
            color: $deep_black;
            font-size: 28px;
            margin-bottom: 30px;
        }

        textarea {
            width: 100%;
            border: 0;
            outline: 0;
            margin: 0 0 20px 0;
            font-size: 28px;
            min-height: 140px;
        }
    }


    .submit-btn {
        line-height: 96px;
        font-size: 28px;
        color: #ffffff;
        background-color: $theme_color;
    }

</style>
