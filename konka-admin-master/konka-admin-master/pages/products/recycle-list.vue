<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>商品回收站</span>
    </el-header>
    <el-main>
      <table-list
        ref="tableList"
        source="/admin/product/recycle-list"
        :search-keywords="searchKeywords"
      >
        <el-table-column header-align="center" align="center" label="缩略图">
          <template slot-scope="scope">
            <img :src="scope.row.mainImageUrl" alt="" />
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="title"
          label="标题"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="productCategoryName"
          label="所属分类"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="isRecommendText"
          label="是否推荐"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="isHotText"
          label="是否热门"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="isNewText"
          label="是否新品"
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
          label="修改时间"
        />
        <el-table-column
          header-align="center"
          align="center"
          width="200px"
          label="操作"
        >
          <template slot-scope="scope">
            <el-button
              type="text"
              size="small"
              @click="restoreInfo(scope.row.code)"
            >
              恢复
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
  name: 'DownList',
  components: { TableList },
  data() {
    return {
      searchKeywords: {}
    }
  },
  methods: {
    restoreInfo(code) {
      this.$axios
        .put('/admin/product/' + code + '/restore')
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
