<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="list-header fixed-flex-item">
                <div class="header-row">
                    <div class="row-item font-light-black">合伙人</div>
                    <div class="row-item font-light-black">手机号</div>
                    <div class="row-item font-light-black">订单数</div>
                    <div class="row-item font-light-black">佣金</div>
                </div>
            </div>
            <div class="list-data elastic-flex-item position-relative">
                <scroll-view :class="['position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
                    <div class="data-row" v-for="(each, n) in list" :key="n">
                        <div class="row-item font-black">{{each.name}}</div>
                        <div class="row-item font-black">{{each.phone}}</div>
                        <div class="row-item font-blue underline" @click="goToList(each.code, each.name)">{{each.orderNumber}}</div>
                        <div class="row-item font-red">
                            <span>¥</span>
                            <span>{{each.orderMoney}}</span>
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
        </div>
    </div>
</template>

<script>
    import Basic from '@/mixins/basic.js'
    import List from '@/mixins/list.js'

    export default {
      mixins: [Basic, List],
      components: {
      },
      onLoad (options) {
      },
      data () {
        return {
          getListPath: '/partner/distribution-order/two-level'
        }
      },
      computed: {
      },
      methods: {
        mountedNextTick () {
          let that = this
          that.defaultList()
          that.startLoading()
        },
        goToList (code, name) {
          this.$utils.navigateTo('/pages/partner/orders-details-list/main', {
            code: code,
            name: name
          })
        }
      }
    }
</script>

<style lang="scss" scoped>
    .underline {
        text-decoration: underline;
    }
    .page-main {
        @extend .column-flex;

        width: 100%;
        height: 100%;
        padding-bottom: 0;
        box-sizing: border-box;
        /*overflow-x: auto;*/
    }
    .iphone-x {
        .page-main {
            padding-bottom: 68px;
        }
    }
    .list-header {
        @extend .fixed-flex-item;
        @extend .border-bottom;

        width: max-content;
        /*padding-left: 30px;*/
        background-color: #ffffff;
        white-space: nowrap;
    }
    .list-data {
        @extend .elastic-flex-item;

        margin-top: 20px;
        width: max-content;
        height: max-content;
        /*padding-left: 30px;*/
        background-color: #ffffff;
        /*overflow-y: auto;*/
        overflow: hidden;
    }
    .header-row, .data-row {
        @extend .border-bottom;

        padding-top: 30px;
        padding-bottom: 30px;
    }
    .row-item {
        height: 40px;
        line-height: 40px;
        font-size: 28px;
        border-right: 1px solid #cccccc;
        text-align: center;
        white-space: nowrap;
        display: inline-block;
        box-sizing: border-box;
    }
    .row-item:nth-of-type(1) {
        width: 200px;
    }
    .row-item:nth-of-type(2) {
        width: 250px;
    }
    .row-item:nth-of-type(3) {
        width: 200px;
    }
    .row-item:nth-of-type(4) {
        width: 250px;
        border-right: 0;
    }
</style>
