<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>退换货管理</span>
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
          <el-button
            type="success"
            plain
            round
            size="small"
            @click="exportSelect"
          >
            导出选中
          </el-button>
          <el-button
            type="success"
            plain
            round
            size="small"
            @click="exportList"
          >
            导出全部
          </el-button>
        </template>
      </search-box>
      <el-tabs v-model="tabActive" type="card" @tab-click="confirmSearch">
        <el-tab-pane name="all">
          <span slot="label">全部</span>
        </el-tab-pane>
        <el-tab-pane name="waitAudit">
          <span slot="label"
            ><badge :value="listTotal.waitAudit">待审核</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="waitRefund">
          <span slot="label"
            ><badge :value="listTotal.waitRefund">待退款</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="waitDelivery">
          <span slot="label"
            ><badge :value="listTotal.waitDelivery">待配送</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="delivery">
          <span slot="label"
            ><badge :value="listTotal.delivery">配送中</badge></span
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
        source="/admin/refund-orders"
        :search-keywords="searchKeywords"
      >
        <el-table-column type="selection" />
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="退货单号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="orderCode"
          label="订单号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="refundTypeText"
          label="售后类型"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="content"
          label="描述"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          label="申请时间"
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
              v-if="scope.row.status === 10"
              type="text"
              size="small"
              @click="auditInfo(scope.row.code)"
            >
              审核
            </el-button>
            <el-button
              v-if="scope.row.status === 30"
              type="text"
              size="small"
              @click="confirmRefund(scope.row.code)"
            >
              确认放款
            </el-button>
            <el-button
              v-if="scope.row.status === 40"
              type="text"
              size="small"
              @click="sendOrder(scope.row.code)"
            >
              确认发货
            </el-button>
          </template>
        </el-table-column>
      </table-list>
      <el-dialog title="退换货详情" :visible.sync="detailVisible">
        <div slot="footer" class="dialog-footer">
          <el-button @click="detailVisible = false">
            取 消
          </el-button>
          <el-button type="primary" @click="detailVisible = false">
            确 定
          </el-button>
        </div>
      </el-dialog>
      <el-dialog
        title="审核申请"
        width="300px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="auditBox.visible"
      >
        <el-form>
          <el-form-item label="处理方式">
            <el-select
              v-model="auditBox.info.type"
              placeholder="请选择处理方式"
            >
              <el-option label="退货" :value="10" />
              <el-option label="换货" :value="20" />
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="auditBox.visible = false">
            取消
          </el-button>
          <el-button type="primary" @click="confirmAudit">
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

export default {
  name: 'List',
  components: { Badge, SearchBox, TableList },
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
        waitRefund: 0,
        waitAudit: 0,
        waitDelivery: 0,
        delivery: 0,
        finish: 0
      },
      detailVisible: false,
      tabActive: 'all',
      sendConfirm: {
        code: null,
        visible: false,
        info: {
          trackingType: 10,
          trackingNumber: ''
        }
      },
      auditBox: {
        code: null,
        visible: false,
        info: {
          type: ''
        }
      }
    }
  },
  computed: {
    searchStatus() {
      const tabMapStatus = {
        all: '',
        waitAudit: 10,
        waitRefund: 30,
        waitDelivery: 40,
        delivery: 50,
        finish: 99
      }
      return tabMapStatus[this.tabActive]
    }
  },
  methods: {
    toOrderDetail(code) {},
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
    exportSelect() {},
    exportList() {
      this.$message.info('功能开发中...')
    },
    auditInfo(code) {
      this.auditBox.code = code
      this.auditBox.visible = true
    },
    confirmAudit() {
      this.$axios
        .put(
          '/admin/refund-orders/' + this.auditBox.code + '/audit',
          this.auditBox.info
        )
        .then(response => {
          this.auditBox.visible = false
          this.auditBox.code = null
          this.auditBox.info = {
            type: ''
          }
          this.$refs.tableList.reload()
          this.$message.success(response.message)
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    confirmRefund(code) {
      this.$confirm('此操作确认后系统会立即发起退款, 是否继续?', '提醒', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.refundInfo(code)
      })
    },
    refundInfo(code) {
      this.$axios
        .put('/admin/refund-orders/' + code + '/refund')
        .then(response => {
          this.$refs.tableList.reload()
          this.$message.success(response.message)
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
          '/admin/refund-orders/' + this.sendConfirm.code + '/delivery',
          this.sendConfirm.info
        )
        .then(response => {
          this.sendConfirm.visible = false
          this.sendConfirm.code = null
          this.sendConfirm.info = {
            trackingType: 10,
            trackingNumber: ''
          }
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
  width: 200px;
  display: inline-block;
  overflow: hidden;
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

.order-product-price {
  width: 50px;
  height: 50px;
  float: left;
  text-align: right;
}
</style>
