<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>管理员列表</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <table-list
        ref="tableList"
        source="/admin/administrators"
        :page-size="10"
      >
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="管理员编号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="typeText"
          label="账号类型"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="username"
          label="登录名"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="nickname"
          label="姓名"
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
          prop="createdAt"
          label="创建时间"
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
              type="text"
              size="small"
              @click="resetPassword(scope.row.code)"
            >
              重置密码
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
        title="新增/编辑管理员"
        width="1100px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-row :gutter="20">
            <el-col :span="6">
              <el-form-item label="姓名">
                <el-input v-model="info.nickname" placeholder="请输入姓名" />
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="登录名">
                <el-input v-model="info.username" placeholder="请输入登录名" />
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item v-if="code === null" label="登录密码">
                <el-input
                  v-model="info.password"
                  type="password"
                  placeholder="请输入登录密码"
                />
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="账户类型">
                <el-select v-model="info.type" placeholder="请选择账户类型">
                  <el-option
                    v-for="(option, index) in typeList"
                    :key="index"
                    :label="option.name"
                    :value="option.code"
                  />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item v-if="info.type === 20" label="授权角色">
                <el-transfer
                  v-model="info.roles"
                  style="text-align: left; display: inline-block"
                  filterable
                  :titles="['未授权角色', '已授权角色']"
                  :props="{
                    label: 'name',
                    key: 'code'
                  }"
                  :data="roleList"
                />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item v-if="info.type === 20" label="授权权限">
                <el-transfer
                  v-model="info.permissions"
                  style="text-align: left; display: inline-block"
                  filterable
                  :titles="['未授权权限', '已授权权限']"
                  :props="{
                    label: 'name',
                    key: 'code'
                  }"
                  :data="permissionList"
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

export default {
  components: { TableList },
  data() {
    return {
      code: null,
      infoVisible: false,
      info: this.defaultInfo(),
      typeList: [],
      roleList: [],
      permissionList: []
    }
  },
  created() {
    this.loadRoleList()
    this.loadTypeList()
    this.loadPermissionList()
  },
  methods: {
    defaultInfo() {
      return {
        username: '',
        nickname: '',
        type: 20,
        password: '',
        roles: [],
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
        .get('/admin/administrators/' + code)
        .then(response => {
          this.info = response.data
          this.code = code
          this.infoVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    resetPassword(code) {
      this.$axios
        .put('/admin/administrators/' + code + '/reset-password')
        .then(response => {
          this.$message.success(response.message)
          this.$alert(
            '重置后密码为：' +
              response.data.password +
              '，密码只会显示一次，请注意保存',
            '提醒'
          )
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    enableInfo(code) {
      this.$axios
        .put('/admin/administrators/' + code + '/enabled')
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
        .put('/admin/administrators/' + code + '/disabled')
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
        .delete('/admin/administrators/' + code)
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
            '/admin/administrators' +
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
    loadTypeList() {
      this.$axios
        .get('/admin/select/administrator-type')
        .then(response => {
          this.typeList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadRoleList() {
      this.$axios
        .get('/admin/select/roles')
        .then(response => {
          this.roleList = response.data
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
