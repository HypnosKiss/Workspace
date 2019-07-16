<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <!--搜索框-->
            <search-btn @click="toSearchPage"></search-btn>
            <!--banner区域-->
            <div class="banners">
                <banner
                        :images="bannerImages"
                        :indicatorColor="'rgba(255, 255, 255, 0.7)'"
                        :indicatorActiveColor="'#ed1c24'"
                        @clickImage="toBannerDetail"></banner>
            </div>
            <!--分类-->
            <div class="categories row-flex flex-wrap">
                <div class="category" v-for="(each, index) in categoryList" :index="index" :key="index"
                     @click="clickCategory(each)">
                    <div class="category-icon">
                        <img :src="each.imageUrl"/>
                    </div>
                    <div class="category-name">
                        {{each.name}}
                    </div>
                </div>
            </div>
            <!--分类展示-->
            <div class="category-exhibition" v-for="(each, n) in 2" :key="n" hidden>
                <div class="category-name">
                    <img class="icon" src="../../../static/image/fridge.png" mode="aspectFit"/>
                    <span class="name">冰箱</span>
                </div>
                <scroll-view class="scroll-x-view" scroll-x="true">
                    <div class="row-products-list">
                        <div class="product" v-for="(each, m) in 10" :key="m" @click="toProductDetail(each.code)">
                            <div class="product-image">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                       mode="aspectFit"/>
                            </div>
                            <div class="product-title">多门冰箱BCD-300EG-DFA-VBAW</div>
                            <div class="product-sub-title">电脑温控，ION保鲜，自动警报</div>
                            <div class="product-price">
                                <div class="present-price">
                                    <span>¥</span>
                                    <span class="price">1699</span>
                                    <!--.00-->
                                </div>
                                <div class="original-price">
                                    <span>¥</span>
                                    <span class="price">2000</span>
                                    <!--.00-->
                                </div>
                            </div>
                        </div>
                    </div>
                </scroll-view>
            </div>

            <!--限时秒杀-->
            <div class="column" hidden>
                <div class="column-title">
                    <img src="../../../static/image/xianshimiaosha.png" mode="widthFix" @click="toInstantKill"/>
                </div>
                <scroll-view class="scroll-x-view" scroll-x="true">
                    <div class="row-products-list">
                        <div class="product" v-for="(each, m) in 10" :key="m" @click="toInstantKillDetail(each)">
                            <div class="product-image">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                       mode="aspectFit"/>
                            </div>
                            <div class="product-title">多门冰箱BCD-300EG-DFA-VBAW</div>
                            <div class="product-sub-title">电脑温控，ION保鲜，自动警报</div>
                            <div class="product-price">
                                <div class="present-price">
                                    <span>¥</span>
                                    <span class="price">1699</span>
                                    <!--.00-->
                                </div>
                                <div class="original-price">
                                    <span>¥</span>
                                    <span class="price">2000</span>
                                    <!--.00-->
                                </div>
                            </div>
                        </div>
                    </div>
                </scroll-view>
            </div>

            <!--全民拼团-->
            <div class="column" hidden>
                <div class="column-title">
                    <img src="../../../static/image/quanminpintuan.png" mode="widthFix" @click="toMakeGroup"/>
                </div>
                <scroll-view class="scroll-x-view" scroll-x="true">
                    <div class="row-products-list">
                        <div class="product" v-for="(each, m) in 10" :key="m" @click="toMakeGroupDetail(each)">
                            <div class="product-image">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                       mode="aspectFit"/>
                            </div>
                            <div class="product-title">多门冰箱BCD-300EG-DFA-VBAW</div>
                            <div class="product-sub-title">电脑温控，ION保鲜，自动警报</div>
                            <div class="product-price">
                                <div class="present-price">
                                    <span>¥</span>
                                    <span class="price">1699</span>
                                    <!--.00-->
                                </div>
                                <div class="original-price">
                                    <span>¥</span>
                                    <span class="price">2000</span>
                                    <!--.00-->
                                </div>
                            </div>
                        </div>
                    </div>
                </scroll-view>
            </div>

            <!--砍价商品-->
            <div class="column" hidden>
                <div class="column-title">
                    <img src="../../../static/image/kanjiashangpin.png" mode="widthFix" @click="toBargainProduct"/>
                </div>
                <scroll-view class="scroll-x-view" scroll-x="true">
                    <div class="row-products-list">
                        <div class="product" v-for="(each, m) in 10" :key="m" @click="toBargainProductDetail(each)">
                            <div class="product-image">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800"
                                       mode="aspectFit"/>
                            </div>
                            <div class="product-title">多门冰箱BCD-300EG-DFA-VBAW</div>
                            <div class="product-sub-title">电脑温控，ION保鲜，自动警报</div>
                            <div class="product-price">
                                <div class="present-price">
                                    <span>¥</span>
                                    <span class="price">1699</span>
                                    <!--.00-->
                                </div>
                                <div class="original-price">
                                    <span>¥</span>
                                    <span class="price">2000</span>
                                    <!--.00-->
                                </div>
                            </div>
                        </div>
                    </div>
                </scroll-view>
            </div>

            <!--新品推荐-->
            <div class="column">
                <div class="column-title">
                    <img :src="newTitle" mode="widthFix"
                         @click="toNewProducts"/>
                </div>
                <scroll-view class="scroll-x-view" scroll-x="true">
                    <div class="row-products-list">
                        <div class="product" v-for="(each, m) in newProductList" :key="m"
                             @click="toProductDetail(each.code)">
                            <div class="product-image">
                                <image :src="each.mainImageUrl" mode="aspectFit"/>
                            </div>
                            <div class="product-title">{{each.title}}</div>
                            <div class="product-sub-title">{{each.subTitle}}</div>
                            <div class="product-price">
                                <div class="present-price">
                                    <span>¥</span>
                                    <span class="price">{{each.price}}</span>
                                    <!--.00-->
                                </div>
                                <div class="kb-price" v-if="userInfo.partner.isBind === 10">{{each.commissionAmount}}
                                </div>
                                <!-- 暂无原价 -->
                                <!-- <div class="original-price">
                                    <span>¥</span>
                                    <span class="price">2000</span> -->
                                <!--.00-->
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                </scroll-view>
            </div>

            <!--热卖单品-->
            <div class="column">
                <div class="column-title">
                    <img :src="hotTitle" mode="widthFix" @click="toHotSale"/>
                </div>
                <scroll-view class="scroll-x-view" scroll-x="true">
                    <div class="row-products-list">
                        <div class="product" v-for="(each, m) in hotProductList" :key="m"
                             @click="toProductDetail(each.code)">
                            <div class="product-image">
                                <image :src="each.mainImageUrl" mode="aspectFit"/>
                            </div>
                            <div class="product-title">{{each.title}}</div>
                            <div class="product-sub-title">{{each.subTitle}}</div>
                            <div class="product-price">
                                <div class="present-price">
                                    <span>¥</span>
                                    <span class="price">{{each.price}}</span>
                                    <!--.00-->
                                </div>
                                <div class="kb-price" v-if="userInfo.partner.isBind === 10">{{each.commissionAmount}}
                                </div>
                                <!-- 暂无原价 -->
                                <!-- <div class="original-price">
                                    <span>¥</span>
                                    <span class="price">2000</span>
                                    .00
                                </div> -->
                            </div>
                        </div>
                    </div>
                </scroll-view>
            </div>

            <!--人气榜单-->
            <div class="column">
                <div class="column-title">
                    <img :src="popularityTitle" mode="widthFix"/>
                </div>
                <div class="column-products-list">
                    <div class="product" v-for="(each, m) in popularityProductList" :key="m"
                         @click="toProductDetail(each.code)">
                        <div class="product-image">
                            <image :src="each.mainImageUrl" mode="aspectFit"/>
                        </div>
                        <div class="product-title">{{each.title}}</div>
                        <div class="product-sub-title">{{each.subTitle}}</div>
                        <div class="product-price">
                            <div class="present-price">
                                <span>¥</span>
                                <span class="price">{{each.price}}</span>
                                <!--.00-->
                            </div>
                            <div class="kb-price" v-if="userInfo.partner.isBind === 10">{{each.commissionAmount}}</div>
                            <!-- 暂无原价 -->
                            <!-- <div class="original-price">
                                <span>¥</span>
                                <span class="price">2000</span>
                                .00
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="no-more-list-data" v-if="popularityProductList.length > 0">
                没有更多产品
            </div>

        </div>
        <div class="page-bottom">
            <tab-nav :navKey="'index'"></tab-nav>
        </div>
    </div>
</template>

<script>
    import Basic from '@/mixins/basic.js'
    import SearchBtn from '@/components/search-btn.vue'
    import Banner from '@/components/banner.vue'
    import TabNav from '@/components/tab-nav.vue'

    export default {
      mixins: [Basic],
      components: {
        SearchBtn,
        Banner,
        TabNav
      },
      data () {
        return {
          bannerImages: [],
          categories: [
            {
              name: '卧室宝贝',
              key: 'parkTypes',
              value: 30,
              icon: require('@static/image/category_red.png'),
              routerName: 'sale'
            },
            {
              name: '客厅专享',
              key: 'parkTypes',
              value: 20,
              icon: require('@static/image/category_yellow.png'),
              routerName: 'purchase-warehousing'
            },
            {
              name: '家庭巨幕',
              key: 'parkTypes',
              value: 10,
              icon: require('@static/image/category_red.png'),
              routerName: 'purchase'
            },
            {
              name: '智能锁',
              key: 'parkTypes',
              value: 40,
              icon: require('@static/image/category_yellow.png'),
              routerName: 'sale-outgoing'
            },
            {
              name: '特价商品',
              key: 'parkTypes',
              value: 50,
              icon: require('@static/image/category_yellow.png'),
              routerName: 'sale-outgoing'
            },
            {
              name: '精品推荐',
              key: 'parkTypes',
              value: 10,
              icon: require('@static/image/category_red.png'),
              routerName: 'purchase'
            },
            {
              name: '微聊',
              key: 'parkTypes',
              value: 40,
              icon: require('@static/image/category_yellow.png'),
              routerName: 'sale-outgoing'
            },
            {
              name: '平台规划',
              key: 'parkTypes',
              value: 50,
              icon: require('@static/image/category_red.png'),
              routerName: 'sale-outgoing'
            }
          ],
          newProductList: [],
          hotProductList: [],
          popularityProductList: [],
          bannerList: [],
          categoryList: []
        }
      },
      computed: {
        popularityTitle () {
          return this.$store.state.Basic.systemSetting ? this.$store.state.Basic.systemSetting.uiIndexPopularityTitleUrl : ''
        },
        hotTitle () {
          return this.$store.state.Basic.systemSetting ? this.$store.state.Basic.systemSetting.uiIndexHotTitleUrl : ''
        },
        newTitle () {
          return this.$store.state.Basic.systemSetting ? this.$store.state.Basic.systemSetting.uiIndexNewTitleUrl : ''
        }
      },
      methods: {
        mountedNextTick () {
          let partnerCode = this.$root.$mp.query.code
          partnerCode = this.query.U ? this.query.U : partnerCode
          console.log('this.$root.$mp.query', partnerCode)
          if (partnerCode) {
            this.$store.commit('setPartnerCode', partnerCode)
          }
          let that = this
          that.$utils.justTest('index.vue')
          that.getProducts()
          that.getNewProducts()
          that.getHotProducts()
          that.getPopularityProducts()
          that.getBanner()
          that.getCategory()
        },
        getProducts () {
          let that = this
          let options = {
            url: '/product-list',
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(data => {
              // console.log(data)
            })
            .catch(err => {
              that.$utils.error(`获取产品列表失败，${err}`)
            })
        },
        //   新品推荐
        getNewProducts () {
          let that = this
          let options = {
            url: '/new-product-list',
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(res => {
              that.newProductList = res.data
            })
            .catch(err => {
              that.$utils.error(`获取失败，${err}`)
            })
        },
        //   热门单品
        getHotProducts () {
          let that = this
          let options = {
            url: '/hot-product-list',
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(res => {
              that.hotProductList = res.data
            })
            .catch(err => {
              that.$utils.error(`获取失败，${err}`)
            })
        },
        //   人气榜单
        getPopularityProducts () {
          let that = this
          let options = {
            url: '/recommend-product-list',
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(res => {
              that.popularityProductList = res.data
            })
            .catch(err => {
              that.$utils.error(`获取失败，${err}`)
            })
        },
        //   轮播图
        getBanner () {
          let that = this
          let options = {
            url: '/advertisement',
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(res => {
              that.bannerImages = res
            })
            .catch(err => {
              that.$utils.error(`获取失败，${err}`)
            })
        },
        //   分类
        getCategory () {
          let that = this
          let options = {
            url: '/sub-product-category',
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(res => {
              that.categoryList = res
            })
            .catch(err => {
              that.$utils.error(`获取失败，${err}`)
            })
        },
        toSearchPage () {
          this.$utils.navigateTo('/pages/search/main')
        },
        toBannerDetail (info) {
          console.log(info)
          if (info.connectType === 10) {
            this.$utils.navigateTo(`/pages/product-detail/main`, {productCode: info.connect})
          }
        },
        clickCategory (info) {
          let categoryCode = info.code || ''
          let categoryName = info.name || ''
          this.$utils.navigateTo('/pages/category-products/main', {
            categoryCode: categoryCode,
            categoryName: categoryName
          })
        },
        toProductDetail (code) {
          this.$utils.navigateTo(`/pages/product-detail/main`, {productCode: code})
        },
        toInstantKill () {
          this.$utils.navigateTo('/pages/instant-kill/main')
        },
        toInstantKillDetail (info) {
          this.$utils.navigateTo('/pages/instant-kill-detail/main')
        },
        toMakeGroup (info) {
          this.$utils.navigateTo('/pages/make-group/main')
        },
        toMakeGroupDetail (info) {
          this.$utils.navigateTo('/pages/make-group-detail/main')
        },
        toBargainProduct (info) {
          this.$utils.navigateTo('/pages/bargain-product/main')
        },
        toBargainProductDetail (info) {
          this.$utils.navigateTo('/pages/bargain-product-detail/main')
        },
        toNewProducts () {
          this.$utils.navigateTo('/pages/new-products/main')
        },
        toHotSale () {
          this.$utils.navigateTo('/pages/hot-sale/main')
        }
      }
    }
</script>

<style lang="scss" scoped>
    .kb-price {
        background: #CF9154;
        color: #ffffff;
        display: inline-block;
        vertical-align: baseline;
        margin-left: 5px;
        padding: 0 3px;
        font-size: 22px;
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

    /*分类图标*/
    .categories {
        @extend .row-flex;
        @extend .flex-wrap;
        padding: 40px 20px 0;
        box-sizing: border-box;
        background-color: $white;
        z-index: 5;
    }

    .category {
        @extend .column-flex;
        @extend .align-item-center;
        flex-basis: 20%;
        padding: 0 0 30px 0;
    }

    .category-icon {
        position: relative;
        width: 105px;
        height: 105px;
    }

    .category-icon > img {
        width: 100%;
        height: 100%;
    }

    .category-name {
        color: #565656;
        font-size: 24px;
        text-align: center;
        padding-top: 10px;
    }

    /*分类展示*/
    .category-exhibition {
        @extend .white-bg;
        /*margin-top: 20px;*/
        /*border-top: 20px solid transparent;*/
        border-top: 20px solid #f7f7f7;
    }

    .category-exhibition .category-name {
        @extend .row-flex;
        @extend .align-item-center;
        padding: 32px 34px 0 34px;
        box-sizing: border-box;
    }

    .category-exhibition .category-name .icon {
        max-width: 40px;
        max-height: 40px;
        margin-right: 13px;
    }

    .category-exhibition .category-name .name {
        color: $deep_black;
        font-size: 36px;
    }

    .row-products-list {
        background-color: #ffffff;
    }

    /*栏目*/
    .column-title {
        line-height: 0;
    }

    .column-title image {
        width: 750px;
        height: 96px;
    }

    .column-products-list {
        background: #ffffff;
    }

    .column-products-list .product-image {
    }
</style>
