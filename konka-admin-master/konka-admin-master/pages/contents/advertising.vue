<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>广告列表</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <table-list ref="tableList" source="/admin/advertisement" :page-size="10">
        <el-table-column
          header-align="center"
          align="center"
          prop="title"
          label="广告名称"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="positionName"
          label="广告位置"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="contentUrl"
          label="缩略图"
        >
          <template slot-scope="scope">
            <img :src="scope.row.contentUrl" />
          </template>
        </el-table-column>
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
        title="新增/编辑广告"
        width="1000px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="广告名称">
                <el-input v-model="info.title" placeholder="请输入名称" />
              </el-form-item>
              <el-form-item label="广告位置">
                <el-select v-model="info.position" placeholder="请选择位置">
                  <el-option
                    v-for="line in adPositionList"
                    :key="line.code"
                    :label="line.name"
                    :value="line.code"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="排序">
                <el-input v-model="info.order" placeholder="请输入排序" />
              </el-form-item>
              <el-form-item label="跳转方式">
                <el-select
                  v-model="info.connectType"
                  placeholder="请选择跳转方式"
                >
                  <el-option :label="'不跳转'" :value="''" />
                  <el-option
                    v-for="line in connectTypeList"
                    :key="line.code"
                    :label="line.name"
                    :value="line.code"
                  />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="广告图片">
                <single-upload-image
                  v-model="info.content"
                  :init-image="info.contentUrl"
                />
              </el-form-item>
              <el-form-item label="跳转内容">
                <el-input v-model="info.connect" placeholder="请输入跳转内容" />
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
  name: 'Advertising',
  components: { SingleUploadImage, TableList },
  data() {
    return {
      infoVisible: false,
      info: {
        title: '',
        position: '',
        content: '',
        contentUrl: '',
        order: 0,
        connectType: '',
        connect: ''
      },
      searchKeywords: {
        code: ''
      },
      adPositionList: [],
      connectTypeList: [],
      code: null
    }
  },
  created() {
    this.loadPositionList()
    this.loadConnectTypeList()
  },
  methods: {
    createInfo() {
      this.info = {
        title: '',
        position: '',
        content: '',
        contentUrl: '',
        order: 0,
        connectType: '',
        connect: ''
      }
      this.code = null
      this.infoVisible = true
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/advertisement/' + code)
        .then(response => {
          this.info = JSON.parse(JSON.stringify(response.data))
          this.code = code
          this.infoVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    enableInfo(code) {
      this.$axios
        .put('/admin/advertisement/' + code + '/enable')
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
        .put('/admin/advertisement/' + code + '/disable')
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
        .delete('/admin/advertisement/' + code)
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
            '/admin/advertisement' +
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
    loadPositionList() {
      this.$axios
        .get('/admin/select/advertisement-position')
        .then(response => {
          this.adPositionList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadConnectTypeList() {
      this.$axios
        .get('/admin/select/advertisement-contact-type')
        .then(response => {
          this.connectTypeList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
