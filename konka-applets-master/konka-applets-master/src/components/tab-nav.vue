<template>
    <div class="nav row-flex align-item-stretch justify-around">
        <div class="elastic-flex-item column-flex align-item-center justify-center" v-for="(each, n) in nav" :index="index" :key="n" @click="changeTab(each)">
        <!--<div class="fixed-flex-item text-center" v-for="(each, n) in nav" :index="index" :key="key">-->
            <div class="occupy-all text-center" v-if="each.key !== navKey">
                <image class="nav-image" :src="each.image" />
                <div class="text-center font-24 font-light-black">
                    <span>{{each.name}}</span>
                </div>
            </div>
            <div class="occupy-all text-center" v-if="each.key === navKey">
                <image class="nav-image" :src="each.activeImage"/>
                <div class="text-center font-24 font-red">
                    <span>{{each.name}}</span>
                </div>
            </div>
        </div>
        <get-phone-number></get-phone-number>
    </div>
</template>
<style lang="scss" scoped>
    .nav {
        width: 100%;
        height: 100%;
        padding-top: 10px;
        box-sizing: border-box;
        background-color: $white;
    }

    .nav-image {
        width: 46px;
        height: 44px;
    }
</style>
<script>
  import Basic from '@/mixins/basic.js'
  import GetPhoneNumber from '@/components/get-phone-number.vue'
  import { mapGetters } from 'vuex'

  export default {
    mixins: [Basic],
    components: {
      GetPhoneNumber
    },
    props: {
      navKey: ''
    },
    data () {
      return {
        nav: [
          {
            key: 'index',
            name: '首页',
            image: require('@static/image/tab_home.png'),
            activeImage: require('@static/image/tab_home_active.png'),
            routeTo: '/pages/index/main'
          },
          {
            key: 'categories',
            name: '分类',
            image: require('@static/image/tab_category.png'),
            activeImage: require('@static/image/tab_category_active.png'),
            routeTo: '/pages/categories/main'
          },
          {
            key: 'cart',
            name: '购物车',
            image: require('@static/image/tab_cart.png'),
            activeImage: require('@static/image/tab_cart_active.png'),
            routeTo: '/pages/cart/main'
          },
          {
            key: 'mine',
            name: '我的',
            image: require('@static/image/tab_mine.png'),
            activeImage: require('@static/image/tab_mine_active.png'),
            routeTo: '/pages/user/index/main'
          }
        ]
      }
    },
    computed: {
      ...mapGetters([
        'userInfo'
      ])
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
      })
    },
    methods: {
      changeTab (info = {}) {
        let key = info.key
        if (['mine'].includes(key)) {
          if (!this.userInfo) {
            this.openGetPhoneNumber(info.routeTo)
          } else if (this.userInfo.avatar === '' || this.userInfo.nickname === '') {
            this.openGetPhoneNumber(info.routeTo)
          } else {
            this.routeTo(info)
          }
        } else {
          // 直接跳转
          this.routeTo(info)
        }
        // this.$emit('change', key)
      },
      routeTo (info = {}) {
        let path = info.routeTo
        if (!path) {
          return
        }
        this.$utils.redirectTo(path)
      }
    }
  }
</script>

