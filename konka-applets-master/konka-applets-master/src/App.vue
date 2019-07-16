<script>
  import Basic from '@/mixins/basic.js'
  import Uuidv4 from 'uuid/v4'

  export default {
    mixins: [Basic],
    created () {
      this.$store.dispatch('checkIpx')
      console.log(this.$store.state.Basic.isIpx)
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.$utils.setSessionId(Uuidv4())
        that.autoLogin()
        this.loadSetting()
      },
      autoLogin () {
        let that = this
        that.getWxCode()
          .then(code => {
            console.log('code===>', code)
            that.getWxOpenid(code)
              .then(data => {
                console.log('data===>', data)
                that.$utils.setOpenid(data)
                that.openidLogin(data)
                  .then(data => {
                    console.log('openidLogin===>', data)
                    that.$utils.setAccessToken(data)
                    that.$store.dispatch('getUserInfo')
                  })
                  .catch(err => {
                    console.log('err===>', err)
                  })
              })
              .catch(err => {
                console.log('err===>', err)
              })
          })
          .catch(err => {
            console.log('err===>', err)
          })
      },
      loadSetting () {
        this.$utils.requestServer({
          url: '/setting',
          method: 'get'
        }).then(response => {
          this.$store.commit('setSystemSetting', response)
        })
      }
    }
  }
</script>

<style src="../static/iconfont/font_963045_cq5r211utth.css"></style>
<!--<style lang="scss">-->
<!--@import "@/style/index.scss";-->
<!--</style>-->
