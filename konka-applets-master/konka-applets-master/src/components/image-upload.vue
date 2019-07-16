<template>
    <div :class="['upload-box', 'justify-'+justify]">
        <div :class="['item', 'image', {'no-margin': noMargin}]" :style="{width: width+'rpx', height: height+'rpx'}" v-for="(each, n) in images" :key="n">
            <image class="thumb" :src="each.url" @click="chooseImage(n)" />
            <i class="icon iconfont icon-shanchu" v-if="xShow" @click="deleteImage(n)"></i>
        </div>
        <div :class="['item', 'camera', {'no-margin': noMargin}]" :style="{width: width+'rpx', height: height+'rpx'}" @click="() => { chooseImage (null)}" v-if="images.length < limit">
            <i class="icon iconfont icon-camera-s"></i>
        </div>
    </div>
</template>
<style lang="scss" scoped>
    .upload-box {
        @extend .row-flex;
        @extend .align-item-center;
        /*@extend .justify-end;*/
        @extend .flex-wrap;
        position: relative;
        width: 100%;
        height: 100%;
        text-align: right;
    }

    .item {
        /*width: 80px;*/
        /*height: 80px;*/
        line-height: 0;
        margin: 0 20px 40px 20px;
        /*background-color: #ffffff;*/
        border: 1px solid #e5e5e5;
        box-sizing: border-box;
        position: relative;
        display: inline-flex;

        flex-direction: row;
        align-items: center;
        justify-content: center;
    }

    .justify-start .item {
        margin: 0 40px 40px 0;
    }

    .justify-end .item {
        margin: 0 0 40px 40px;
    }
    .item.no-margin {
        margin: 0;
    }

    .item.image {
        background-color: $black;
    }

    .item.camera {
        border: 0;
    }

    .thumb {
        position: absolute;
        width: 100%;
        height: 100%;
    }

    .icon-camera-s {
        font-size: 80px;
        color: $light_black;
        z-index: 2;
    }

    .icon-bofang {
        font-size: 80px;
        color: #dbdbdb;
        z-index: 2;
    }

    .icon-shanchu {
        width: 40px;
        height: 40px;
        line-height: 40px;
        border-radius: 20px;
        position: absolute;
        top: 0;
        right: 0;
        text-align: center;
        font-size: 22px;
        color: $theme_sub_color;
        border: 1px solid $theme_sub_color;
        box-sizing: border-box;
        background-color: $white;
        transform: translate(50%, -50%);
        z-index: 2;
        display: inline-block;
    }
</style>
<script>
  export default {
    props: {
      width: {
        type: Number,
        default: 80
      },
      height: {
        type: Number,
        default: 80
      },
      noMargin: {
        type: Boolean,
        default: false
      },
      disabled: {
        type: Boolean,
        default: false
      },
      xShow: {
        type: Boolean,
        default: true
      },
      limit: {
        type: Number,
        default: 1
      },
      defaultImages: {
        type: Array,
        default () {
          return []
        }
      },
      justify: {
        type: String,
        default () {
          return 'start'
        }
      }
    },
    data () {
      return {
        images: [
          // {
          //   filename: '',
          //   url: ''
          // }
        ]
      }
    },
    computed: {},
    watch: {
      defaultImages (val, oldVal) {
        this.defaultImagesToImages()
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.defaultImagesToImages()
      },
      defaultImagesToImages () {
        this.images = JSON.parse(JSON.stringify(this.defaultImages))
      },
      reset () {
        this.images = []
      },
      chooseImage (index) {
        let that = this
        console.log(index)
        wx.chooseImage({
          count: 1,
          sizeType: ['compressed'],
          sourceType: ['album', 'camera'],
          success: async (res) => {
            console.log(res)
            console.log(JSON.stringify(res))
            let tempFilePaths = res.tempFilePaths
            let images = JSON.parse(JSON.stringify(that.images))

            if (tempFilePaths && tempFilePaths.length === 0) {
              return
            }
            try {
              // let name = (new Date()).getTime()
              let name = await that.uploadFile(tempFilePaths[0])
              let newImage = {
                filename: name,
                url: tempFilePaths[0]
              }
              if (index > -1 && index !== null) {
                console.log('替换')
                images.splice(index, 1, newImage)
              } else {
                console.log('添加')
                images.push(newImage)
              }
              that.images = images
              that.eventEmit()
            } catch (e) {
            }
          }
        })
      },
      uploadFile (filePath) {
        let that = this
        that.$utils.showLoading()
        return new Promise((resolve, reject) => {
          let host = this.$utils.requestUrl()
          wx.uploadFile({
            url: `${host}/api/upload-image`,
            filePath: filePath,
            name: 'file',
            success (res) {
              // console.log(res);
              that.$utils.hideLoading()
              if (res.statusCode === 200) {
                let body = JSON.parse(res.data)
                let data = body.data
                resolve(data.filename)
              } else {
                console.log('错误1')
                reject(res.errMsg)
              }
            },
            fail (err) {
              that.$utils.hideLoading()
              console.log('错误2')
              that.$utils.error(`上传图片失败，${JSON.stringify(err)}`)
              reject(err)
            }
          })
        })
      },
      deleteImage (index) {
        this.images.splice(index, 1)
      },
      eventEmit () {
        this.$emit('change', this.images)
      }
    },
    components: {}
  }
</script>
