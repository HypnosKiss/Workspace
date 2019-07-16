<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>在售(已上架)商品列表</span>
    </el-header>
    <el-main>
      <table-list
        ref="tableList"
        source="/admin/product"
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
          width="180"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="updatedAt"
          label="修改时间"
          width="180"
        />
        <el-table-column
          header-align="center"
          align="center"
          width="220"
          label="操作"
        >
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
              @click="configPriceInfo(scope.row.code)"
            >
              价格配置
            </el-button>
            <el-button
              type="text"
              size="small"
              @click="setTags(scope.row.code)"
            >
              设置标签
            </el-button>
            <el-button
              type="text"
              size="small"
              @click="disableInfo(scope.row.code)"
            >
              下架
            </el-button>
          </template>
        </el-table-column>
      </table-list>
    </el-main>
    <el-dialog
      title="设置标签"
      width="538px"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="tagVisible"
    >
      <el-form>
        <el-transfer
          v-model="tags"
          filterable
          :titles="['未选择标签', '已选择标签']"
          filter-placeholder="请输入关键字"
          :data="tagList"
          :props="{
            key: 'code',
            label: 'name'
          }"
        />
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="tagVisible = false">
          取消
        </el-button>
        <el-button type="primary" @click="saveTags">
          保存
        </el-button>
      </div>
    </el-dialog>
  </el-container>
</template>

<script>
import TableList from '../../components/TableList'

export default {
  name: 'List',
  components: { TableList },
  data() {
    return {
      tagVisible: false,
      searchKeywords: {
        status: 10
      },
      tagCode: null,
      tags: [],
      tagList: []
    }
  },
  mounted() {
    this.loadTagList()
  },
  methods: {
    updateInfo(code) {
      this.$router.push('/products/page/' + code)
    },
    disableInfo(code) {
      this.$axios
        .put('/admin/product/' + code + '/disable')
        .then(response => {
          this.$message.success(response.message)
          this.$refs.tableList.refresh()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    configPriceInfo(code) {
      this.$router.push('/products/config-price/' + code)
    },
    setTags(code) {
      this.$axios
        .get('/admin/product/' + code + '/tags')
        .then(response => {
          this.tagCode = code
          this.tags = response.data
          this.tagVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    saveTags() {
      this.$axios
        .put('/admin/product/' + this.tagCode + '/tags', this.tags)
        .then(response => {
          this.tagVisible = false
          this.$message.success(response.message)
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadTagList() {
      this.$axios
        .get('/admin/select/tags/product')
        .then(response => {
          this.tagList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
