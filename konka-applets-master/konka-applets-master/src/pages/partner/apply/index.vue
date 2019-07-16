<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="section">
                <div class="section-title">
                    <span class="font-30 font-deep-black right-padding">资质上传</span>
                    <span class="font-26 font-light-black">注：每张照片不能超过5M</span>
                </div>
                <div class="section-content">
                    <div class="upload-line">
                        <div class="operate">
                            <div class="image-upload">
                                <image-upload
                                        ref="img1"
                                        :limit="1"
                                        :width="270"
                                        :height="180"
                                        :noMargin="true"
                                        @change="changeImages1"></image-upload>
                            </div>
                            <div class="message">身份证正面</div>
                        </div>
                        <div class="example">
                            <div class="image-show">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800" />
                            </div>
                            <div class="message">示例</div>
                        </div>
                    </div>
                    <div class="upload-line">
                        <div class="operate">
                            <div class="image-upload">
                                <image-upload
                                        ref="img2"
                                        :limit="1"
                                        :width="270"
                                        :height="180"
                                        :noMargin="true"
                                        @change="changeImages2"></image-upload>
                            </div>
                            <div class="message">身份证反面</div>
                        </div>
                        <div class="example">
                            <div class="image-show">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800" />
                            </div>
                            <div class="message">示例</div>
                        </div>
                    </div>
                    <div class="upload-line">
                        <div class="operate">
                            <div class="image-upload">
                                <image-upload
                                        ref="img3"
                                        :limit="1"
                                        :width="270"
                                        :height="180"
                                        :noMargin="true"
                                        @change="changeImages3"></image-upload>
                            </div>
                            <div class="message">营业执照</div>
                        </div>
                        <div class="example">
                            <div class="image-show">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800" />
                            </div>
                            <div class="message">示例</div>
                        </div>
                    </div>
                    <div class="upload-line">
                        <div class="operate">
                            <div class="image-upload">
                                <image-upload
                                        ref="img4"
                                        :limit="1"
                                        :width="270"
                                        :height="180"
                                        :noMargin="true"
                                        @change="changeImages4"></image-upload>
                            </div>
                            <div class="message">店面照片</div>
                        </div>
                        <div class="example">
                            <div class="image-show">
                                <image src="https://www.konka.com/public/images/59/dc/c2/caa9eea7b6172baa5852421f0fc10d4d576aebb3.jpg?19336_OW800_OH800" />
                            </div>
                            <div class="message">示例</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="button-group" @click="send">
                <button>立即申请</button>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import ImageUpload from '@/components/image-upload.vue'

  export default {
    mixins: [Basic],
    components: {
      ImageUpload
    },
    onLoad (options) {
    },
    data () {
      return {
        info: {
          name: '',
          phone: '',
          provinceText: '',
          cityText: '',
          countyText: '',
          address: '',
          companyName: ''
        },
        images: ''
      }
    },
    computed: {
    },
    methods: {
      mountedNextTick () {
        this.info = this.query.info
        console.log(this.info)
        this.info.images = []
        this.$refs['img1'].reset()
        this.$refs['img2'].reset()
        this.$refs['img3'].reset()
        this.$refs['img4'].reset()
        // console.log(this.info)
      },
      changeImages1 (images = []) {
        // console.log('akm===>', images[0].filename)
        let o = {
          type: 'idCardFront',
          content: images[0].filename
        }
        this.info.images[0] = o
        console.log(o, this.info.images[0])
      },
      changeImages2 (images = []) {
        // console.log('akm===>', images)
        this.info.images[1] = {
          type: 'idCardObverse',
          content: images[0].filename
        }
      },
      changeImages3 (images = []) {
        // console.log('akm===>', images)
        this.info.images[2] = {
          type: 'businessLicense',
          content: images[0].filename
        }
      },
      changeImages4 (images = []) {
        // console.log('akm===>', images)
        this.info.images[3] = {
          type: 'storefront',
          content: images[0].filename
        }
      },
      send () {
        let that = this
        // for (let i in this.info.images) {
        //   if (this.info.images[i].content === '') {
        //     this.$utils.error(`请将资料上传完整`)
        //     return
        //   }
        // }
        for (let n = 0; n < 4; n++) {
          if ((!this.info.images[n]) || (this.info.images[n].content === '')) {
            this.$utils.error(`请将资料上传完整`)
            return
          }
        }

        let options = {
          url: '/partner-application',
          method: 'post',
          data: that.info
        }
        that.$utils.showLoading()
        that.$utils.requestServer(options)
          .then(data => {
            that.$utils.hideLoading()
            console.log(data)
            that.$utils.reLaunchTo('/pages/index/main')
          })
          .catch(err => {
            that.$utils.hideLoading()
            that.$utils.error(`申请失败，${err}`)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: #ffffff;
    }
    .section {
        padding: 30px;
        box-sizing: border-box;
    }
    .section-title {
        margin-bottom: 40px;
    }
    .upload-line {
        @extend .row-flex;
        @extend .justify-around;

        margin-top: 20px;

        .operate, .example {
            @extend .fixed-flex-item;
            @extend .column-flex;

            width: 270px;
        }
        .image-upload, .image-show {
            position: relative;
            width: 270px;
            height: 180px;
            border: 1px solid #cccccc;
            background-color: #fafafa;
        }
        .message {
            padding: 20px 0;
            font-size: 26px;
            color: $black;
            text-align: center;
        }
        .image-show image {
            position: absolute;
            top: 50%;
            left: 50%;
            max-width: 100%;
            max-height: 100%;
            transform: translate(-50%, -50%);
        }
    }

    .button-group {
        padding: 30px;
        box-sizing: border-box;

        button {
            width: 100%;
            height: 88px;
            line-height: 88px;
            margin: 0;
            margin-bottom: 20px;
            font-size: 28px;
            border-radius: 4px;
            border: 1px solid $theme_sub_color;
            box-sizing: border-box;
        }
        button:nth-of-type(1) {
            background-color: $theme_sub_color;
            color: #ffffff;
        }
        button:nth-of-type(2) {
            background-color: #ffffff;
            color: $theme_sub_color;
        }
    }
</style>
