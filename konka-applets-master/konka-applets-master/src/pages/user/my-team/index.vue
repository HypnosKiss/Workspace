<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <scroll-view :class="['page-main', 'position-relative', 'occupy-all']" :scroll-y="true" @scrolltolower="getNextPage">
            <!--搜索框-->
            <search-btn placeholder="搜索名称" @click="searchOrder"></search-btn>
            <div class="distribution-box">
              <div class="box-oder">
                <div class="order-total">
                   <i :class="['icon', 'iconfont', 'iconteam']" :style="{'background-image': '-webkit-gradient(linear, left top, right bottom, from(#fff), to(#fff))'}"></i>
                  <span>全部合伙人</span>
                </div>
                <div class="order-price">
                  0
                </div>
                <div class="box-quota">
                  <div class="quota-item">
                    <div class="item-num">0</div>
                    <div class="item-txt">销售总额</div>
                  </div>
                  <div class="quota-item">
                    <div class="item-num">0</div>
                    <div class="item-txt">赚取总K币</div>
                  </div>
                </div>
              </div>
             
            </div>
            <div class="distribution-list-header">
                <div class="distribution-header-row">
                    <div class="distribution-row-item">合伙人名称</div>
                    <div class="distribution-row-item">销售额</div>
                    <div class="distribution-row-item">赚取K币</div>
                </div>
                <div v-if="false" class="distribution-list-data">
                  <div class="distribution-list-row">
                    <div class="distribution-row-item font-black">易烊千玺</div>
                    <div class="distribution-row-item font-red">¥1200</div>
                    <div class="distribution-row-item font-green">100</div>
                  </div>
                  <div class="distribution-list-row">
                    <div class="distribution-row-item font-black">杨幂</div>
                    <div class="distribution-row-item font-red">¥1200</div>
                    <div class="distribution-row-item font-green">100</div>
                  </div>
                  <div class="distribution-list-row">
                    <div class="distribution-row-item font-black">迪丽热巴</div>
                    <div class="distribution-row-item font-red">¥1200</div>
                    <div class="distribution-row-item font-green">100</div>
                  </div>
                </div>
            </div>
            <div class="no-list-data" v-if="noSearchResult || true">
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
  import SearchBtn from '@/components/search-btn.vue'
  import List from '@/mixins/list.js'

  export default {
    mixins: [Basic, List],
    components: {
      SearchBtn
    },
    data () {
      return {
        getListPath: ''
      }
    },
    methods: {
      searchOrder () {
      },
      mountedNextTick () {
        this.defaultList()
        this.startLoading()
      },
      defaultKeywords () {
        console.log('defaultKeywords', this.query)
        return {
          status: this.query.status || ''
        }
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
              margin-top: 15px;
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
