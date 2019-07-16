<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">

            <div class="total">
                <div class="text-center">
                    <span class="balance">{{ balance }}</span>
                    <span class="font-light-black font-24">元</span>
                </div>
                <div class="text-center">
                    <span class="font-light-black font-24">可提现金额</span>
                </div>
            </div>

            <div class="operate-area">
                <div class="text-center font-deep-black font-30">提现到微信零钱</div>
                <div class="input-row">
                    <span class="symbol">¥</span>
                    <input class="take-number" type="digit" placeholder="请输入提现金额" placeholder-style="color: #cccccc;" v-model="takeCash" @input="checkMoney"/>
                    <span class="take-all" @click="takeAll">全部提现</span>
                </div>
                <div class="poundage">
                    手续费<span class="poundage-price">{{ aug }}元</span>
                </div>
            </div>

            <div class="submit">
                <button @click="submit">立即提现</button>
            </div>

        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    data () {
      return {
        balance: 0,
        charge: 0.09,
        takeCash: 0,
        aug: 0
      }
    },
    methods: {
      mountedNextTick () {
        this.balance = parseFloat(this.query.limitMoney)
        this.aug = (parseFloat(this.takeCash || 0) * parseFloat(this.charge)).toFixed(2)
      },
      takeAll () {
        this.takeCash = this.balance
        this.aug = (parseFloat(this.takeCash || 0) * parseFloat(this.charge)).toFixed(2)
      },
      checkMoney () {
        if (this.takeCash > this.balance) {
          this.$utils.error('提现金额不能大于可提现金额')
          this.takeCash = this.balance
        }
        this.aug = (parseFloat(this.takeCash || 0) * parseFloat(this.charge)).toFixed(2)
      },
      submit () {
        // this.$utils.showModal('点击了提现')
        if (this.takeCash === 0) {
          this.$utils.error(`不能提取0元`)
          return
        }
        let that = this
        let options = {
          url: '/partner/withdraw',
          method: 'post',
          data: {
            price: that.takeCash
          }
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            this.takeCash = 0
            this.aug = (parseFloat(this.takeCash || 0) * parseFloat(this.charge)).toFixed(2)
            this.$utils.navigateBack()
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page-main {
        padding: 0 30px;
    }
    .total {
        @extend .column-flex;
        @extend .align-item-center;
        @extend .justify-center;

        padding: 78px 0 76px 0;

        .balance {
            color: $deep_black;
            font-size: 48px;
            margin-right: 10px;
        }
    }

    .operate-area {
        @extend .border-bottom;

        padding: 30px;
        border-radius: 4px;
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
            font-size: 40px;
            color: $deep_black;
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
</style>
