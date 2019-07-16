<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="total">
                共{{ total }}人
            </div>

            <div class="member-list">
                <div class="member" v-for="(each, n) in list" :key="n" @click="goToTeam(each.code, each.name)">
                    <div class="user-header">
                        <image :src="each.avatar" />
                    </div>
                    <div class="name">{{ each.name }}</div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    components: {
    },
    onLoad (options) {
      wx.setNavigationBarTitle({
        title: this.query.name
      })
    },
    data () {
      return {
        list: [],
        total: 0
      }
    },
    computed: {
    },
    methods: {
      mountedNextTick () {
        let that = this
        let options = {
          url: `/partner/${that.query.code}/team`,
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log('oo=>', data)
            that.list = data
            that.total = data.length
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .member-list {
        background-color: #ffffff;
    }
    .total {
        height: 96px;
        line-height: 96px;
        padding: 0 30px;
        box-sizing: border-box;
        font-size: 28px;
        color: $black;
    }
    .member-list {
        padding-left: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .member {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        height: 140px;
        padding-right: 30px;
        box-sizing: border-box;

        .user-header {
            @extend .fixed-flex-item;
            margin-right: 20px;
            font-size: 30px;
            color: $black;
        }
        .name {
            @extend .elastic-flex-item;
            margin-right: 20px;
        }
    }
    .member:nth-last-of-type(1) {
        border-bottom: 0;
    }
</style>
