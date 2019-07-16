<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>产品规格管理</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增规格</el-button
        >
      </span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/specification">
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="规格编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="name"
          label="规格名称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="parentName"
          label="所属规格分类"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="order"
          label="排序"
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
        title="新增/编辑产品规格"
        width="500px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-form-item label="规格名称">
            <el-input v-model="info.name" placeholder="请输入名称" />
          </el-form-item>
          <el-form-item label="所属规格分类">
            <el-select
              v-model="info.parentCode"
              placeholder="请选择所属规格分类"
            >
              <el-option :label="'创建分类'" :value="''" />
              <el-option
                v-for="line in parentList"
                :key="line.code"
                :label="line.name"
                :value="line.code"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="排序">
            <el-input v-model="info.order" placeholder="请输入排序" />
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

export default {
  name: 'Specification',
  components: { TableList },
  data() {
    return {
      code: null,
      infoVisible: false,
      info: {
        name: '',
        order: 0,
        parentCode: ''
      },
      parentList: []
    }
  },
  created() {
    this.loadParentList()
  },
  methods: {
    createInfo() {
      this.info = {
        name: '',
        order: 0,
        parentCode: ''
      }
      this.code = null
      this.infoVisible = true
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/specification/' + code)
        .then(response => {
          this.info = JSON.parse(JSON.stringify(response.data))
          this.code = code
          this.infoVisible = true
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
        .delete('/admin/specification/' + code)
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          console.log(error)
          this.$message.error(error.message)
        })
    },
    enableInfo(code) {
      this.$axios
        .put('/admin/specification/' + code + '/enable')
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
        .put('/admin/specification/' + code + '/disable')
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
            '/admin/specification' +
            (this.code === null ? '' : '/' + this.code),
          data: this.info
        })
        .then(response => {
          this.$message.success(response.message)
          this.infoVisible = false
          this.loadParentList()
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadParentList() {
      this.$axios
        .get('/admin/select/specification-category')
        .then(response => {
          this.parentList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
