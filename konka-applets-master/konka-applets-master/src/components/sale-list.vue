<template>
    <div class="sale-list-header">
        <div class="sale-header-row">
            <div class="sale-row-item">我的销售额</div>
            <div class="sale-row-item">分销销售额</div>
            <div class="sale-row-item">时间</div>
        </div>
        <div class="sale-list-data">
          <div class="sale-list-row" v-for="(item, index) in saleData" :index="index" :key="index">
            <div class="sale-row-item font-red">￥{{item.price}}</div>
            <div class="sale-row-item font-blue">￥{{item.price2}}</div>
            <div class="sale-row-item font-black">{{item.time}}</div>
          </div>
      </div>
    </div>
</template>
<script>
  export default {
    props: {
      saleData: {
        type: Array,
        default () {
          return []
        }
      }
    },
    data () {
      return {
      }
    },
    computed: {
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
      })
    },
    methods: {
    }
  }
</script>
<style lang="scss" scoped>
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
</style>

