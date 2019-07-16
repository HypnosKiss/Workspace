<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="column-products-list">
                <div class="product" v-for="(each, m) in 10" @click="toProductDetail(each)" :key='m'>
                    <div class="product-image">
                        <image src="https://www.konka.com/public/images/1e/bc/e9/41e327ffe4d0413cac1bbd0efc81d481aec86501.jpg?05055_OW280_OH180" mode="aspectFit" />
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
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import SearchBtn from '@/components/search-btn.vue'
  import TabSort from '@/components/tab-sort.vue'

  export default {
    mixins: [Basic],
    components: {
      SearchBtn,
      TabSort
    },
    data () {
      return {
      }
    },
    methods: {
      mountedNextTick () {
      },
      toProductDetail (info) {
        this.$utils.navigateTo('/pages/product-detail/main')
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }
    .column-products-list .product-image {
        background-color: #ebedf0;
    }
</style>
