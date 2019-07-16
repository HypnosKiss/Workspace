<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>产品分类管理</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/product-categories">
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="分类编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="name"
          label="分类名称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="parentName"
          label="上级分类"
        />
        <el-table-column header-align="center" align="center" label="分类图标">
          <template slot-scope="scope">
            <img :src="scope.row.imageUrl" alt="" />
          </template>
        </el-table-column>
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
        <el-table-column
          header-align="center"
          align="center"
          prop="recommendText"
          label="首屏推荐"
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
        title="新增/编辑产品分类"
        width="1000px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="分类名称">
                <el-input v-model="info.name" placeholder="请输入名称" />
              </el-form-item>
              <el-form-item label="上级分类">
                <el-select v-model="info.parentCode" placeholder="请选择上级">
                  <el-option :label="'无上级'" :value="''" />
                  <el-option
                    v-for="line in parentSelectList"
                    :key="line.code"
                    :label="line.name"
                    :value="line.code"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="排序">
                <el-input v-model="info.order" placeholder="请输入排序" />
              </el-form-item>
              <el-form-item label="首屏推荐">
                <el-select
                  v-model="info.recommend"
                  placeholder="请选择是否推荐"
                >
                  <el-option
                    v-for="line in recommendList"
                    :key="line.code"
                    :label="line.name"
                    :value="line.code"
                  />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="广告图片(宽512px,高512px,100K内)">
                <single-upload-image
                  v-model="info.image"
                  :init-image="info.imageUrl"
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
import SingleUploadImage from '../../components/SingleUploadImage'

export default {
  name: 'Categories',
  components: { SingleUploadImage, TableList },
  data() {
    return {
      code: null,
      infoVisible: false,
      info: {
        name: '',
        image: '',
        imageUrl: '',
        order: 0,
        parentCode: '',
        recommend: 10
      },
      parentList: [],
      recommendList: []
    }
  },
  computed: {
    parentSelectList() {
      return this.parentList.filter(
        function(parent) {
          return parent.code !== this.code
        }.bind(this)
      )
    }
  },
  created() {
    this.loadRecommendList()
    this.loadParentList()
  },
  methods: {
    createInfo() {
      this.info = {
        name: '',
        image: '',
        imageUrl: '',
        order: 0,
        parentCode: '',
        recommend: 10
      }
      this.code = null
      this.infoVisible = true
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/product-categories/' + code)
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
        .delete('/admin/product-categories/' + code)
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
        .put('/admin/product-categories/' + code + '/enable')
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
        .put('/admin/product-categories/' + code + '/disable')
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
            '/admin/product-categories' +
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
    loadRecommendList() {
      this.$axios
        .get('/admin/select/product-category-recommend')
        .then(response => {
          this.recommendList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadParentList() {
      this.$axios
        .get('/admin/select/top-product-category')
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
