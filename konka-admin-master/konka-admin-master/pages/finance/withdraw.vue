<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>提现列表</span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/withdraw" :page-size="10">
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="提现编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="partnerName"
          label="合伙人姓名"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="partnerCode"
          label="合伙人编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="partnerPhone"
          label="合伙人电话"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="price"
          label="提现金额"
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
    </el-main>
  </el-container>
</template>

<script>
import TableList from '../../components/TableList'
export default {
  name: 'List',
  components: { TableList }
}
</script>

<style scoped></style>
