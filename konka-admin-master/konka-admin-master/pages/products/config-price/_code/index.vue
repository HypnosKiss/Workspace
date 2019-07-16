<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>《{{ product.title }}》产品规格组合配置</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="returnToList"
          >返回</el-button
        >
      </span>
    </el-header>
    <el-main>
      <table-list ref="tableList" :source="tableSource" :page-size="10">
        <el-table-column
          header-align="center"
          align="center"
          prop="code"
          label="组合编号"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="specificationCodesText"
          label="组合名称"
        />
        <el-table-column header-align="center" align="center" label="组合图片">
          <template slot-scope="scope">
            <img :src="scope.row.imageUrl" />
          </template>
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="price"
          label="销售价"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="guidePrice"
          label="指导价"
        />
        <el-table-column
          header-align="center"
          align="center"
          prop="stock"
          label="库存数"
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
          </template>
        </el-table-column>
      </table-list>
      <el-dialog
        title="编辑规格组合"
        width="500px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-form-item label="组合图片">
            <single-upload-image
              v-model="info.image"
              :init-image="info.imageUrl"
            />
          </el-form-item>
          <el-form-item label="销售价">
            <el-input
              v-model="info.price"
              clearable
              placeholder="请输入销售价"
              @focus="autoClearInput('price')"
            />
          </el-form-item>
          <el-form-item label="指导价">
            <el-input
              v-model="info.guidePrice"
              clearable
              placeholder="请输入指导价"
              @focus="autoClearInput('guidePrice')"
            />
          </el-form-item>
          <el-form-item label="库存数">
            <el-input
              v-model="info.stock"
              clearable
              placeholder="请输入库存数"
            />
          </el-form-item>
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
import TableList from '../../../../components/TableList'
import SingleUploadImage from '../../../../components/SingleUploadImage'

export default {
  name: 'Advertising',
  components: { SingleUploadImage, TableList },
  data() {
    return {
      tableSource: '',
      infoVisible: false,
      info: {
        image: '',
        price: '',
        guidePrice: '',
        stock: '',
        imageUrl: ''
      },
      product: {},
      code: null,
      fromPath: ''
    }
  },
  created() {
    this.loadProductInfo(this.$route.params.code)
    this.tableSource =
      '/admin/product/' + this.$route.params.code + '/specification-combination'
  },
  methods: {
    autoClearInput(field) {
      if (Number(this.info[field]) === 0) {
        this.info[field] = ''
      }
    },
    loadProductInfo(code) {
      this.$axios
        .get('/admin/product/' + code)
        .then(response => {
          this.product = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    updateInfo(code) {
      this.$axios
        .get(
          '/admin/product/' +
            this.product.code +
            '/specification-combination/' +
            code
        )
        .then(response => {
          this.info = JSON.parse(JSON.stringify(response.data))
          this.code = code
          this.infoVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    returnToList() {
      this.$router.back()
    },
    saveInfo() {
      this.$axios
        .put(
          '/admin/product/' +
            this.product.code +
            '/specification-combination/' +
            this.code,
          this.info
        )
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
