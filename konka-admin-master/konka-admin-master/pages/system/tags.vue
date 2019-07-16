<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>标签列表</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <search-box @confirm-search="confirmSearch" @reset-input="resetSearch">
        <el-form label-position="right" label-width="120px">
          <el-form-item label="标签编号">
            <el-input v-model="inputSearchKeyword.code" />
          </el-form-item>
          <el-form-item label="标签名称">
            <el-input v-model="inputSearchKeyword.name" />
          </el-form-item>
          <el-form-item label="标签类型">
            <el-select v-model="inputSearchKeyword.type" placeholder="请选择">
              <el-option :key="-1" label="全部" :value="''" />
              <el-option
                v-for="(option, index) in typeList"
                :key="index"
                :label="option.name"
                :value="option.code"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="标签状态">
            <el-select v-model="inputSearchKeyword.status" placeholder="请选择">
              <el-option :key="-1" label="全部" :value="''" />
              <el-option
                v-for="(option, index) in statusList"
                :key="index"
                :label="option.name"
                :value="option.code"
              />
            </el-select>
          </el-form-item>
        </el-form>
      </search-box>
      <table-list
        ref="tableList"
        source="/admin/tags"
        :page-size="10"
        :search-keywords="searchKeywords"
      >
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="标签编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="name"
          label="标签名称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="typeText"
          label="标签类型"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="statusText"
          label="标签状态"
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
        title="新增/编辑标签"
        width="300px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-form-item label="标签名称">
            <el-input v-model="info.name" placeholder="请输入名称" />
          </el-form-item>
          <el-form-item label="标签类型">
            <el-select v-model="info.type" placeholder="请选择类型">
              <el-option
                v-for="(option, index) in typeList"
                :key="index"
                :label="option.name"
                :value="option.code"
              />
            </el-select>
          </el-form-item>
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
      info: {
        name: '',
        type: ''
      },
      inputSearchKeyword: {
        code: '',
        name: '',
        type: '',
        status: ''
      },
      searchKeywords: {},
      typeList: [],
      statusList: [],
      code: null
    }
  },
  created() {
    this.loadTypeList()
    this.loadStatusList()
  },
  methods: {
    createInfo() {
      this.info = {
        name: '',
        type: ''
      }
      this.code = null
      this.infoVisible = true
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/tags/' + code)
        .then(response => {
          this.info = JSON.parse(JSON.stringify(response.data))
          this.code = code
          this.infoVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    enableInfo(code) {
      this.$axios
        .put('/admin/tags/' + code + '/enabled')
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
        .put('/admin/tags/' + code + '/disabled')
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
        .delete('/admin/tags/' + code)
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          console.log(error)
          this.$message.error(error.message)
        })
    },
    saveInfo() {
      this.$axios
        .request({
          method: this.code === null ? 'post' : 'put',
          url: '/admin/tags' + (this.code === null ? '' : '/' + this.code),
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
    confirmSearch() {
      this.searchKeywords = JSON.parse(JSON.stringify(this.inputSearchKeyword))
      this.$refs.tableList.reload()
    },
    resetSearch() {
      this.inputSearchKeyword = {
        code: '',
        name: '',
        type: '',
        status: ''
      }
    },
    loadTypeList() {
      this.$axios
        .get('/admin/select/tags-type')
        .then(response => {
          this.typeList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadStatusList() {
      this.$axios
        .get('/admin/select/status')
        .then(response => {
          this.statusList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>
