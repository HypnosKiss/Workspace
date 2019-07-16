<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>订单查询</span>
    </el-header>
    <el-main>
      <search-box @confirm-search="confirmSearch" @reset-input="resetSearch">
        <el-form label-position="right" label-width="120px">
          <el-form-item label="订单编号">
            <el-input v-model="inputSearchKeyword.code" />
          </el-form-item>
          <el-form-item label="商品名称">
            <el-input v-model="inputSearchKeyword.productName" />
          </el-form-item>
          <el-form-item label="下单时间">
            <el-date-picker
              v-model="inputSearchKeyword.createdAt"
              type="date"
            />
          </el-form-item>
          <el-form-item label="支付时间">
            <el-date-picker v-model="inputSearchKeyword.payAt" type="date" />
          </el-form-item>
          <el-form-item label="客户姓名">
            <el-input v-model="inputSearchKeyword.clientName" />
          </el-form-item>
          <el-form-item label="客户电话">
            <el-input v-model="inputSearchKeyword.clientPhone" />
          </el-form-item>
          <el-form-item label="SKU编码">
            <el-input v-model="inputSearchKeyword.specificationCode" />
          </el-form-item>
          <el-form-item label="下单账号">
            <el-input v-model="inputSearchKeyword.username" />
          </el-form-item>
          <el-form-item label="订单类型">
            <el-input v-model="inputSearchKeyword.type" />
          </el-form-item>
        </el-form>
        <template slot="btn">
          <export-button
            export-url="/admin/export/order"
            :select-rows="selectRows"
            export-mode="select"
            >导出选中</export-button
          >
          <export-button
            export-url="/admin/export/order"
            :search-keywords="searchKeywords"
            >导出全部</export-button
          >
        </template>
      </search-box>
      <el-tabs v-model="tabActive" type="card" @tab-click="confirmSearch">
        <el-tab-pane name="all">
          <span slot="label"
            ><badge :value="listTotal.all">全部订单</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="waitPay">
          <span slot="label"
            ><badge :value="listTotal.waitPay">待付款</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="waitSend">
          <span slot="label"
            ><badge :value="listTotal.waitSend">待发货</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="hasSend">
          <span slot="label"
            ><badge :value="listTotal.hasSend">已发货</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="finish">
          <span slot="label"
            ><badge :value="listTotal.finish">已完成</badge></span
          >
        </el-tab-pane>
      </el-tabs>
      <table-list
        ref="tableList"
        source="/admin/order"
        :search-keywords="searchKeywords"
        @selection-change="selectToRows"
      >
        <el-table-column type="selection" />
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="订单编号"
        />
        <el-table-column
          header-align="center"
          align="center"
          width="280px"
          label="商品信息"
        >
          <template slot-scope="scope">
            <div
              v-for="(product, index) in scope.row.products"
              :key="index"
              class="order-product-box"
            >
              <img class="order-product-image" :src="product.imageUrl" alt="" />
              <span class="order-product-text">
                <div>{{ product.title }}</div>
                <div>{{ product.specificationsText }}</div>
              </span>
              <span class="order-product-price">
                <div>￥{{ product.price }}</div>
                <div>x{{ product.num }}</div>
              </span>
            </div>
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="productTotalNumber"
          label="总数量"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="productTotalPrice"
          label="总金额"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          label="下单时间"
        />
        <el-table-column header-align="center" align="center" label="备注">
          <template slot-scope="scope">
            {{ scope.row.remarks.substr(0, 20) }}
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="createUserUsername"
          label="下单账号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="statusText"
          label="状态"
        />
        <el-table-column header-align="center" align="center" label="操作">
          <template slot-scope="scope">
            <el-button
              type="text"
              size="small"
              @click="toOrderDetail(scope.row.code)"
            >
              订单详情
            </el-button>
            <el-button
              v-if="scope.row.status === 5 && scope.row.payType === 20"
              type="text"
              size="small"
              @click="payOrder(scope.row.code)"
            >
              确认支付
            </el-button>
            <el-button
              v-if="scope.row.status === 30"
              type="text"
              size="small"
              @click="sendOrder(scope.row.code)"
            >
              确认发货
            </el-button>
          </template>
        </el-table-column>
      </table-list>
      <el-dialog
        title="确认支付"
        width="300px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="payConfirm.visible"
      >
        <el-form>
          <el-form-item label="支付时间">
            <el-date-picker
              v-model="payConfirm.info.payTime"
              type="datetime"
              placeholder="请选择支付时间"
            />
          </el-form-item>
          <el-form-item label="支付单据号(选填)">
            <el-input
              v-model="payConfirm.info.payOrderNumber"
              placeholder="请输入支付单据号"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="payConfirm.visible = false">
            取消
          </el-button>
          <el-button type="primary" @click="confirmPayOrder">
            确认
          </el-button>
        </div>
      </el-dialog>
      <el-dialog
        title="确认发货"
        width="300px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="sendConfirm.visible"
      >
        <el-form>
          <el-form-item label="快递单号">
            <el-input
              v-model="sendConfirm.info.trackingNumber"
              placeholder="请输入快递单号"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="sendConfirm.visible = false">
            取消
          </el-button>
          <el-button type="primary" @click="confirmSendOrder">
            确认
          </el-button>
        </div>
      </el-dialog>
    </el-main>
  </el-container>
</template>

<script>
import TableList from '../../components/TableList'
import SearchBox from '../../components/SearchBox'
import Badge from '../../components/Badge'
import ExportButton from '../../components/ExportButton'

export default {
  name: 'List',
  components: { ExportButton, Badge, SearchBox, TableList },
  data() {
    return {
      inputSearchKeyword: {
        specificationCode: '',
        type: '',
        username: '',
        clientPhone: '',
        clientName: '',
        payAt: '',
        createdAt: '',
        productName: '',
        code: ''
      },
      searchKeywords: {},
      listTotal: {
        all: 0,
        waitPay: 0,
        waitSend: 0,
        hasSend: 0,
        finish: 0
      },
      tabActive: 'all',
      payConfirm: {
        code: null,
        visible: false,
        info: {
          payTime: '',
          payOrderNumber: ''
        }
      },
      sendConfirm: {
        code: null,
        visible: false,
        info: {
          trackingType: 10,
          trackingNumber: ''
        }
      },
      selectRows: []
    }
  },
  computed: {
    searchStatus() {
      const tabMapStatus = {
        all: '',
        waitPay: 10,
        waitSend: 20,
        hasSend: 30,
        finish: 40
      }
      return tabMapStatus[this.tabActive]
    }
  },
  methods: {
    selectToRows(event) {
      this.selectRows = event.map(function(row) {
        return row.code
      })
    },
    toOrderDetail(code) {
      this.$router.push('/orders/detail/' + code)
    },
    confirmSearch() {
      const searchKeyword = JSON.parse(JSON.stringify(this.inputSearchKeyword))
      searchKeyword.status = this.searchStatus
      this.searchKeywords = searchKeyword
      this.$refs.tableList.reload()
    },
    resetSearch() {
      this.inputSearchKeyword = {
        specificationCode: '',
        type: '',
        username: '',
        clientPhone: '',
        clientName: '',
        payAt: '',
        createdAt: '',
        productName: '',
        code: ''
      }
    },
    payOrder(code) {
      this.payConfirm.code = code
      this.payConfirm.visible = true
    },
    confirmPayOrder() {
      this.$axios
        .put(
          '/admin/order/' + this.payConfirm.code + '/to-pay',
          this.payConfirm.info
        )
        .then(response => {
          this.payConfirm.visible = false
          this.payConfirm.code = null
          this.$message.success(response.message)
          this.$refs.tableList.reload()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    sendOrder(code) {
      this.sendConfirm.code = code
      this.sendConfirm.visible = true
    },
    confirmSendOrder() {
      this.$axios
        .put(
          '/admin/order/' + this.sendConfirm.code + '/confirm-delivery',
          this.sendConfirm.info
        )
        .then(response => {
          this.sendConfirm.visible = false
          this.sendConfirm.code = null
          this.$message.success(response.message)
          this.$refs.tableList.reload()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped>
.order-product-box {
  width: 230px;
  display: inline-block;
  overflow: hidden;
}

.order-product-box + .order-product-box {
  margin-top: 8px;
}

.order-product-image {
  max-width: 50px;
  max-height: 50px;
  float: left;
}

.order-product-text {
  width: 100px;
  height: 50px;
  float: left;
}

.order-product-text div {
  width: 100px;
  overflow: hidden;
  height: 25px;
  text-align: left;
  line-height: 25px;
}

.order-product-price {
  width: 80px;
  height: 50px;
  float: left;
  text-align: right;
}

.order-product-price div {
  width: 80px;
  height: 25px;
  overflow: hidden;
  text-align: right;
}
</style>
