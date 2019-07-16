<template>
  <div :class="['page', {'iphone-x': isIpx}]">
      <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
        <!--搜索框-->
        <search-btn @click="$store.commit('setSearchStatus', true)"></search-btn>
        <div class="distribution-box">
          <div class="box-oder">
            <div class="order-total">
                <i :class="['icon', 'iconfont', 'iconpig']" :style="{'background-color': '#fff'}"></i>
              <span>累计收益K币</span>
            </div>
            <div class="order-price">
              {{ total.totalKMoney || 0 }}
            </div>
            <div class="box-quota">
              <div class="quota-item">
                <div class="item-num">{{ total.availableWithdrawKMoney || 0 }}</div>
                <div class="item-txt">可提现收益</div>
              </div>
              <div class="quota-item">
                <div class="item-num">{{ total.hasWithdrawKMoney || 0 }}</div>
                <div class="item-txt">已提现收益</div>
              </div>
              <div class="quota-item">
                <div class="item-num">{{ total.waitSettlementKMoney || 0 }}</div>
                <div class="item-txt">未结算收益</div>
              </div>
            </div>
          </div>
        </div>
        <tab :isK="true" :tabs="tabs" :defaultTab="keywords.status" @change="changeTab"></tab>

        <!-- 数据展示 -->
        <div class="list-wrap">
          <div class="item border-b" v-for="(each, n) of showList" :key="n">
            <div class="left">
              <span>订单号：{{ each.orderCode }}</span>
              <span>收益：{{ each.integral }} k币</span>
            </div>
            <div class="right">
              <span :class="{isGreen: each.statusText === '已结算'}">{{ each.statusText }}</span>
              <span>{{ each.createdAt }}</span>
            </div>
          </div>
        </div>

        <div class="no-list-data" v-if="noSearchResult">
            没有找到相关数据
        </div>
        <div class="no-more-list-data" v-if="!noSearchResult && noMoreListData">
            已无更多数据
        </div>
        <div style="height: 20px;"></div>
    </scroll-view>
    <search-by-wzh @modalSearch="handleModalSearch"></search-by-wzh>
  </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import List from '@/mixins/list.js'
  import SearchBtn from '@/components/search-btn.vue'
  import Tab from '@/components/tab.vue'
  import SearchByWzh from '@/components/search-by-wzh.vue'

  export default {
    mixins: [Basic, List],
    components: {
      SearchBtn,
      Tab,
      SearchByWzh
    },
    data () {
      return {
        // getListPath: '/order',
        getListPath: '/partner/sale-commission-records',
        // 10待结算，20已结算，传空则为全部|
        tabs: [
          {
            key: '',
            name: '全部'
          },
          {
            key: 10,
            name: '未结算'
          },
          {
            key: 20,
            name: '已结算'
          }
        ],
        total: {}
      }
    },
    computed: {
      showList () {
        let list = this.$utils.cloneObject(this.list)
        // console.log('showList', list)
        return list
      }
    },
    mounted () {
      // 获取累计统计数据
      this.getTotalData()
    },
    methods: {
      mountedNextTick () {
        this.defaultList()
        this.startLoading()
      },
      async getTotalData () {
        const options = {
          url: '/partner/sale-commission-record-total'
        }
        try {
          this.$utils.showLoading()
          const data = await this.$utils.requestServer(options)
          this.total = data
          this.$utils.hideLoading()
        } catch (error) {
          this.$utils.error(`获取失败，${error}`)
          this.$utils.hideLoading()
        }
      },
      defaultKeywords () {
        console.log('defaultKeywords', this.query)
        return {
          status: this.query.status || '',
          time: this.query.time || []
        }
      },
      searchOrder () {
      },
      changeTab (val) {
        this.keywords.status = val
        this.changeKeyword()
      },
      // 搜索modal
      handleModalSearch (params) {
        console.log('modal', params)
        const time = [params.dateStart, params.dateEnd]
        this.changeKeyword({ ...this.query, time })
        this.$store.commit('setSearchStatus', false)
      }
    },
    // 动态修改标题
    onLoad (options) {
      console.log('options', options, this.$utils.pathQueryToStr({status: 20}))
      if (this.$utils.pathQueryToStr({status: 20}).includes(options.queryEncodeStr)) {
        wx.setNavigationBarTitle({
          title: '已结算K币',
          success: function (res) {
            // success
          }
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color:#f7f7f7;
        height: 100vh;
        padding-bottom: 0;
    }
    .distribution-box{
      padding: 20px 30px;
      color: #fff;
      font-family: PingFang-SC-Heavy;
      .box-oder{
        padding: 40px 0;
        background-image: linear-gradient(45deg, 
          #ff7690 0%, 
          #ff5960 100%), 
        linear-gradient(45deg, 
          #f29b69 0%, 
          #ed6a69 50%, 
          #e73d68 100%, 
          #e75268 100%, 
          #e76668 100%);
        background-blend-mode: normal,normal;
        border-radius: 20px;
        .order-total{
          text-align: center;
          height: 45px;
          .iconfont {
            vertical-align: middle;
            font-size: 50px;
            /*background-image: -webkit-gradient(linear, left top, right bottom, from(#ff7a90), to(#f56271));*/
            -webkit-background-clip: text; /*必需加前缀 -webkit- 才支持这个text值 */
            -webkit-text-fill-color: transparent; /*text-fill-color会覆盖color所定义的字体颜色： */
          }
          span {
            color: #fff;
            font-size: 28px;
            margin-left: 20px;
          }
        }
        .order-price{
          margin:20px 0 34px 0;
          font-size: 60px;
          color: #fff;
          text-align: center;
        }
        .box-quota{
          display: flex;
          .quota-item{
            flex: 1;
            text-align: center;
            .item-num{
              font-size: 30px;
              font-weight: normal;
              font-stretch: normal;
            }
            .item-txt{
              font-size: 24px;
              margin-top: 10px;
              letter-spacing: 2px;
            }
          }
        }
      }
    }
    .list-wrap {
      padding: 0 30px;
      .item {
        display: flex;
        justify-content: space-between;
        div {
          display: flex;
          flex-direction: column;
          font-size: 28px;
          padding: 30px 0 24px 0;
          line-height: 1;
          span:last-child {
            margin-top: 14px;
          }
          span.isGreen {
            color: #067a21 !important;
          }
        }
        div.left {
          span {
            color: #333333;
          }
        }
        div.right {
          span {
            color: #999999;
          }
          span:last-child {
            font-size: 22px;
          }
        }
      }
      .border-b:after {
        background-color: #e6e6e6;
      }
    }
</style>
