<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>单页列表</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/articles" :page-size="10">
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="单页编码"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="title"
          label="单页标题"
        />
        <el-table-column header-align="center" align="center" label="首张图片">
          <template slot-scope="scope">
            <img :src="scope.row.contentUrl[0]" />
          </template>
        </el-table-column>
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
          </template>
        </el-table-column>
      </table-list>
      <el-dialog
        title="新增/编辑单页"
        width="1400px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-row :gutter="20">
            <el-col :span="6">
              <el-form-item label="单页标题">
                <el-input v-model="info.title" placeholder="请输入名称" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="20">
            <el-col
              v-for="(image, index) in info.content"
              :key="index"
              :span="4"
            >
              <el-form-item :label="'内容图' + (index + 1)">
                <single-upload-image
                  v-model="image.image"
                  class="image-row"
                  :init-image="image.imageUrl"
                  :image-height="150"
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
  components: { SingleUploadImage, TableList },
  data() {
    return {
      infoVisible: false,
      info: this.defaultInfo()
    }
  },
  methods: {
    defaultInfo() {
      return {
        title: '',
        content: [
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          },
          {
            image: '',
            imageUrl: ''
          }
        ]
      }
    },
    createInfo() {
      this.info = this.defaultInfo()
      this.code = null
      this.infoVisible = true
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/articles/' + code)
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
        .delete('/admin/articles/' + code)
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
          url: '/admin/articles' + (this.code === null ? '' : '/' + this.code),
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
    }
  }
}
</script>

<style scoped></style>
