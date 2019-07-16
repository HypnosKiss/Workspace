<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="consignee" v-for="(each, n) in invoices" :key="n">
                <div class="base-info" @click="chooseInvoice(each)">
                    <div class="row-flex align-item-center bottom-gap-20">
                        <span class="name">{{each.unitName}}</span>
                    </div>
                    <div class="row-flex align-item-center address">
                        {{each.taxTicketAddress}}
                    </div>
                </div>
                <div class="functions">
                    <div class="fixed-flex-item row-flex align-item-center" v-if="each.isDefault === 10">
                        <i class="icon iconfont icon-xuanze-xuanzhong-"></i>
                        <span>默认发票</span>
                    </div>
                    <div class="set-default" @click="setDefaultInvoice(each)" v-if="each.isDefault === 20">
                        <i class="icon iconfont icon-xuanze-weixuanzhong-"></i>
                        <span>设为默认发票</span>
                    </div>
                    <div class="elastic-flex-item row-flex align-item-center justify-end">
                        <div class="operate">
                            <i class="icon iconfont icon-bianji-"></i>
                            <span @click="toInvoiceEdit(each)">编辑</span>
                        </div>
                        <div class="operate">
                            <i class="icon iconfont icon-shanchu-"></i>
                            <span @click="deleteInvoice(each)">删除</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-bottom">
            <button class="submit-btn" @click="toInvoiceEdit">新建发票</button>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    data () {
      return {
        invoices: []
      }
    },
    methods: {
      mountedNextTick () {
      },
      // 获取发票列表
      getInvoices () {
        let that = this
        let options = {
          url: `/invoice`,
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.invoices = data
          })
          .catch(err => {
            that.$utils.error(`获取发票列表失败，${err}`)
          })
      },
      setDefaultInvoice (info = {}) {
        let that = this
        that.$utils.showLoading()
        let options = {
          url: `/invoice/${info.code}/default`,
          method: 'put'
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.getInvoices()
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`设置默认发票失败，${err}`)
          })
      },
      async deleteInvoice (info = {}) {
        let that = this
        if (!(await that.$utils.confirm('确定删除所选？'))) {
          return
        }
        that.$utils.showLoading()
        let options = {
          url: `/invoice/${info.code}`,
          method: 'delete'
        }
        that.$utils.requestServer(options)
          .then(data => {
            console.log(data)
            that.$utils.hideLoading()
            that.getInvoices()
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`删除发票失败，${err}`)
          })
      },
      toInvoiceEdit (info = {}) {
        let code = info.code || ''
        this.$utils.navigateTo('/pages/user/invoice-edit/main', {
          code: code
        })
      },
      chooseInvoice (info = {}) {
        if (!this.query.ordering) {
          return
        }
        this.$store.commit('setInvoice', info)
        this.$utils.navigateBack()
      }
    },
    onShow () {
      let that = this
      that.getInvoices()
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
