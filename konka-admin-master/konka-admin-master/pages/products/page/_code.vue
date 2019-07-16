<template>
  <el-container v-loading="!isReady">
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>新增/编辑商品信息</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="saveInfo"
          >保存</el-button
        >
      </span>
    </el-header>
    <el-main>
      <el-form :model="info" class="page-form">
        <el-row :gutter="20">
          <el-col :span="6">
            <el-form-item label="所属分类">
              <el-select
                v-model="info.productCategoryCode"
                placeholder="请选择"
              >
                <el-option-group
                  v-for="categoryGroup in productCategoryList"
                  :key="categoryGroup.name"
                  :label="categoryGroup.name"
                >
                  <el-option
                    v-for="category in categoryGroup.subs"
                    :key="category.code"
                    :label="category.name"
                    :value="category.code"
                  />
                </el-option-group>
              </el-select>
            </el-form-item>
            <el-form-item label="商品标题">
              <el-input v-model="info.title" />
            </el-form-item>
            <el-form-item label="商品副标题">
              <el-input v-model="info.subTitle" />
            </el-form-item>
            <el-form-item label="康佳产品编码">
              <el-input v-model="info.konkaProductCode" />
            </el-form-item>
            <el-form-item label="排序(数据约大约前)">
              <el-input v-model="info.order" />
            </el-form-item>
            <el-form-item label="列表缩略图(宽750px,高750px,200K内)">
              <single-upload-image
                v-model="info.mainImage"
                :init-image="info.mainImageUrl"
                :image-height="200"
              />
            </el-form-item>
            <el-form-item label="是否热销">
              <el-select v-model="info.isHot" placeholder="请选择">
                <el-option
                  v-for="item in hotList"
                  :key="item.code"
                  :label="item.name"
                  :value="item.code"
                />
              </el-select>
            </el-form-item>
            <el-form-item label="是否推荐">
              <el-select v-model="info.isRecommend" placeholder="请选择">
                <el-option
                  v-for="item in recommendList"
                  :key="item.code"
                  :label="item.name"
                  :value="item.code"
                />
              </el-select>
            </el-form-item>
            <el-form-item label="是否新品">
              <el-select v-model="info.isNew" placeholder="请选择">
                <el-option
                  v-for="item in newList"
                  :key="item.code"
                  :label="item.name"
                  :value="item.code"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="产品规格">
              <el-transfer
                v-model="info.specification"
                filterable
                :titles="['可选规格', '已选规格']"
                :data="specificationList"
              />
            </el-form-item>
            <el-form-item label="宣传主图(宽750px,高750px,200K内)">
              <el-row class="image-row" :gutter="20">
                <el-col :span="12">
                  <single-upload-image
                    v-model="info.images[0].image"
                    :init-image="info.images[0].imageUrl"
                    :image-height="150"
                  />
                </el-col>
                <el-col :span="12">
                  <single-upload-image
                    v-model="info.images[1].image"
                    :init-image="info.images[1].imageUrl"
                    :image-height="150"
                  />
                </el-col>
              </el-row>
              <el-row class="image-row" :gutter="20">
                <el-col :span="12">
                  <single-upload-image
                    v-model="info.images[2].image"
                    :init-image="info.images[2].imageUrl"
                    :image-height="150"
                  />
                </el-col>
                <el-col :span="12">
                  <single-upload-image
                    v-model="info.images[3].image"
                    :init-image="info.images[3].imageUrl"
                    :image-height="150"
                  />
                </el-col>
              </el-row>
              <el-row class="image-row" :gutter="20">
                <el-col :span="12">
                  <single-upload-image
                    v-model="info.images[4].image"
                    :init-image="info.images[4].imageUrl"
                    :image-height="150"
                  />
                </el-col>
                <el-col :span="12">
                  <single-upload-image
                    v-model="info.images[5].image"
                    :init-image="info.images[5].imageUrl"
                    :image-height="150"
                  />
                </el-col>
              </el-row>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item>
              <template slot="label">
                <span title="宽750像素，高度不限建议单张大小控制在200K左右"
                  >详情内容长图(宽750px,高不限,200K内)</span
                >
              </template>
              <single-upload-image
                v-model="info.content[0].image"
                class="image-row"
                :init-image="info.content[0].imageUrl"
                :image-height="150"
              />
              <single-upload-image
                v-model="info.content[1].image"
                class="image-row"
                :init-image="info.content[1].imageUrl"
                :image-height="150"
              />
              <single-upload-image
                v-model="info.content[2].image"
                class="image-row"
                :init-image="info.content[2].imageUrl"
                :image-height="150"
              />
              <single-upload-image
                v-model="info.content[3].image"
                class="image-row"
                :init-image="info.content[3].imageUrl"
                :image-height="150"
              />
              <single-upload-image
                v-model="info.content[4].image"
                class="image-row"
                :init-image="info.content[4].imageUrl"
                :image-height="150"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
    </el-main>
  </el-container>
</template>

<script>
import SingleUploadImage from '../../../components/SingleUploadImage'

export default {
  components: { SingleUploadImage },
  data() {
    return {
      isReady: false,
      code: null,
      fromPath: null,
      info: {
        title: '',
        subTitle: '',
        order: 0,
        konkaProductCode: '',
        productCategoryCode: '',
        mainImage: '',
        mainImageUrl: '',
        isHot: 20,
        isRecommend: 20,
        isNew: 20,
        images: [
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
        ],
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
          }
        ],
        specification: []
      }
    }
  },
  asyncData({ app }) {
    return Promise.all([
      app.$axios.get('/admin/select/product-category-tree'),
      app.$axios.get('/admin/select/specification-combination'),
      app.$axios.get('/admin/select/product-recommend'),
      app.$axios.get('/admin/select/product-hot'),
      app.$axios.get('/admin/select/product-new')
    ])
      .then(responses => {
        return {
          isReady: true,
          productCategoryList: responses[0].data,
          specificationList: responses[1].data,
          recommendList: responses[2].data,
          hotList: responses[3].data,
          newList: responses[4].data
        }
      })
      .catch(() => {
        return {
          isReady: true,
          productCategoryList: [],
          specificationList: [],
          recommendList: [],
          hotList: [],
          newList: []
        }
      })
  },
  created() {
    this.code =
      this.$route.params.code !== undefined ? this.$route.params.code : null
    this.code !== null && this.loadInfo()
  },
  methods: {
    saveInfo() {
      this.$axios
        .request({
          url: '/admin/product' + (this.code === null ? '' : '/' + this.code),
          method: this.code === null ? 'post' : 'put',
          data: this.info
        })
        .then(response => {
          this.$message.success(response.message)
          if (this.code === null) {
            this.$router.push('/products/down-list')
          } else {
            this.$router.back()
          }
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadInfo() {
      this.$axios
        .get('/admin/product/' + this.code)
        .then(response => {
          const info = response.data
          const images = []
          for (let i = 0; i < 6; i++) {
            images.push(
              info.images[i] !== undefined
                ? info.images[i]
                : {
                    image: '',
                    imageUrl: ''
                  }
            )
          }
          info.images = images
          const content = []
          for (let i = 0; i < 5; i++) {
            content.push(
              info.content[i] !== undefined
                ? info.content[i]
                : {
                    image: '',
                    imageUrl: ''
                  }
            )
          }

          info.content = content
          this.info = info
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped>
.page-form {
  width: 1100px;
  margin: 0 auto;
  display: block;
}

.image-row {
  clear: both;
  margin-bottom: 20px;
}
</style>
