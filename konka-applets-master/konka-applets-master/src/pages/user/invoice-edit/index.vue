<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="invoice-type">
                <div class="title">发票类型</div>
                <div class="row-flex justify-between">
                    <button v-for="(each, n) in invoiceTypes" :key="n" :class="['type-item', {'active': info.invoiceType === each.key}]" @click="changeInvoiceType(each)">{{each.name}}</button>
                </div>
            </div>

            <!-- 普通发票 -->
            <div class="info-list" v-if="isGeneralInvoice">
                <div class="info-row">
                    <div class="row-name">发票信息</div>
                </div>
                <div class="info-row">
                    <div class="row-name">类别</div>
                    <div class="row-value row-flex align-item-center justify-end">
                        <div v-for="(each, n) in types" :key="n" :class="['radio-item', {'active': info.type === each.key}]" @click="changeType(each)">
                            <i class="icon iconfont icon-xuanze-weixuanzhong-"></i>
                            <i class="icon iconfont icon-xuanze-xuanzhong-"></i>
                            <span class="item-name">{{each.name}}</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-row" v-if="isPersonalType">
                    <div class="row-name">抬头</div>
                    <input class="row-value" placeholder="请输入抬头" :placeholder-style="placeholderStyle" v-model="info.unitName" />
                </div>
                <div class="info-row" v-if="isCompanyType">
                    <div class="row-name">名称</div>
                    <input class="row-value" placeholder="请输入单位名称" :placeholder-style="placeholderStyle" v-model="info.unitName" />
                </div>
                <div class="info-row" v-if="isCompanyType">
                    <div class="row-name">纳税人识别号</div>
                    <input class="row-value" placeholder="请输入纳税人识别号" :placeholder-style="placeholderStyle" v-model="info.taxTicket" />
                </div>
                <div class="info-row" v-if="isCompanyType">
                    <div class="row-name">地址</div>
                    <input class="row-value" placeholder="请输入税票地址" :placeholder-style="placeholderStyle" v-model="info.taxTicketAddress" />
                </div>
                <div class="info-row" v-if="isCompanyType">
                    <div class="row-name">电话</div>
                    <input class="row-value" placeholder="请输入税票电话" :placeholder-style="placeholderStyle" v-model="info.taxTicketPhone" />
                </div>
                <div class="info-row" v-if="isCompanyType">
                    <div class="row-name">开户行</div>
                    <input class="row-value" placeholder="请输入开户行" :placeholder-style="placeholderStyle" v-model="info.openBank" />
                </div>
                <div class="info-row" v-if="isCompanyType">
                    <div class="row-name">银行账号</div>
                    <input class="row-value" type="number" placeholder="请输入银行账号" :placeholder-style="placeholderStyle" v-model="info.bankAccount" />
                </div>
            </div>

            <!-- 增值税专用 -->
            <div class="info-list" v-if="isVATInvoice">
                <div class="info-row">
                    <div class="row-name">发票信息</div>
                </div>
                <div class="info-row">
                    <div class="row-name">名称</div>
                    <input class="row-value" placeholder="请输入单位名称" :placeholder-style="placeholderStyle" v-model="info.unitName" />
                </div>
                <div class="info-row">
                    <div class="row-name">纳税人识别号</div>
                    <input class="row-value" placeholder="请输入纳税人识别号" :placeholder-style="placeholderStyle" v-model="info.taxTicket" />
                </div>
                <div class="info-row">
                    <div class="row-name">地址</div>
                    <input class="row-value" placeholder="请输入税票地址" :placeholder-style="placeholderStyle" v-model="info.taxTicketAddress" />
                </div>
                <div class="info-row">
                    <div class="row-name">电话</div>
                    <input class="row-value" placeholder="请输入税票电话" :placeholder-style="placeholderStyle" v-model="info.taxTicketPhone" />
                </div>
                <div class="info-row">
                    <div class="row-name">开户行</div>
                    <input class="row-value" placeholder="请输入开户行" :placeholder-style="placeholderStyle" v-model="info.openBank" />
                </div>
                <div class="info-row">
                    <div class="row-name">银行账号</div>
                    <input class="row-value" placeholder="请输入银行账号" :placeholder-style="placeholderStyle" v-model="info.bankAccount" />
                </div>
                <div class="info-row">
                    <div class="row-name">发票邮寄地址</div>
                    <input class="row-value" placeholder="请输入邮寄地址" :placeholder-style="placeholderStyle" v-model="info.userAddressesCode" />
                </div>
            </div>
            <!-- 收件信息 -->
            <div class="info-list">
              <div class="info-row">
                <div class="row-name">收件信息</div>
              </div>
              <div class="info-row">
                <div class="row-name">材质类型</div>
                <div class="row-value row-flex align-item-center justify-end">
                  <div v-for="(each, n) in materialType" :key="n" :class="['radio-item', {'active': info.materialType === each.key}]" @click="changeMaterialType(each)">
                    <i class="icon iconfont icon-xuanze-weixuanzhong-"></i>
                    <i class="icon iconfont icon-xuanze-xuanzhong-"></i>
                    <span class="item-name">{{each.name}}</span>
                  </div>
                </div>
              </div>
              <!-- 电子 -->
              <div class="info-row" v-if="isElectronicInvoice">
                <div class="row-name">手机号码</div>
                <input class="row-value" type="number" placeholder="请输入电子发票通知手机号码" :placeholder-style="placeholderStyle" v-model="info.sendMobile" />
              </div>
              <div class="info-row" v-if="isElectronicInvoice">
                <div class="row-name">常用邮箱</div>
                <input class="row-value" placeholder="请输入电子发票发送邮箱" :placeholder-style="placeholderStyle" v-model="info.sendEmail" />
              </div>

              <!-- 纸质 -->
              <div class="info-row" v-if="isPaperInvoice">
                <div class="row-name">收货人名称</div>
                <input class="row-value" placeholder="请输入收货人名称" :placeholder-style="placeholderStyle" v-model="info.name" />
              </div>
              <div class="info-row" v-if="isPaperInvoice">
                <div class="row-name">收货人电话</div>
                <input class="row-value" placeholder="请输入收货人电话" :placeholder-style="placeholderStyle" v-model="info.phone" />
              </div>
              <div class="info-row" v-if="isPaperInvoice">
                <div class="row-name">收货人地区</div>
                <picker
                  class="row-value"
                  mode="region"
                  :value="region"
                  @change="bindRegionChange"
                >
                  <view v-if="region.length" class="picker">
                    {{region[0]}}，{{region[1]}}，{{region[2]}}
                  </view>
                  <view v-else class="picker row-value">
                    <span style="color: #ccc;">请选择收货人地区(省市县)</span>
                  </view>
                </picker>
              </div>
              <div class="info-row" v-if="isPaperInvoice">
                <div class="row-name">详细地址</div>
                <input class="row-value" placeholder="请输入收货人详细地址" :placeholder-style="placeholderStyle" v-model="info.address" />
              </div>
            </div>
        </div>
        <div class="page-bottom">
            <button class="submit-btn" @click="submit">保存</button>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    data () {
      return {
        invoiceTypes: [
          {
            key: 10,
            name: '普通发票'
          },
          {
            key: 20,
            name: '增值税专用发票'
          }
        ],
        types: [
          {
            key: 10,
            name: '个人'
          },
          {
            key: 20,
            name: '企业'
          }
        ],
        // 材质类型
        materialType: [{
          key: 10,
          name: '电子'
        }, {
          key: 20,
          name: '纸质'
        }],
        info: this.defaultInfo(),
        region: [], // 地址选择
        isPhone: /^1[34578]\d{9}$/, // 手机号和邮箱 正则
        // eslint-disable-next-line
        isEmail: /^([A-Za-z0-9_\-\.\u4e00-\u9fa5])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,8})$/
      }
    },
    computed: {
      placeholderStyle () {
        return 'color: #cccccc;'
      },
      // 普通发票
      isGeneralInvoice () {
        return this.info.invoiceType === 10
      },
      // 增值税专用发票
      isVATInvoice () {
        return this.info.invoiceType === 20
      },
      // 发票类别: 个人
      isPersonalType () {
        return this.info.type === 10
      },
      // 发票类别: 企业
      isCompanyType () {
        return this.info.type === 20
      },
      // 发票材质：电子
      isElectronicInvoice () {
        return this.info.materialType === 10
      },
      // 发票材质：纸质
      isPaperInvoice () {
        return this.info.materialType === 20
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.getInvoice()
      },
      defaultInfo () {
        return {
          invoiceType: 10, // 发票类型 普通，增值税专用
          // status: 10, // 状态
          type: 10, // 类型 个人，企业
          materialType: 20, // 材质类型 电子，纸质
          unitName: '', // 名称/抬头
          taxTicket: '', // 税票号
          taxTicketAddress: '', // 税票地址
          taxTicketPhone: '', // 税票电话
          openBank: '', // 开户行
          bankAccount: '', // 银行帐号
          userAddressesCode: '', // 地址编码
          name: '', // 收货人名称
          phone: '', // 收货人电话
          province: '', // 省
          city: '', // 市
          county: '', // 县
          address: '', // 详细地址
          sendMobile: '', // 电子发票通知手机号码
          sendEmail: '' // 电子发票发送邮箱
        }
      },
      // 获取发票
      getInvoice () {
        let that = this
        console.log('code=====>', that.query.code)
        if (!that.query.code) {
          return
        }
        let options = {
          url: `/invoice/${that.query.code}`,
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.info = data
          })
          .catch(err => {
            that.$utils.error(`获取发票失败，${err}`)
          })
      },
      changeInvoiceType (data = {}) {
        if (!data.key) {
          return
        }
        if (data.key === 20) {
          this.$utils.showToast('该功能正在开发中')
          return
        }
        let info = this.$utils.cloneObject(this.info)
        info.invoiceType = data.key
        this.info = info
      },
      changeType (data = {}) {
        if (!data.key) {
          return
        }
        let info = this.$utils.cloneObject(this.info)
        info.type = data.key
        this.info = info
      },
      changeMaterialType (data) {
        if (!data.key) {
          return
        }
        if (data.key === 10) {
          this.$utils.showToast('该功能正在开发中')
          return
        }
        let info = this.$utils.cloneObject(this.info)
        info.materialType = data.key
        // console.log('info.materialType', info.materialType)
        this.info = info
      },
      handleTypeChange () {
        // 抬头类型为个人时，unitName字段必填，其他发票内容字段隐藏
        if (this.info.type === 10) {
          if (!this.info.unitName) {
            this.$utils.showToast('请输入抬头')
            return false
          }
          return true
        }
        // 抬头类型为公司时，unitName、taxTicket字段必填，其他发票内容字段选填
        if (this.info.type === 20) {
          if (!this.info.unitName || !this.info.taxTicket) {
            this.$utils.showToast('请输入抬头和税号')
            return false
          }
          return true
        }
      },
      handleMaterialTypeChange () {
        // 当材质类型是电子发票的时候，sendEmail、sendMobile字段必填
        if (this.info.materialType === 10) {
          if (!this.info.sendMobile || !this.info.sendEmail) {
            this.$utils.showToast('请输入手机号码和邮箱地址')
            return false
          }
          if (!this.isPhone.test(this.info.sendMobile)) {
            this.$utils.showToast('请输入正确的手机号码')
            return false
          }
          if (!this.isEmail.test(this.info.sendEmail)) {
            this.$utils.showToast('请输入正确的邮箱')
            return false
          }
          return true
        }
        // 当材质类型是纸质发票的时候，name、phone、province、city、county、address字段必填
        if (this.info.materialType === 20) {
          if (!this.info.name) {
            this.$utils.showToast('请输入收货人名称')
            return false
          }
          if (!this.info.phone) {
            this.$utils.showToast('请输入收货人手机号码')
            return false
          }
          if (!this.isPhone.test(this.info.phone)) {
            this.$utils.showToast('请输入正确的手机号码')
            return false
          }
          if (this.region.length === 0) {
            this.$utils.showToast('请选择收货省市县(区)')
            return false
          }
          if (!this.info.address) {
            this.$utils.showToast('请输入详细收货地址')
            return false
          }
          return true
        }
      },
      submit () {
        if (!this.handleTypeChange() || !this.handleMaterialTypeChange()) {
          return
        }
        let that = this
        // that.$utils.showModal('点击了保存')

        let options = that.query.code ? {
          url: `/invoice/${that.query.code}`,
          method: 'put',
          data: that.info
        } : {
          url: `/invoice`,
          method: 'post',
          data: that.info
        }
        that.$utils.showLoading()
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.$utils.navigateBack()
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`保存发票信息失败，${err}`)
          })
      },
      // 地区更改
      bindRegionChange (e) {
        console.log('picker发送选择改变，携带值为', e.mp.detail.value)
        this.region = e.mp.detail.value
        this.info.province = this.region[0]
        this.info.city = this.region[1]
        this.info.county = this.region[2]
      }
    },
    onUnload () {
      // 后退和替换当前页的时候触发
      console.log('onUnload')
      // 清除结算产品缓存
      Object.assign(this.$data, this.$options.data())
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }
    .page-main {
        overflow: hidden;
    }

    .invoice-type {
        padding: 30px;
        box-sizing: border-box;
        background-color: #ffffff;

        .title {
            font-size: 28px;
            color: $deep_black;
            margin-bottom: 30px;
        }
    }
    .type-item {
        margin: 0;
        width: 330px;
        height: 60px;
        line-height: 60px;
        font-size: 24px;
        color: $light_black;
        background-color: #ffffff;
        border-radius: 4px;
        border: 1px solid #cccccc;
        box-sizing: border-box;
        transition: all 0.1s linear;
    }
    .type-item.active {
        color: $theme_color;
        border: 1px solid $theme_color;
    }

    .info-list {
        @extend .border-bottom;

        margin-top: 20px;
        padding-left: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }

    .info-row:nth-last-of-type(1) {
        border-bottom: 0;
    }

    .radio-item {
        margin-right: 80px;
    }
    .radio-item:nth-last-of-type(1) {
        margin-right: 0;
    }


    .submit-btn {
        line-height: 96px;
        font-size: 28px;
        color: #ffffff;
        background-color: $theme_color;
    }
</style>
