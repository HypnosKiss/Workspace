<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>订单编号：《 {{ code }} 》订单详情</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="backToList"
          >返回列表</el-button
        >
      </span>
    </el-header>
    <el-main>
      <el-row :gutter="20">
        <el-col :span="4">
          <label>订单编号：</label>
          <div>{{ orderInfo.code }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="4">
          <label>下单时间：</label>
          <div>{{ orderInfo.createdAt }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="4">
          <label>订单状态：</label>
          <div>{{ orderInfo.statusText }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="4">
          <label>支付时间：</label>
          <div>{{ orderInfo.payAt }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="4">
          <label>订单总金额：</label>
          <div>{{ orderInfo.actuallyPayPrice }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="4">
          <label>备注：</label>
          <div>{{ orderInfo.remarks }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="6">
          <label>收货人：</label>
          <div>{{ orderInfo.clientName }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="6">
          <label>收货电话：</label>
          <div>{{ orderInfo.clientPhone }}</div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="12">
          <label>收货地址：</label>
          <div>{{ orderInfo.fullAddress }}</div>
        </el-col>
      </el-row>
    </el-main>
  </el-container>
</template>

<script>
export default {
  data() {
    return {
      code: null,
      orderInfo: {}
    }
  },
  created() {
    this.code = this.$route.params.code
    this.loadInfo()
  },
  methods: {
    backToList() {
      this.$router.back()
    },
    loadInfo() {
      this.$axios
        .get('/admin/order/' + this.code)
        .then(response => {
          this.orderInfo = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
