<template>
  <div :class="['page', {'iphone-x': isIpx}]">
    <div class="page-main">
      <div class="box-quota">
        <div class="quota-item">
          <div class="item-num">0</div>
          <div class="item-txt">可提现K币</div>
        </div>
        <div class="quota-item">
            <div class="item-num">0</div>
          <div class="item-txt">实际可提现金额</div>
        </div>
      </div>
      <div class="cash-box">
        <div class="bg-red"></div>
        <div class="operate-area">
          <div class="text-center font-deep-black font-30">提现到微信零钱</div>
          <div class="input-row">
              <div class="symbol"><sapn class="letter">K</sapn><span>币</span></div>
              <input class="take-number" type="digit" placeholder="请输入提现K币" placeholder-style="color: #cccccc;"/>
              <span class="take-all" @click="cashAll">全部提现</span>
          </div>
          <div class="poundage">
              实际到账金额<span class="poundage-price">0.00 元</span>
          </div>
        </div>
        <div class="submit">
            <button @click="cashAll">立即提现</button>
        </div>
        <div class="cash-explain">
           提现说明：<br>
          1.提现成功后，24小时内发送公众号红包；<br>
          2.如有疑问请联系在线客服；<br>
          3.一旦发现任何违规行为，平台有权终止提现，并对您出暂时或永久封号。<br>
        </div>

      </div>
       
    </div>
  </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  export default {
    mixins: [Basic],
    data () {
    },
    methods: {
      cashAll () {
        this.$utils.showToast(`该功能正在开发中`)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color:#f7f7f7;
        font-family: PingFang-SC-Heavy;
    }
    .box-quota{
      color: #fff;
      display: flex;
      background-image: linear-gradient(90deg, 
		#576102 0%, 
		#9f5b2d 0%, 
		#e65458 0%, 
		#f53137 100%);
      .quota-item{
        flex: 1;
        padding: 95px 0 68px 0;
        text-align: center;
        .item-num{
          font-size: 48px;
          font-weight: normal;
          font-stretch: normal;
        }
        .item-txt{
          font-size: 28px;
          margin-top: 10px;
          letter-spacing: 2px;
        }
      }
    }
    .cash-box{
      padding: 0 30px;
      position: relative;
      .bg-red {
        background-image: linear-gradient(90deg, 
      #576102 0%, 
      #9f5b2d 0%, 
      #e65458 0%, 
      #f53137 100%);
        height: 60px;
        position: absolute;
        top: -1px;
        left: 0;
        right: 0;
      }
      .operate-area {
        position: relative;
        z-index: 1;
        @extend .border-bottom;
        box-shadow: 0px 1px 0px 0px 
          #e5e5e5;
        border-radius: 8px;
        padding: 30px;
        background-color: #ffffff;
        box-sizing: border-box;
      }
      .input-row {
          @extend .row-flex;
          @extend .align-item-center;

          padding: 60px 0;

          .symbol {
              @extend .fixed-flex-item;
              margin-right: 20px;
              font-size: 20px;
              color: $deep_black;
              .letter{
                font-size: 40px;
                font-weight: bold;
              }
          }
          .take-number {
              @extend .elastic-flex-item;

              margin-right: 20px;
              height: 40px;
              line-height: 40px;
              font-size: 30px;
              color: $deep_black;
          }
          .take-all {
              @extend .fixed-flex-item;

              color: $theme_sub_color;
              font-size: 28px;
          }
      }
      .poundage {
          font-size: 24px;
          color: #888888;
          text-align: left;
      }
      .poundage-price {
          margin-left: 20px;
          color: $theme_sub_color;
      }

      .submit {
          margin-top: 80px;

          button {
              height: 88px;
              line-height: 88px;
              text-align: center;
              color: #ffffff;
              font-size: 24px;
              border-radius: 44px;
              background-color: $theme_sub_color;
          }
      }
      .cash-explain{
        margin-top: 60px;
        font-size: 26px;
        color: #999;
        line-height: 40px;
      }
    }
    
</style>
