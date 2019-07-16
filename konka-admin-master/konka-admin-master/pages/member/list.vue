<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>会员列表</span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/users" :page-size="10">
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="用户编号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="username"
          label="用户名"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="avatarUrl"
          label="头像"
        >
          <template slot-scope="scope">
            <img :src="scope.row.avatarUrl" />
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="nickname"
          label="昵称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          label="注册时间"
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
    </el-main>
  </el-container>
</template>

<script>
import TableList from '../../components/TableList'

export default {
  components: { TableList },
  methods: {
    enableInfo(code) {
      this.$axios
        .put('/admin/users/' + code + '/enable')
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
        .put('/admin/users/' + code + '/disable')
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
