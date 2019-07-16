<template>
  <el-container v-loading="isReady">
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>系统设置</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="saveInfo"
          >保存</el-button
        >
      </span>
    </el-header>
    <el-main>
      <el-form :model="settings" class="page-form">
        <el-tabs type="card">
          <el-tab-pane label="合伙人模块参数">
            <el-row :gutter="20">
              <el-col :span="8">
                <el-form-item label="合伙人入驻协议">
                  <el-input
                    v-model="settings.partnerProtocol"
                    resize="none"
                    :rows="20"
                    type="textarea"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="4">
                <el-form-item label="K币兑换比例">
                  <el-input
                    v-model="settings.partnerExchangeProportion"
                    type="number"
                    placeholder="100K币可以兑换多少钱"
                  >
                    <template slot="prepend">
                      100K币:
                    </template>
                    <template slot="append">
                      元
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col :span="3">
                <el-form-item label="一级基础返佣比例">
                  <el-input
                    v-model="settings.partnerFirstPercentage"
                    type="number"
                    step="0.01"
                    placeholder="最多支持两位描述"
                  >
                    <template slot="append">
                      %
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col :span="3">
                <el-form-item label="二级基础返佣比例">
                  <el-input
                    v-model="settings.partnerSecondPercentage"
                    type="number"
                    step="0.01"
                    placeholder="最多支持两位描述"
                  >
                    <template slot="append">
                      %
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col :span="3">
                <el-form-item label="开放自主注册">
                  <el-switch
                    v-model="settings.isAllowSelfRegisterPartner"
                    active-text="开放"
                    inactive-text="关闭"
                    :active-value="20"
                    :inactive-value="10"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="3">
                <el-form-item label="开放推荐注册">
                  <el-switch
                    v-model="settings.isAllowRecommendRegisterPartner"
                    active-text="开放"
                    inactive-text="关闭"
                    :active-value="20"
                    :inactive-value="10"
                  />
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
          <el-tab-pane label="其他参数">
            <el-row :gutter="20">
              <el-col :span="8">
                <el-form-item label="帮助中心单页编号">
                  <el-input
                    v-model="settings.helpCenterArticleCode"
                    placeholder="请输入单页编号"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="8">
                <el-form-item label="全局转发显示标题">
                  <el-input
                    v-model="settings.globalShareTitle"
                    placeholder="请输入全局转发显示标题"
                  />
                </el-form-item>
                <el-form-item label="全局转发显示图片">
                  <single-upload-image
                    v-model="settings.globalShareImage"
                    :init-image="settings.globalShareImageUrl"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="8">
                <el-form-item label="首页热卖单品标题图片(750px*96px)">
                  <single-upload-image
                    v-model="settings.uiIndexHotTitle"
                    :init-image="settings.uiIndexHotTitleUrl"
                  />
                </el-form-item>
                <el-form-item label="首页新品推荐标题图片(750px*96px)">
                  <single-upload-image
                    v-model="settings.uiIndexNewTitle"
                    :init-image="settings.uiIndexNewTitleUrl"
                  />
                </el-form-item>
                <el-form-item label="首页人气榜单标题图片(750px*96px)">
                  <single-upload-image
                    v-model="settings.uiIndexPopularityTitle"
                    :init-image="settings.uiIndexPopularityTitleUrl"
                  />
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
        </el-tabs>
      </el-form>
    </el-main>
  </el-container>
</template>

<script>
import SingleUploadImage from '../../components/SingleUploadImage'
export default {
  components: { SingleUploadImage },
  data() {
    return {
      isReady: false,
      settings: {
        partnerProtocol: '',
        partnerFirstPercentage: 0,
        partnerSecondPercentage: 0,
        partnerExchangeProportion: 100,
        isAllowSelfRegisterPartner: 10,
        isAllowRecommendRegisterPartner: 10,
        helpCenterArticleCode: ''
      }
    }
  },
  mounted() {
    this.loadInfo()
  },
  methods: {
    loadInfo() {
      this.$axios
        .get('/admin/settings')
        .then(response => {
          this.settings = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    saveInfo() {
      this.$axios
        .put('/admin/settings', this.settings)
        .then(response => {
          this.$message.success(response.message)
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped></style>
