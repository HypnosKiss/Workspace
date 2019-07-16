<template>
    <div :class="['page', {'iphone-x': isIpx}]">
         <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
            <tab :tabs="tabs" :defaultTab="activeTab" @change="changeTab"></tab>
            <div class="search">
                <i class="icon iconfont icon-rili"></i>
                <div class="picker">
                    <picker
                            mode="date"
                            start="2018-01-01"
                            :end="keywords.created_at[1]"
                            @change="changeStartDate">
                        <view class="picker">{{keywords.created_at[0]}}</view>
                    </picker>
                </div>
                <span class="to">~</span>
                <div class="picker">
                    <picker
                            mode="date"
                            :start="keywords.created_at[0]"
                            @change="changeEndDate">
                        <view class="picker">{{keywords.created_at[1]}}</view>
                    </picker>
                </div>
                <i class="icon iconfont icon-xia"></i>
            </div>

            <div class="list">
                <div v-for="(each, n) in list" :key="n" :class="['info-line', status[each.status]]">
                    <div class="elastic-flex-item column-flex">
                        <div class="price">
                            <span>¥</span>
                            <span>{{ each.price }}</span>
                        </div>
                        <div class="date">
                            <span>{{ each.updatedAt }}</span>
                        </div>
                    </div>
                    <div class="fixed-flex-item">
                        <span class="status">{{each.statusText}}</span>
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
  import Tab from '@/components/tab.vue'

  export default {
    mixins: [Basic, List],
    components: {
      Tab
    },
    data () {
      return {
        getListPath: '/partner/withdraw',
        activeTab: 'waiting',
        tabs: [
          {
            key: 'waiting',
            name: '提现中'
          },
          {
            key: 'success',
            name: '提现成功'
          },
          {
            key: 'fail',
            name: '提现失败'
          }
        ],
        status: {
          10: 'waiting',
          50: 'success',
          40: 'fail'
        },
        keywords: {
          created_at: ['2018-01-01', '2019-12-31'],
          keywords: 10
        }
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.defaultList()
        that.startLoading()
      },
      defaultKeywords () {
        return {
          created_at: ['2018-01-01', '2019-12-31'],
          status: 10
        }
      },
      changeStartDate (e) {
        let value = this.$utils.getValue(e)
        console.log(value)
        let keywords = this.$utils.cloneObject(this.keywords)
        keywords.created_at[0] = value
        this.keywords = keywords
        this.changeKeyword()
      },
      changeEndDate (e) {
        let value = this.$utils.getValue(e)
        console.log(value)
        let keywords = this.$utils.cloneObject(this.keywords)
        keywords.created_at[1] = value
        this.keywords = keywords
        this.changeKeyword()
      },
      toSearchPage () {
        this.$utils.navigateTo('/pages/search/main')
      },
      changeTab (key) {
        // console.log('key====>', key)
        this.keywords.status = key
        this.changeKeyword()
      },
      toProductDetail () {
        this.$utils.navigateTo('/pages/instant-kill-detail/main')
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }
    .page-main {
        height: 100%;
        padding-bottom: 0;
    }
    .iphone-x {
        .page-main {
            padding-bottom: 68px;
        }
    }
    .search {
        @extend .row-flex;
        @extend .align-item-center;

        font-size: 28px;
        color: $black;
        padding: 15px 30px;
        box-sizing: border-box;

        .icon-rili {
            @extend .fixed-flex-item;
            font-size: 50px;
        }
        .picker {
            @extend .fixed-flex-item;
            margin: 0 10px;
        }
        .to {
            @extend .fixed-flex-item;
        }
        .icon-xia {
            @extend .fixed-flex-item;
            font-size: 20px;
        }
    }
    .list {
        padding-left: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .info-line {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        height: 120px;
        padding-right: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .info-line:nth-last-of-type(1) {
        border-bottom: 0;
    }
    .price {
        font-size: 30px;
        color: $deep_black;
        margin-bottom: 5px;
    }
    .date {
        font-size: 22px;
        color: $light_black;
    }
    .status {
        font-size: 28px;
        color: $light_black;
    }
    .waiting .status {
        color: $light_black;
    }
    .success .status {
        color: $green;
    }
    .fail .status {
        color: $red;
    }
</style>
