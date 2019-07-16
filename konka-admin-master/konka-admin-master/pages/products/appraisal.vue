<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>商品评价管理</span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/evaluations" :page-size="10">
        <el-table-column
          header-align="center"
          align="center"
          prop="productName"
          label="商品名称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="specificationText"
          label="商品规格组合"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="rate"
          label="星数"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="content"
          label="评价内容"
        />
        <el-table-column header-align="center" align="center" label="评价图片">
          <template slot-scope="scope">
            <img
              v-for="(image, index) in scope.row.images"
              :key="index"
              :src="image.imageUrl"
            />
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="createdAt"
          label="评价时间"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="username"
          label="评价人"
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
              @click="replyInfo(scope.row.code)"
            >
              回复
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
  components: { TableList },
  methods: {
    replyInfo(code) {}
  }
}
</script>

<style scoped></style>
