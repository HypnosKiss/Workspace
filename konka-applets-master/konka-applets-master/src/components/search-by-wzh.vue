<template>
  <div :class="['popup-box', 'scale-in', {'active': getSearchStatus}]" @touchmove.stop="() => {}">
    <div class="popup-bg" @click="closePopupBox"></div>
    <div class="popup-content">
      <div class="drawer-box">
        <div class="drawer-title">{{ title }}</div>  
        <!-- <slot name="time">1232</slot>
        <slot name="order">12</slot> -->
        <slot name="body">
          <div class="slot-body">
            <div class="dateStart">
              <span>选取时间</span>
              <picker
                mode="date"
                :value="dateStart"
                start="2019-01-01"
                end="2029-01-01"
                @change="bindDateChange"
              >
                <view class="picker">
                  {{ dateStart }}
                </view>
              </picker>
            </div>
            <div class="dateEnd">
              <span>截止日期</span>
              <picker
                mode="date"
                :value="dateEnd"
                start="2019-01-01"
                end="2029-01-01"
                @change="bindDateChange2"
              >
                <view class="picker">
                  {{ dateEnd }}
                </view>
              </picker>
            </div>
          </div>
        </slot>
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
    computed: {
      ...mapGetters([
        'getSearchStatus'
      ])
    },
    props: {
      title: {
        type: String,
        default: '查询时间'
      }
    },
    data () {
      return {
        dateStart: '',
        dateEnd: ''
      }
    },
    methods: {
      closePopupBox () {
        this.$store.commit('setSearchStatus', false)
      },
      modalSearch () {
        if (!this.dateStart) {
          this.$utils.showToast('请选取时间')
          return
        }
        const params = {
          dateStart: this.dateStart || null, // 如果传入slot，那么dateStart取父组件的值
          dateEnd: this.dateEnd || null
        }
        this.$emit('modalSearch', params)
      },
      // 日期更改
      bindDateChange (e) {
        console.log('picker发送选择改变，携带值为', e.mp.detail.value)
        this.dateStart = e.mp.detail.value
      },
      bindDateChange2 (e) {
        this.dateEnd = e.mp.detail.value
      }
    }
  }
</script>

<style lang="scss" scoped>
    .popup-box {
        z-index: 99;
    }
    .popup-content {
        @extend .column-flex;
        @extend .align-item-stretch;
    }
    
    .drawer-box{
      border-radius: 8px;
      width: 560px;  
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
// 公共样式 slot
  .slot-body {
    margin-bottom: 33px;
    padding: 0 55px;
    font-size: 30px;
    span {
      color: #999999;
    }
    .picker {
      width: 450px;
      height: 80px;
      border: solid 1px #d2d3d5;
      text-align: left;
      line-height: 80px;
      color: #333333;
      padding-left: 20px;
      margin-top: 14px;
    }
    .dateEnd {
      margin-top: 30px;
    }
  }
</style>

