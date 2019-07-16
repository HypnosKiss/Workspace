<template>
  <div :class="['popup-box', 'scale-in', {'active': getSearchStatus}]" @touchmove.stop="() => {}">
    <div class="popup-bg" @click="closePopupBox"></div>
    <div class="popup-content">
      <div class="drawer-box">
        <div class="drawer-title">{{title}}</div>  
        <slot name="month"></slot>
        <slot name="year"></slot>
        <slot name="order"></slot>
        <div class="btn-box">
          <div class="btn cancel" @click="closePopupBox">取消</div>
          <div class="btn ok" @click="modalSearch">搜索</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  export default {
    name: 'getSearchStatus',
    props: {
      title: {
        type: String,
        default () {
          return '查询时间'
        }
      }
    },
    data () {
      return {
      }
    },
    computed: {
      ...mapGetters([
        'getSearchStatus'
      ])
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
      })
    },
    methods: {
      modalSearch () {
        this.$emit('modalSearch')
      },
      closePopupBox () {
        this.$store.commit('setSearchStatus', false)
      }
    }
  }
</script>
<style lang="scss" scoped>
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
<style lang="scss">
  .drawer-box{
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
        .picker {
          width: 100%;
          height: 40px;
          text-align: left;
          line-height: 40px;
          color: #333333;
          padding-left: 20px;
        }
      }
    } 
  }
</style>


