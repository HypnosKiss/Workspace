<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>合伙人佣金规则列表</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <search-box @confirm-search="confirmSearch" @reset-input="resetSearch">
        <el-form label-position="right" label-width="120px">
          <el-form-item label="规则名称">
            <el-input v-model="inputSearchKeyword.name" />
          </el-form-item>
        </el-form>
      </search-box>
      <table-list
        ref="tableList"
        source="/admin/commission-rules"
        :page-size="10"
        :search-keywords="searchKeywords"
      >
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="规则编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="name"
          label="规则名称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="beginTime"
          width="160px"
          label="开始时间"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="endTime"
          width="160px"
          label="结束时间"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="firstLevelCommissionPercentage"
          label="一级佣金"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="secondLevelCommissionPercentage"
          label="二级佣金"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="order"
          label="优先级"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          width="180px"
          label="创建时间"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="updatedAt"
          width="180px"
          label="最后修改时间"
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
              @click="updateInfo(scope.row.code)"
            >
              编辑
            </el-button>
            <el-button
              type="text"
              size="small"
              @click="confirmDelete(scope.row.code)"
            >
              删除
            </el-button>
            <el-button
              v-if="scope.row.status === 20"
              type="text"
              size="small"
              @click="enableInfo(scope.row.code)"
            >
              启用
            </el-button>
            <el-button
              v-if="scope.row.status === 10"
              type="text"
              size="small"
              @click="disableInfo(scope.row.code)"
            >
              禁用
            </el-button>
          </template>
        </el-table-column>
      </table-list>
      <el-dialog
        title="新增/编辑规则"
        width="1100px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-row :gutter="20">
            <el-col :span="6">
              <el-form-item label="规则名称">
                <el-input v-model="info.name" placeholder="请输入名称" />
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="优先级(数字越大优先级越高)">
                <el-input v-model="info.order" placeholder="请输入优先级" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="20">
            <el-col :span="6">
              <el-form-item label="开始时间">
                <el-date-picker
                  v-model="info.beginTime"
                  format="yyyy-MM-dd HH:mm"
                  value-format="yyyy-MM-dd HH:mm"
                  type="datetime"
                  placeholder="选择日期时间"
                />
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="结束时间">
                <el-date-picker
                  v-model="info.endTime"
                  format="yyyy-MM-dd HH:mm"
                  value-format="yyyy-MM-dd HH:mm"
                  type="datetime"
                  placeholder="选择日期时间"
                />
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="一级佣金比例">
                <el-input
                  v-model="info.firstLevelCommissionPercentage"
                  placeholder="请输入一级佣金比例"
                >
                  <span slot="append">%</span>
                </el-input>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="二级佣金比例">
                <el-input
                  v-model="info.secondLevelCommissionPercentage"
                  placeholder="请输入二级佣金比例"
                >
                  <span slot="append">%</span>
                </el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="应用商品标签">
                <el-transfer
                  v-model="info.products"
                  :titles="['未选商品标签', '已选商品标签']"
                  :props="{
                    key: 'code',
                    label: 'name'
                  }"
                  :filterable="true"
                  :data="productTags"
                />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="应用合伙人标签">
                <el-transfer
                  v-model="info.partners"
                  :titles="['未选合伙人标签', '已选合伙人标签']"
                  :props="{
                    key: 'code',
                    label: 'name'
                  }"
                  :filterable="true"
                  :data="partnerTags"
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

export default {
  components: { SearchBox, TableList },
  data() {
    return {
      infoVisible: false,
      info: this.defaultInfo(),
      code: null,
      partnerTags: [],
      productTags: [],
      inputSearchKeyword: this.defaultSearch(),
      searchKeywords: {}
    }
  },
  created() {
    this.loadPartnerTags()
    this.loadProductTags()
  },
  methods: {
    defaultInfo() {
      return {
        name: '',
        order: 0,
        beginTime: '',
        endTime: '',
        firstLevelCommissionPercentage: 0,
        secondLevelCommissionPercentage: 0,
        partners: [],
        products: []
      }
    },
    defaultSearch() {
      return {
        name: ''
      }
    },
    confirmSearch() {
      this.searchKeywords = JSON.parse(JSON.stringify(this.inputSearchKeyword))
      this.$refs.tableList.reload()
    },
    resetSearch() {
      this.inputSearchKeyword = this.defaultSearch()
    },
    createInfo() {
      this.info = this.defaultInfo()
      this.code = null
      this.infoVisible = true
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/commission-rules/' + code)
        .then(response => {
          this.info = response.data
          this.code = code
          this.infoVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    enableInfo(code) {
      this.$axios
        .put('/admin/commission-rules/' + code + '/enabled')
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    disableInfo(code) {
      this.$axios
        .put('/admin/commission-rules/' + code + '/disabled')
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    confirmDelete(code) {
      this.$confirm('此操作将永久删除该记录, 是否继续?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.deleteInfo(code)
      })
    },
    deleteInfo(code) {
      this.$axios
        .delete('/admin/commission-rules/' + code)
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    saveInfo() {
      this.$axios
        .request({
          method: this.code === null ? 'post' : 'put',
          url:
            '/admin/commission-rules' +
            (this.code === null ? '' : '/' + this.code),
          data: this.info
        })
        .then(response => {
          this.$message.success(response.message)
          this.infoVisible = false
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadPartnerTags() {
      this.$axios
        .get('/admin/select/tags/partner')
        .then(response => {
          this.partnerTags = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadProductTags() {
      this.$axios
        .get('/admin/select/tags/product')
        .then(response => {
          this.productTags = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
