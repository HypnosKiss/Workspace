<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="content">
                <image v-for="(image,index) in info.contentUrl" mode="widthFix" :src="image" :key="index" />
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    data () {
      return {
        info: {}
      }
    },
    mounted () {
      if (this.$store.state.Basic.systemSetting.helpCenterArticleCode !== '') {
        this.loadInfo()
      } else {
        this.$utils.error(`此功能尚未开放`)
      }
    },
    methods: {
      loadInfo () {
        this.$utils.requestServer({
          url: '/articles/' + this.$store.state.Basic.systemSetting.helpCenterArticleCode,
          method: 'get'
        })
          .then(res => {
            this.info = res
          })
          .catch(err => {
            this.$utils.error(`获取失败，${err}`)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #f7f7f7;
    }
    .content {
        line-height: 0;
    }
    .content image {
        width: 750px;
    }
</style>
