<template>
  <div :class="['page', {'iphone-x': isIpx}]">
    <div class="order-offline">
      <img class="img" src="/static/image/big_complete.png" alt="">
      <p>提交成功</p>
      <div>温馨提示：您的订单金额大于2万元，需线下转账付款，完成付款后务必联系客服确认是否汇款成功</div>
      <div>汇款对公账户信息</div>
      <div>户名: {{ orderOffline.bankName || '暂无' }}</div>
      <div>开户行名称: {{ orderOffline.bankOpen || '暂无' }}</div>
      <div>账号：{{ orderOffline.bankAccount || '暂无' }}</div>
      <button @click="goOrderDetail">查看订单</button>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters([
      'orderOffline'
    ])
  },
  data () {
    return {}
  },
  methods: {
    mountedNextTick () {
      // console.log('order-offline -> this.$root.$mp.query', this.$root.$mp.query)
    },
    goOrderDetail () {
      this.$utils.navigateTo('/pages/user/order-detail/main', {
        orderCode: this.orderOffline.orderCode
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .page, .page-main {
    background-color: #f7f7f7;
    padding-bottom: 20px;
    .order-offline {
      padding: 100px 30px 0 30px;
      text-align: center;
      .img {
        width: 200px;
        height: 200px;
      }
      p {
        color: #000000;
        font-size: 40px;
        margin: 59px 0 47px 0
      }
      div {
        text-align: left;
        font-size: 28px;
        color: #888;
        line-height: 40px;
      }
      button {
        margin-top: 100px;
        height: 88px;
        background-color: #f56271;
        border-radius: 10px;
        display: block;
        line-height: 88px;
        color: #ffffff;
        font-size: 36px;
      }
    }
  }
</style>

