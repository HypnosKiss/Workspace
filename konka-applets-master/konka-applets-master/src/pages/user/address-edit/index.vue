<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="info-list">
                <div class="info-row">
                    <div class="row-name">收件人</div>
                    <input class="row-value" placeholder="请输入收件人" :placeholder-style="placeholderStyle"
                           v-model="info.name"/>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="info-row">
                    <div class="row-name">手机号码</div>
                    <input class="row-value" placeholder="请输入手机号码" :placeholder-style="placeholderStyle"
                           v-model="info.phone"/>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="info-row">
                    <div class="row-name">所在地</div>
                    <picker class="address-picker"
                            mode="region"
                            @change="selectAddress"
                            :value="addressValue"
                    ><div :class="['row-value', {'no-address': !addressText}]">{{addressText || '请选择地址'}}</div>
                    </picker>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="info-row textarea-box">
                    <textarea class="row-value" placeholder="请输入详细地址" :placeholder-style="placeholderStyle"
                              v-model="info.address"></textarea>
                </div>
                <div class="info-row">
                    <div class="row-name">邮政编码</div>
                    <input class="row-value" placeholder="请输入邮政编码(选填)" :placeholder-style="placeholderStyle"
                           v-model="info.postalCode"/>
                </div>
                <div class="info-row">
                    <div class="row-name">设为默认</div>
                    <div class="row-value">
                        <switch :checked="info.status === 10" @change="changeStatus" color="#ed1c24"/>
                    </div>
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
          info: this.defaultInfo()
        }
      },
      computed: {
        placeholderStyle () {
          return 'color: #cccccc;'
        },
        addressText () {
          let result = ''
          if (this.info.provinceText || this.info.cityText || this.info.countyText) {
            result = `${this.info.provinceText} ${this.info.cityText} ${this.info.countyText}`
          }
          return result
        },
        addressValue () {
          return [this.info.provinceText, this.info.cityText, this.info.countyText]
        }
      },
      methods: {
        selectAddress (e) {
          this.info.provinceText = e.mp.detail.value[0]
          this.info.cityText = e.mp.detail.value[1]
          this.info.countyText = e.mp.detail.value[2]
          this.info.postalCode = e.mp.detail.postcode
          this.info.provinceCode = e.mp.detail.code[0]
          this.info.cityCode = e.mp.detail.code[1]
          this.info.countyCode = e.mp.detail.code[2]
        },
        mountedNextTick () {
          let that = this
          that.getAddress()
          this.$store.dispatch('getWxSetting')
        },
        defaultInfo () {
          return {
            name: '', // 收货人名称
            phone: '', // 收货人电话
            provinceCode: '',
            provinceText: '', // 省
            cityCode: '',
            cityText: '', // 市
            countyCode: '',
            countyText: '', // 县
            address: '', // 详细地址
            postalCode: '', // 邮政编码
            status: 10 // 是否默认
          }
        },
        // 获取地址
        getAddress () {
          let that = this
          console.log('code=====>', that.query.code)
          if (!that.query.code) {
            return
          }
          let options = {
            url: `/addresses/${that.query.code}`,
            method: 'get'
          }
          that.$utils.requestServer(options)
            .then(data => {
              console.log(data)
              that.info = data
            })
            .catch(err => {
              that.$utils.error(`获取地址失败，${err}`)
            })
        },
        afterOpenSetting (res) {
          let detail = res.mp.detail

          if (detail.errMsg !== 'openSetting:ok') {
            this.$utils.error('设置授权出错')
            return
          }
          if (detail.authSetting[`scope.address`]) {
            this.$store.dispatch('getWxSetting')
            this.chooseAddress()
            return
          }
          this.$utils.error('请授权小程序使用微信地址')
        },
        chooseAddress () {
          let that = this
          wx.chooseAddress({
            success (res) {
              that.info = Object.assign(that.info, {
                name: res.userName,
                phone: res.telNumber,
                provinceText: res.provinceName,
                cityText: res.cityName,
                countyText: res.countyName,
                address: res.detailInfo,
                postalCode: res.postalCode,
                status: 10
              })
            }
          })
        },
        changeStatus () {
          let info = this.$utils.cloneObject(this.info)
          info.status = (info.status === 10)
        },
        // 保存地址
        submit () {
          let that = this
          // that.$utils.showModal('点击了保存')

          let options = that.query.code ? {
            url: `/addresses/${that.query.code}`,
            method: 'put',
            data: that.info
          } : {
            url: `/addresses`,
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
              that.$utils.error(`保存地址信息失败，${err}`)
            })
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

    .address-picker {
        width: 500px;
        margin-left: 50px;
    }

    .page, .page-main {
        background-color: #f7f7f7;
    }

    .page-main {
        overflow: hidden;
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

    button.row-value {
        background-color: transparent;
    }

    .info-row .row-value.no-address {
        color: #cccccc;
    }

    .textarea-box {
        height: max-content;
        padding: 30px 30px 30px 0;
    }

    .textarea-box textarea {
        height: 130px;
        text-align: left;
    }

    .submit-btn {
        line-height: 96px;
        font-size: 28px;
        color: #ffffff;
        background-color: $theme_color;
    }
</style>
