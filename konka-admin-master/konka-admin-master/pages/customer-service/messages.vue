<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>聊天记录查询</span>
    </el-header>
    <el-main>
      <table-list
        ref="tableList"
        source="/admin/customer-service/messages"
        :page-size="10"
      >
        <el-table-column
          header-align="center"
          align="center"
          prop="typeText"
          label="消息类型"
        ></el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="messageTypeText"
          label="内容类型"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="content"
          label="内容"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="sendName"
          label="发送人"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="sendUsername"
          label="发送账号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          label="发送时间"
        />
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
