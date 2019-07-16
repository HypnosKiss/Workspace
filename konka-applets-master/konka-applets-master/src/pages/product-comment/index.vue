<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <scroll-view :class="['page-main','comment-list','position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
                <div class="comment" v-for="(each,n) in list" :key="n">
                    <div class="user-info">
                        <div class="user-header">
                            <image :src="each.avatar || defaultUserHeader" />
                        </div>
                        <div class="user-nickname">
                            {{each.nickname || '用户'}}
                        </div>
                        <div class="create-date">
                            {{each.updatedAt}}
                        </div>
                    </div>
                    <div class="row-flex align-item-center">
                        <div class="specifications">
                            <span class="item">{{each.specificationText}}</span>
                        </div>
                        <div class="achievement">
                            <i class="icon iconfont icon-pingjia-xuanzhong- active" v-for="(m,a) in each.rate" :key="a"></i>
                            <i class="icon iconfont icon-pingjia-xuanzhong-" v-for="(m,b) in (5 - each.rate)" :key="b"></i>
                        </div>
                    </div>
                    <div class="comment-content">
                        {{each.content}}
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
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import List from '@/mixins/list.js'
  export default {
    mixins: [Basic, List],
    components: {
    },
    data () {
      return {
        commentLists: [],
        getListPath: ''
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.getListPath = `/evaluation/${this.query.productCode}`
        that.defaultList()
        that.startLoading()
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
    .comment {
        padding: 0 30px;
        margin-bottom: 20px;
        background-color: #ffffff;
    }
</style>
