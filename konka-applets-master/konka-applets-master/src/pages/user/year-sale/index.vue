<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
            <!--搜索框-->
          <search-btn placeholder="搜索时间" @click="$store.commit('setSearchStatus', true)"></search-btn>
          <div class="sale-box">
            <div class="box-quota">
              <div class="quota-item quota-box">
                <div class="item-num">{{ saleTotal.yearSales }}</div>
                <div class="item-txt">我的年销售额</div>
              </div>
              <div class="quota-item">
                  <div class="item-num">{{ saleTotal.yearSubSales }}</div>
                <div class="item-txt">分销年销售额</div>
              </div>
            </div>
          </div>
          <div class="sale-list-header">
            <div class="sale-header-row">
                <div class="sale-row-item">我的销售额</div>
                <div class="sale-row-item">分销销售额</div>
                <div class="sale-row-item">时间</div>
            </div>
            <div class="sale-list-data">
              <div class="sale-list-row" v-for="(each, m) in saleData" :key="m">
                <div class="sale-row-item font-red">{{ each.mySales }}</div>
                <div class="sale-row-item font-blue">{{ each.subSales }}</div>
                <div class="sale-row-item font-black">{{ each.time }}</div>
              </div>
            </div>
            <div class="no-list-data" v-if="noSearchResult">
                没有找到相关数据
            </div>
            <div class="no-more-list-data" v-if="!noSearchResult && noMoreListData">
                已无更多数据
            </div>
          </div>
        </scroll-view>
        <!-- slot="year" -->
        <div :class="['popup-box', 'scale-in', {'active': getSearchStatus}]" @touchmove.stop="() => {}">
            <div class="popup-bg" @click="closePopupBox"></div>
            <div class="popup-content">
                <div class="drawer-box">
                    <div class="drawer-title">{{title}}</div>
                    <div class="drawer-content">
                        <label class="lbl">开始时间</label>
                        <div class="d-picker">
                            <picker mode="date" :value="dateStart" fields='year' :end="today" @change="bindDateChange">
                                <div class="picker">{{ dateStart }}</div>
                            </picker>
                        </div>
                    </div>
                    <div class="drawer-content">
                        <label class="lbl">结束时间</label>
                        <div class="d-picker">
                            <picker mode="date" :value="dateEnd" fields='year' :start="dateStart" :end="today" @change="bindDateChange2">
                                <view class="picker">
                                    {{dateEnd}}
                                </view>
                            </picker>
                        </div>
                    </div>
                    <div class="btn-box">
                        <div class="btn cancel" @click="closePopupBox">取消</div>
                        <div class="btn ok" @click="getSale">搜索</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import SearchBtn from '@/components/search-btn.vue'
  import UserSearch from '@/components/user-search.vue'
  import { mapGetters } from 'vuex'

  export default {
    mixins: [Basic],
    components: {
      SearchBtn,
      UserSearch
    },
    data () {
      return {
        saleTotal: {},
        saleData: [],
        title: '查询时间',
        dateStart: '',
        dateEnd: '',
        time: []
      }
    },
    computed: {
      ...mapGetters([
        'getSearchStatus'
      ]),
      today () {
        return new Date()
      }
    },
    methods: {
      mountedNextTick () {
        console.log('enter')
        this.getSaleTotal()
        this.getSaleList()
        this.defaultList()
        this.startLoading()
      },
      // 获取销售额
      async getSaleList () {
        let that = this
        const time = {
          time: this.time
        }
        let options = {
          url: '/partner/sales',
          method: 'get',
          headers: {
            'X-Total-Type': 20,
            'X-Search-Keywords': that.$utils.base64Encode(JSON.stringify(time))
          }
        }
        console.log('options===>', options)
        try {
          this.$utils.showLoading()
          const data = await this.$utils.requestServer(options)
          this.saleData = data
          this.$utils.hideLoading()
        } catch (error) {
          this.$utils.error(`获取失败，${error}`)
          this.$utils.hideLoading()
        }
      },
      // 销售额统计
      async getSaleTotal () {
        let options = {
          url: '/partner/sales-total',
          method: 'get'
        }
        try {
          this.$utils.showLoading()
          const data = await this.$utils.requestServer(options)
          this.saleTotal = data
          this.$utils.hideLoading()
        } catch (error) {
          this.$utils.error(`获取失败，${error}`)
          this.$utils.hideLoading()
        }
      },
      getSale () {
        let start = this.dateStart
        let end = this.dateEnd
        if (!start) {
          this.$utils.showToast('开始时间不能为空')
          return
        }
        if (!end) {
          this.$utils.showToast('结束时间不能为空')
          return
        }
        if (end < start) {
          this.$utils.showToast('结束日期必须大于开始日期')
          return
        }
        this.time = [start, end]
        console.log(this.time)
        this.getSaleList()
        this.$store.commit('setSearchStatus', false)
      },
      // 日期更改
      bindDateChange (e) {
        console.log('picker发送选择改变，携带值为', e.mp.detail.value)
        this.dateStart = e.mp.detail.value
      },
      bindDateChange2 (e) {
        this.dateEnd = e.mp.detail.value
      },
      closePopupBox () {
        this.$store.commit('setSearchStatus', false)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color:#f7f7f7;
        font-family: PingFang-SC-Heavy;
    }
    .sale-box{
      padding: 20px 30px;
      color: #fff;
      .box-quota{
        display: flex;
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
        .quota-box{
          position: relative;
          &::before{
            content:'';
            width: 1px;
            height: 58px;
            display: inline-block;
            position: absolute;
            background-color: #fff;
            right: 0px;
            top: 140px;
          }
        }
        .quota-item{
          flex: 1;
          padding: 100px 0;
          text-align: center;
          .item-num{
            font-size: 60px;
            font-weight: normal;
            font-stretch: normal;
          }
          .item-txt{
            font-size: 28px;
            margin-top: 20px;
            letter-spacing: 2px;
          }
        }
      }
    }
    .sale-list-header{
      background-color: #fff;
      font-size: 32px;
      .sale-header-row{
        border-top: 1px solid #e6e6e6;
        display: flex;
        padding: 36px 0;
        color: #666;
        text-align: center;
        .sale-row-item{
          flex: 1;

        }
      }
      .sale-list-data{
        border-top: 1px solid #e6e6e6;
        border-bottom: 1px solid #e6e6e6;
        font-size: 30px;
        padding-left: 30px;
        .sale-list-row{
          display: flex;
          text-align: center;
           border-bottom: 1px solid #e6e6e6;
           padding: 36px 0;
           &:last-child{
              border-bottom: none;
            }
          .sale-row-item{
            flex: 1;

          }
        }
      }
    }
    .drawer-box{
        border-radius: 8px;
        width: 650px;
        background: #ffffff;
        .drawer-title{
            letter-spacing: 4px;
            padding: 63px 0 20px 0;
            font-weight: bold;
            font-size: 36px;
            color: #000;
            text-align: center;
        }
        .drawer-content{
            margin: 30px 55px;
            .lbl{
                font-size: 30px;

                color: #999;
            }
            .d-picker{
                margin-top: 15px;
                padding: 20px;
                border: solid 1px #d2d3d5;
                .picker{
                    color: #333;
                    text-align: center;
                }
            }
        }
        .btn-box{
            display: flex;
            border-top: 1px solid #E8E8EA;
            .btn{
                flex: 1;
                font: 20px "microsoft yahei";
                font-size: 36px;
                text-align: center;
                height: 100px;
                line-height: 100px;
            }
            .cancel{
                border-right: 1px solid #E8E8EA;
                color: #000000;
            }
            .ok{
                color: #ed1c24;
            }

        }
    }
</style>
