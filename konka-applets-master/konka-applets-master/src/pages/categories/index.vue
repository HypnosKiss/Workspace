<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <!--搜索框-->
            <div class="search fixed-flex-item">
                <div class="search-area">
                    <i class="icon iconfont icon-sousuo- fixed-flex-item"></i>
                    <input class="elastic-flex-item search-keyword" v-model="keyword" @confirm="toSearchResult" />
                </div>
                <div class="search-start" @click="toSearchResult">搜索</div>
            </div>

            <div class="elastic-flex-item position-relative row-flex align-item-stretch">
                <div class="left-box">
                    <scroll-view class="position-relative occupy-all" scroll-y="true">
                        <div class="parent-list">
                            <div v-for="(each, n) in categories" :key="n" :class="['parent-category', {'active': each.id === activeParentCode}]" @click="changeParent(each.id)">
                                {{each.name}}
                            </div>
                        </div>
                    </scroll-view>
                </div>
                <div class="right-box">
                    <scroll-view class="position-relative occupy-all" scroll-y="true">
                        <div class="children-list">
                            <div v-for="(each, n) in childCategories" :key="n" class="child-category" @click="toCategory(each)">
                                <div class="image">
                                    <img :src="each.imageUrl || 'https://www.konka.com/public/images/ec/93/09/c0fe7650c9b73b8a7fda75112670bf77020541f3.jpg?74802_OW800_OH800'"/>
                                </div>
                                <div class="name">{{each.name}}</div>
                            </div>
                        </div>
                    </scroll-view>
                </div>
            </div>
        </div>
        <div class="page-bottom">
            <tab-nav :navKey="'categories'"></tab-nav>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import TabNav from '@/components/tab-nav.vue'

  export default {
    mixins: [Basic],
    components: {
      TabNav
    },
    data () {
      return {
        keyword: '',
        activeParentCode: '',
        categories: []
      }
    },
    computed: {
      childCategories () {
        let result = []
        let categories = this.categories
        for (let n = 0; n < categories.length; n++) {
          if (categories[n].id === this.activeParentCode) {
            result = categories[n].subCategory
            break
          }
        }
        return result
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.getCategories()
      },
      getCategories () {
        let that = this
        let options = {
          url: '/product-category-list',
          method: 'get'
        }
        that.$utils.showLoading()
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.categories = data
            that.activeParentCode = data.length > 0 ? data[0].id : ''
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`获取失败，${err}`)
          })
      },
      toSearchResult () {
        if (!this.keyword) {
          this.$utils.showModal('请输入搜索关键字')
          return
        }
        this.$utils.navigateTo('/pages/search-result/main', {
          keyword: this.keyword
        })
      },
      toCategory (info) {
        let categoryCode = info.code || ''
        let categoryName = info.name || ''
        this.$utils.navigateTo('/pages/category-products/main', {
          categoryCode: categoryCode,
          categoryName: categoryName
        })
      },
      changeParent (code = '') {
        this.activeParentCode = code
      }
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

    /*关键字输入*/
    .search {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;
        padding: 20px 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .search-area {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .elastic-flex-item;
        height: 56px;
        padding: 0 30px;
        border-radius: 28px;
        margin-right: 35px;
        text-align: center;
        box-sizing: border-box;
        background-color: #f2f2f2;
    }
    .icon-sousuo- {
        font-size: 28px;
        color: #999999;
        padding-right: 15px;
    }
    .search-keyword {
        font-size: 24px;
        color: #999999;
        text-align: left;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .search-start {
        @extend .fixed-flex-item;
        color: $deep_black;
        font-size: 28px;
    }

    /*主要区域*/
    .left-box {
        @extend .fixed-flex-item;
        @extend .position-relative;

        width: 190px;
        background-color: #fafafa;
    }
    .right-box {
        @extend .elastic-flex-item;
        @extend .position-relative;

        background-color: #ffffff;
    }

    .parent-category {
        height: 104px;
        line-height: 104px;
        font-size: 26px;
        text-align: center;
        color: $deep_black;
        border-left: 4px solid transparent;
        transition: all 0.1s linear;
    }
    .parent-category.active {
        border-left: 4px solid $theme_color;
        color: $theme_color;
        background-color: #ffffff;
    }

    .children-list {
        @extend .row-flex;
        @extend .flex-wrap;
        /*@extend .justify-around;*/

        padding: 20px;
        box-sizing: border-box;
    }
    .child-category {
        width: calc(100% / 3);
        margin-bottom: 20px;
        /*width: 160px;*/
        height: 220px;

        .image {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 0 auto;
            overflow: hidden;
        }
        .image image {
            position: absolute;
            top: 50%;
            left: 50%;
            max-width: 100%;
            max-height: 100%;
            transform: translate(-50%, -50%);
        }
        .name {
            width: 100%;
            height: 60px;
            line-height: 60px;
            text-align: center;
            font-size: 24px;
            color: $deep_black;
        }
    }
</style>
