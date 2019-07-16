<template>
    <swiper
            :style="{width: width + 'rpx', height: height + 'rpx'}"
            :indicatorDots="indicatorDots"
            :indicatorColor="indicatorColor"
            :indicatorActiveColor="indicatorActiveColor"
            :autoplay="autoplay"
            :interval="interval"
            :duration="duration"
            :circular="circular">
        <block v-for="(item, index) in images" :index="index" :key="index">
            <swiper-item>
                <div class="banner-item">
                    <image :src="item.contentUrl || item.imageUrl || ''" class="banner-item-image" @click="clickImage(item)"/>
                    <!--<image :src="item.image || ''" class="banner-item-image" @click="clickImage(item)" mode="widthFix"/>-->
                </div>
            </swiper-item>
        </block>
    </swiper>
</template>
<style lang="scss" scoped>
    .banners {
    }
    .banner-item {
        width: 100%;
        height: 100%;
    }
    .banner-item-image {
        width: 100%;
        height: 100%;
    }
</style>
<script>
  export default {
    props: {
      /**
       * banner相关
       */
      width: {
        type: Number,
        default () {
          return 750
        }
      },
      height: {
        type: Number,
        default () {
          return 360
        }
      },
      // 是否显示面板指示点
      indicatorDots: {
        type: Boolean,
        default () {
          return true
        }
      },
      // 指示点颜色
      indicatorColor: {
        type: String,
        default () {
          return 'rgba(255, 255, 255, 0.5)'
        }
      },
      // 当前选中的指示点颜色
      indicatorActiveColor: {
        type: String,
        default () {
          return 'rgba(255, 255, 255, 1)'
        }
      },
      // 是否自动切换
      autoplay: {
        type: Boolean,
        default () {
          return true
        }
      },
      // 自动切换时间间隔
      interval: {
        type: Number,
        default () {
          return 3000
        }
      },
      // 滑动动画时长
      duration: {
        type: Number,
        default () {
          return 500
        }
      },
      // 是否采用衔接滑动
      circular: {
        type: Boolean,
        default () {
          return true
        }
      },
      // 图片数组
      images: {
        type: Array,
        default () {
          return []
        }
      }
    },
    data () {
      return {
      }
    },
    computed: {
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
      })
    },
    methods: {
      clickImage (item) {
        this.$emit('clickImage', item)
      }
    }
  }
</script>

