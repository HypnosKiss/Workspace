<template>
    <div :class="['page', {'iphone-x': isIpx}]">
       <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
            <!--搜索框-->
          <search-btn placeholder="搜索订单" @click="$store.commit('setSearchStatus', true)"></search-btn>
          <div class="distribution-box">
            <div class="box-oder">
              <div class="order-total">
                  <i :class="['icon', 'iconfont', 'icondingdan']" :style="{'background-color': '#fff'}"></i>
                <span>订单总量</span>
              </div>
              <div class="order-price">
                {{ commissionOrderTotal.orderTotal || 0 }}
              </div>
              <div class="box-quota">
                <div class="quota-item">
                  <div class="item-num">{{ commissionOrderTotal.salesAmountTotal || 0}}</div>
                  <div class="item-txt">销售总额</div>
                </div>
                <div class="quota-item">
                  <div class="item-num">{{ commissionOrderTotal.integralTotal || 0}}</div>
                  <div class="item-txt">赚取总K币</div>
                </div>
              </div>
            </div>
            
          </div>
          <div class="distribution-list-header">
            <div class="distribution-header-row">
                <div class="distribution-row-item">订单 (订单号)</div>
                <div class="distribution-row-item">销售额</div>
                <div class="distribution-row-item">赚取K币</div>
            </div>
            <div class="distribution-list-data">
              <div class="distribution-list-row" v-for="(each, m) in list" :key="m">
                <div class="distribution-row-item font-black">{{ each.orderCode }}</div>
                <div class="distribution-row-item font-red">{{ each.orderPayAmount }}</div>
                <div class="distribution-row-item font-green">{{ each.integral }}</div>
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
        <!-- 搜索订单弹窗 -->
        <user-search  :title="title" @modalSearch="getCommissionOrder" slot="order">
          <div slot="order">
            <div class="drawer-content">
              <label class="lbl">订单号</label>
              <div class="d-picker">
                <input type="text" v-model="orderCode">
              </div>
            </div>
          </div>
        </user-search>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import SearchBtn from '@/components/search-btn.vue'
  import UserSearch from '@/components/user-search.vue'
  import List from '@/mixins/list.js'

  export default {
    mixins: [Basic, List],
    components: {
      SearchBtn,
      UserSearch
    },
    data () {
      return {
        getListPath: '/partner/sale-commission-orders',
        title: '搜索订单',
        commissionOrderTotal: {},
        orderCode: ''
      }
    },
    methods: {
      mountedNextTick () {
        console.log('enter')
        this.getCommissionOrderTotal()
        this.defaultList()
        this.startLoading()
      },
      defaultKeywords () {
        console.log('defaultKeywords')
        return {
          orderCode: this.orderCode
        }
      },
      getCommissionOrder (val) {
        if (!this.orderCode) {
          this.$utils.showModal('请输入订单号')
          return
        }
        this.keywords.orderCode = this.orderCode
        this.changeKeyword()
        this.orderCode = ''
        this.$store.commit('setSearchStatus', false)
      },
      // 分销订单统计
      getCommissionOrderTotal () {
        let that = this
        let options = {
          url: '/partner/sale-commission-order-total',
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(res => {
            console.log(res)
            this.commissionOrderTotal = res
            console.log('commissionOrderTotalt====>', res)
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color:#f7f7f7;
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
            font-size: 40px;
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
    .distribution-list-header{
      background-color: #fff;
      font-size: 32px;
      .distribution-header-row{
        border-top: 1px solid #e6e6e6;
        display: flex;
        padding: 36px 0;
        color: #666;
        text-align: center;
        .distribution-row-item{
          flex: 1;
          
        }
      }
      .distribution-list-data{
        border-top: 1px solid #e6e6e6;
        border-bottom: 1px solid #e6e6e6;
        font-size: 30px;
        padding-left: 30px;
        .distribution-list-row{
          display: flex;
          text-align: center;
           border-bottom: 1px solid #e6e6e6;
           padding: 36px 0;
           &:last-child{
              border-bottom: none;
            }
          .distribution-row-item{
            flex: 1;
            
          }
        }
      }
    }
</style>
