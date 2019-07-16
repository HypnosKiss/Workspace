<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
            <!--搜索框-->
            <search-btn :placeholder="'搜索商品'" @click="toSearchPage"></search-btn>
            <tab-sort @change="changeSortType"></tab-sort>
            <div class="column-products-list">
                <div class="product" v-for="(each, m) in productList" @click="toProductDetail(each)" :key="m">
                    <div class="product-image">
                        <image :src="each.mainImageUrl" mode="aspectFit" />
                    </div>
                    <div class="product-title">{{each.title}}</div>
                    <div class="product-sub-title">{{each.subTitle}}</div>
                    <div class="product-price">
                        <div class="present-price">
                            <span>¥</span>
                            <span class="price">{{each.mainPrice}}</span>
                            <span class="dot-price">{{ each.pointPrice }}</span>
                        </div>
                        <!--<div class="original-price">-->
                            <!--<span>¥</span>-->
                            <!--<span class="price">{{each.title}}</span>-->
                            <!--&lt;!&ndash;.00&ndash;&gt;-->
                        <!--</div>-->
                    </div>
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
  import SearchBtn from '@/components/search-btn.vue'
  import TabSort from '@/components/tab-sort.vue'

  export default {
    mixins: [Basic, List],
    components: {
      SearchBtn,
      TabSort
    },
    data () {
      return {
        getListPath: '/product-list'
      }
    },
    computed: {
      productList () {
        return this.list.map(function (line) {
          line.mainPrice = Number(line.price).toFixed(0)
          line.pointPrice = Number(line.price).toFixed(2).substr(line.mainPrice.length)
          return line
        })
      }
    },
    methods: {
      mountedNextTick () {
        wx.setNavigationBarTitle({
          title: this.query.categoryName
        })
        this.defaultList()
        this.startLoading()
      },
      defaultKeywords () {
        console.log('this.query====>', this)
        console.log('this.query====>', this.query)
        return {
          'product_category_code': this.query.categoryCode || ''
        }
      },
      toSearchPage () {
        this.$utils.navigateTo('/pages/search/main')
      },
      changeSortType (key, type) {
        console.log('key====>', key)
        console.log('type====>', type)
      },
      toProductDetail (info) {
        let code = info.code || ''
        this.$utils.navigateTo('/pages/product-detail/main', {
          productCode: code
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
        width: 100%;
        height: 100%;
        padding-bottom: 0;
        box-sizing: border-box;
    }
    .iphone-x {
        .page-main {
            padding-bottom: 68px;
        }
    }
    .page-main::-webkit-scrollbar {
        width: 0;
        height: 0;
        background-color: transparent;
    }
    .column-products-list .product-image {
        background-color: #ebedf0;
    }
</style>
