<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>订单发票列表</span>
    </el-header>
    <el-main>
      <search-box @confirm-search="confirmSearch" @reset-input="resetSearch">
        <el-form label-position="right" label-width="120px">
          <el-form-item label="订单编号">
            <el-input v-model="inputSearchKeyword.orderCode" />
          </el-form-item>
          <el-form-item label="抬头">
            <el-input v-model="inputSearchKeyword.unitName" />
          </el-form-item>
          <el-form-item label="税号">
            <el-input v-model="inputSearchKeyword.taxTicket" />
          </el-form-item>
          <el-form-item label="发票类型">
            <el-select v-model="inputSearchKeyword.invoiceType">
              <el-option :value="''" label="全部" />
              <el-option
                v-for="(option, index) in invoiceTypeList"
                :key="index"
                :value="option.code"
                :label="option.name"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="发票材质">
            <el-select v-model="inputSearchKeyword.materialType">
              <el-option :value="''" label="全部" />
              <el-option
                v-for="(option, index) in materialTypeList"
                :key="index"
                :value="option.code"
                :label="option.name"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="抬头类型">
            <el-select v-model="inputSearchKeyword.type">
              <el-option :value="''" label="全部" />
              <el-option
                v-for="(option, index) in typeList"
                :key="index"
                :value="option.code"
                :label="option.name"
              />
            </el-select>
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
        <el-tab-pane name="wait">
          <span slot="label"
            ><badge :value="listTotal.wait">待开票</badge></span
          >
        </el-tab-pane>
        <el-tab-pane name="ing">
          <span slot="label"><badge :value="listTotal.ing">开票中</badge></span>
        </el-tab-pane>
        <el-tab-pane name="finish">
          <span slot="label"
            ><badge :value="listTotal.finish">已开票</badge></span
          >
        </el-tab-pane>
      </el-tabs>
      <table-list
        ref="tableList"
        source="/admin/invoices"
        :page-size="10"
        :search-keywords="searchKeywords"
      >
        <el-table-column type="selection" />
        <el-table-column
          header-align="center"
          align="center"
          prop="orderCode"
          label="订单号"
          width="150"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="invoiceTypeText"
          label="发票类型"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="materialTypeText"
          label="发票材质"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="typeText"
          label="抬头类型"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="unitName"
          label="抬头"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="taxTicket"
          label="税号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="positionName"
          label="地址和电话"
        >
          <template slot-scope="scope">
            {{ scope.row.taxTicketAddress + ' ' + scope.row.taxTicketPhone }}
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          label="开户行和账号"
        >
          <template slot-scope="scope">
            {{ scope.row.openBank + ' ' + scope.row.bankAccount }}
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          width="160"
          label="申请时间"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="statusText"
          label="状态"
        />
        <el-table-column
          header-align="center"
          align="center"
          label="操作"
          width="150"
        >
          <template slot-scope="scope">
            <el-button
              type="text"
              size="small"
              @click="detailInfo(scope.row.orderCode)"
            >
              详情
            </el-button>
            <el-button
              v-if="scope.row.status === 10"
              type="text"
              size="small"
              @click="updateInfo(scope.row.orderCode)"
            >
              修改
            </el-button>
            <el-button
              v-if="scope.row.status === 10"
              type="text"
              size="small"
              @click="beginInfo(scope.row.orderCode)"
            >
              开票
            </el-button>
            <el-button
              v-if="scope.row.status === 20"
              type="text"
              size="small"
              @click="confirmInfo(scope.row.orderCode)"
            >
              确认开票
            </el-button>
          </template>
        </el-table-column>
      </table-list>
      <el-dialog title="发票详情" :visible.sync="detailVisible" width="400px">
        <el-row :gutter="20">
          <el-col :span="12">
            <div class="info-detail-line">
              <label>订单编号:</label>
              <span>{{ info.orderCode || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>发票类型:</label>
              <span>{{ info.invoiceTypeText || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>发票材质:</label>
              <span>{{ info.materialTypeText || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>抬头类型:</label>
              <span>{{ info.typeText || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>抬头:</label>
              <span>{{ info.unitName || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>税号:</label>
              <span>{{ info.taxTicket || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>地址和电话:</label>
              <span>{{
                info.taxTicketAddress + info.taxTicketPhone || '无'
              }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>开户行和账号:</label>
              <span>{{ info.openBank + info.bankAccount || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>申请时间:</label>
              <span>{{ info.createdAt || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>邮寄地址:</label>
              <span>{{ info.fullAddress || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>邮寄收货人:</label>
              <span>{{ info.name || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>邮寄收货电话:</label>
              <span>{{ info.phone || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>电子发票发送邮箱:</label>
              <span>{{ info.sendEmail || '无' }}</span>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-detail-line">
              <label>电子发票通知手机号码:</label>
              <span>{{ info.sendMobile || '无' }}</span>
            </div>
          </el-col>
        </el-row>
        <span slot="footer">
          <el-button type="primary" @click="detailVisible = false"
            >关闭</el-button
          >
        </span>
      </el-dialog>
      <el-dialog
        title="修改发票信息"
        width="1000px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-row :gutter="20">
            <el-col :span="6">
              <el-form-item label="抬头类型">
                <el-select v-model="info.type">
                  <el-option
                    v-for="(option, index) in typeList"
                    :key="index"
                    :label="option.name"
                    :value="option.code"
                  />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="抬头">
                <el-input v-model="info.unitName" placeholder="请输入抬头" />
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="税号">
                <el-input v-model="info.taxTicket" placeholder="请输入税号" />
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="发票地址">
                <el-input
                  v-model="info.taxTicketAddress"
                  placeholder="请输入发票地址"
                />
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="发票电话">
                <el-input
                  v-model="info.taxTicketPhone"
                  placeholder="请输入发票电话"
                />
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="开户行">
                <el-input v-model="info.openBank" placeholder="请输入开户行" />
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="银行账号">
                <el-input
                  v-model="info.bankAccount"
                  placeholder="请输入银行账号"
                />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 20" :span="6">
              <el-form-item label="邮寄地址省">
                <el-input
                  v-model="info.province"
                  placeholder="请输入邮寄地址省"
                />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 20" :span="6">
              <el-form-item label="邮寄地址市">
                <el-input v-model="info.city" placeholder="请输入邮寄地址市" />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 20" :span="6">
              <el-form-item label="邮寄地址县">
                <el-input
                  v-model="info.county"
                  placeholder="请输入邮寄地址县"
                />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 20" :span="6">
              <el-form-item label="邮寄地址">
                <el-input v-model="info.address" placeholder="请输入邮寄地址" />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 20" :span="6">
              <el-form-item label="邮寄收货人">
                <el-input v-model="info.name" placeholder="请输入邮寄收货人" />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 20" :span="6">
              <el-form-item label="邮寄收货电话">
                <el-input
                  v-model="info.phone"
                  placeholder="请输入邮寄收货电话"
                />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 10" :span="6">
              <el-form-item label="电子发票发送邮箱">
                <el-input
                  v-model="info.sendEmail"
                  placeholder="请输入电子发票发送邮箱"
                />
              </el-form-item>
            </el-col>
            <el-col v-if="info.materialType === 10" :span="6">
              <el-form-item label="电子发票通知手机号码">
                <el-input
                  v-model="info.sendMobile"
                  placeholder="请输入电子发票通知手机号码"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="infoVisible = false">
            取消
          </el-button>
          <el-button type="primary" @click="saveInfo">
            保存
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
  components: { Badge, SearchBox, TableList },
  data() {
    return {
      inputSearchKeyword: this.searchKeywordDefault(),
      searchKeywords: {},
      typeList: [],
      invoiceTypeList: [],
      materialTypeList: [],
      tabActive: 'all',
      listTotal: {
        wait: 0,
        ing: 0,
        finish: 0
      },
      info: {},
      code: null,
      infoVisible: false,
      detailVisible: false
    }
  },
  computed: {
    searchStatus() {
      const tabMapStatus = {
        all: '',
        wait: 10,
        ing: 20,
        finish: 30
      }
      return tabMapStatus[this.tabActive]
    }
  },
  created() {
    this.loadTypeList()
    this.loadInvoiceTypeList()
    this.loadMaterialTypeList()
  },
  methods: {
    searchKeywordDefault() {
      return {
        orderCode: '',
        invoiceType: '',
        type: '',
        materialType: '',
        unitName: '',
        taxTicket: ''
      }
    },
    confirmSearch() {
      const searchKeywords = JSON.parse(JSON.stringify(this.inputSearchKeyword))
      searchKeywords.status = this.searchStatus
      this.searchKeywords = searchKeywords
      this.$refs.tableList.reload()
    },
    resetSearch() {
      this.inputSearchKeyword = this.searchKeywordDefault()
    },
    exportSelect() {},
    exportList() {},
    loadInfo(code) {
      return this.$axios
        .get('/admin/invoices/' + code)
        .then(response => {
          this.info = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    detailInfo(code) {
      this.loadInfo(code).then(() => {
        this.detailVisible = true
      })
    },
    updateInfo(code) {
      this.loadInfo(code).then(() => {
        this.infoVisible = true
        this.code = code
      })
    },
    saveInfo() {
      this.$axios
        .put('/admin/invoices/' + this.code, this.info)
        .then(response => {
          this.$message.success(response.message)
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    beginInfo(code) {
      this.$axios
        .put('/admin/invoices/' + code + '/begin')
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    confirmInfo(code) {
      this.$axios
        .put('/admin/invoices/' + code + '/confirm')
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadTypeList() {
      this.$axios
        .get('/admin/select/invoices/type')
        .then(response => {
          this.typeList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadInvoiceTypeList() {
      this.$axios
        .get('/admin/select/invoices/invoice-type')
        .then(response => {
          this.invoiceTypeList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadMaterialTypeList() {
      this.$axios
        .get('/admin/select/invoices/material-type')
        .then(response => {
          this.materialTypeList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped>
.info-detail-line {
  font-size: 14px;
  line-height: 30px;
  margin-bottom: 10px;
}

.info-detail-line label {
  display: block;
}
</style>
