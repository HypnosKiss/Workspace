<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="consignee" v-for="(each, n) in addresses" :key="n">
                <div class="base-info" @click="chooseAddress(each)">
                    <div class="row-flex align-item-center bottom-gap-20">
                        <span class="name">{{each.name}}</span>
                        <span class="phone">{{each.phone}}</span>
                    </div>
                    <div class="row-flex align-item-center address">
                        {{each.provinceText}}{{each.cityText}}{{each.countyText}}{{each.address}}
                    </div>
                </div>
                <div class="functions">
                    <div class="fixed-flex-item row-flex align-item-center" v-if="each.status === 10">
                        <i class="icon iconfont icon-xuanze-xuanzhong-"></i>
                        <span>默认地址</span>
                    </div>
                    <div class="set-default" @click="setDefaultAddress(each)" v-if="each.status === 20">
                        <i class="icon iconfont icon-xuanze-weixuanzhong-"></i>
                        <span>设为默认地址</span>
                    </div>
                    <div class="elastic-flex-item row-flex align-item-center justify-end">
                        <div class="operate">
                            <i class="icon iconfont icon-bianji-"></i>
                            <span @click="toAddressEdit(each)">编辑</span>
                        </div>
                        <div class="operate">
                            <i class="icon iconfont icon-shanchu-"></i>
                            <span @click="deleteAddress(each)">删除</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-bottom">
            <button class="submit-btn" @click="toAddressEdit()">新建地址</button>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    data () {
      return {
        addresses: []
      }
    },
    methods: {
      mountedNextTick () {
      },
      // 获取地址列表
      getAddresses () {
        let that = this
        let options = {
          url: `/addresses`,
          method: 'get'
        }
        that.$utils.showLoading()
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.addresses = data
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`获取地址列表失败，${err}`)
          })
      },
      setDefaultAddress (info = {}) {
        let that = this
        that.$utils.showLoading()
        let options = {
          url: `/addresses/${info.code}/status`,
          method: 'put'
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.getAddresses()
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`设置默认地址失败，${err}`)
          })
      },
      async deleteAddress (info = {}) {
        let that = this
        if (!(await that.$utils.confirm('确定删除所选？'))) {
          return
        }
        let options = {
          url: `/addresses/${info.code}`,
          method: 'delete'
        }
        that.$utils.showLoading()
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.getAddresses()
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`删除地址失败，${err}`)
          })
      },
      toAddressEdit (info = {}) {
        let code = info.code || ''
        this.$utils.navigateTo('/pages/user/address-edit/main', {
          code: code
        })
      },
      chooseAddress (info = {}) {
        if (!this.query.ordering) {
          return
        }
        this.$store.commit('setAddress', info)
        this.$utils.navigateBack()
      }
    },
    onShow () {
      let that = this
      that.getAddresses()
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

    .consignee {
        @extend .column-flex;
        @extend .align-item-stretch;
    }

    .submit-btn {
        line-height: 96px;
        font-size: 28px;
        color: #ffffff;
        background-color: $theme_color;
    }
</style>
