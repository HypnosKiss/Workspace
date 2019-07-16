<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="search">
                <div class="search-area">
                    <i class="icon iconfont icon-sousuo- fixed-flex-item"></i>
                    <input class="elastic-flex-item search-keyword" :focus="true" v-model="keyword" @confirm="toSearchResult" />
                    <i class="icon iconfont icon-shanchu1 fixed-flex-item" @click="cleanKeyword" v-if="keyword"></i>
                </div>
                <div class="search-start" @click="toSearchResult">搜索</div>
            </div>
            <div class="shortcut">
                <div class="group">
                    <div class="group-top">
                        <span class="group-name">热门搜索</span>
                    </div>
                    <div class="keyword-list">
                        <div class="keyword-item" v-for="(each, n) in hots" :key="n" @click="setKeyword(each.content)">{{each.content}}</div>
                    </div>
                </div>
                <div class="group" v-if="histories.length !== 0">
                    <div class="group-top">
                        <span class="group-name">搜索历史</span>
                        <span class="group-operate" @click="clearHistory">清除</span>
                    </div>
                    <div class="keyword-list">
                        <div class="keyword-item" v-for="(each, n) in histories" :key="n" @click="setKeyword(each.content)">{{each.content}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    data () {
      return {
        keyword: '',
        hots: [],
        histories: []
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.getUserRecord()
        that.getHotRecord()
      },
      cleanKeyword () {
        this.keyword = ''
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
      setKeyword (keyword) {
        this.keyword = keyword
        this.toSearchResult()
      },
      //   用户搜索记录
      getUserRecord () {
        let that = this
        let options = {
          url: '/search-record',
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(res => {
            that.histories = res
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      },
      //   热门搜索记录
      getHotRecord () {
        let that = this
        let options = {
          url: '/popular-search-record',
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(res => {
            that.hots = res
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      },
      //   清楚搜索记录
      clearHistory () {
        let that = this
        let options = {
          url: '/search-record',
          method: 'delete'
        }
        that.$utils.requestServer(options)
          .then(res => {
            that.getUserRecord()
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
    /*关键字输入*/
    .search {
        @extend .row-flex;
        @extend .align-item-center;
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
    .icon-shanchu1 {
        font-size: 28px;
        color: #999999;
        padding-left: 15px;
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

    /*关键字推荐区*/
    .shortcut {
        padding: 0 30px;
        box-sizing: border-box;
    }
    .group {
        padding-bottom: 20px;
    }
    .group-top {
        @extend .row-flex;
        @extend .align-item-center;
        color: $black;;
        font-size: 26px;
        padding: 27px 0;
    }
    .group-name {
        @extend .elastic-flex-item;
    }
    .group-operate {
        @extend .fixed-flex-item;
        color: $red;
        font-size: 26px;
    }
    .keyword-list {
        padding: 16px 0 0 0;
    }
    .keyword-list .keyword-item {
        color: $black;;
        font-size: 24px;
        padding: 12px 18px;
        margin: 0 20px 24px 0;
        background-color: #f0f0f0;
        border-radius: 4px;
        display: inline-block;
    }
</style>
