<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>角色列表</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/roles" :page-size="10">
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="角色编号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="name"
          label="角色名称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          label="创建时间"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="updatedAt"
          label="最后更新时间"
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
        title="新增/编辑角色"
        width="550px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-form-item label="角色名称">
            <el-input v-model="info.name" placeholder="请输入角色名称" />
          </el-form-item>
          <el-form-item label="授权权限">
            <el-transfer
              v-model="info.permissions"
              filterable
              :titles="['未授权权限', '已授权权限']"
              :props="{
                key: 'code',
                label: 'name'
              }"
              :data="permissionList"
            />
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
  components: { TableList },
  data() {
    return {
      infoVisible: false,
      code: null,
      info: this.defaultInfo(),
      permissionList: []
    }
  },
  created() {
    this.loadPermissionList()
  },
  methods: {
    defaultInfo() {
      return {
        name: '',
        permissions: []
      }
    },
    createInfo() {
      this.code = null
      this.info = this.defaultInfo()
      this.infoVisible = true
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/roles/' + code)
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
        .put('/admin/roles/' + code + '/enabled')
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
        .put('/admin/roles/' + code + '/disabled')
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
        .delete('/admin/roles/' + code)
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
          url: '/admin/roles' + (this.code === null ? '' : '/' + this.code),
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
    loadPermissionList() {
      this.$axios
        .get('/admin/select/permissions')
        .then(response => {
          this.permissionList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
