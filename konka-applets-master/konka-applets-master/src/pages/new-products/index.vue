<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
            <div class="column-products-list">
                <div class="product" v-for="(each, n) in list" @click="toProductDetail(each)">
                    <div class="product-image">
                        <image :src="each.mainImageUrl" mode="aspectFit" />
                    </div>
                    <div class="product-title">{{each.title}}</div>
                    <div class="product-sub-title">{{each.subTitle}}</div>
                    <div class="product-price">
                        <div class="present-price">
                            <span>¥</span>
                            <span class="price">{{each.price}}</span>
                            <!--.00-->
                        </div>
                        <!--<div class="original-price">-->
                        <!--<span>¥</span>-->
                        <!--<span class="price">2000</span>-->
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
          getListPath: '/new-product-list'
        }
      },
      methods: {
        mountedNextTick () {
          let that = this
          that.startLoading()
        },
        toProductDetail (info) {
          this.$utils.navigateTo('/pages/product-detail/main', {
            productCode: info.code
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
    }
    .iphone-x {
        .page-main {
            padding-bottom: 68px;
        }
    }
    .column-products-list .product-image {
        background-color: #ebedf0;
    }
</style>
