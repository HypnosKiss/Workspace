<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <scroll-view id="message-list-box" class="position-relative occupy-all" scroll-y="true" :scroll-top="scrollTop">
                <div class="dialogue-histories">
                    <!-- <div class="date-time">
                        <div class="date-time-text">周三 10:21</div>
                    </div> -->
                    <div v-for="(each, n) in dialogues" :key="n" :class="['dialogue', fromMap[each.type]]">
                        <div class="user-header">
                            <image :src="each.avatar" />
                        </div>
                        <div class="dialogue-content" v-if="each.messageType == 10">
                            {{each.content}}
                        </div>
                        <div class="dialogue-content" v-if="each.messageType == 20">
                            <img :src="each.contentUrl"/>
                        </div>
                    </div>
                </div>
            </scroll-view>
        </div>
        <div class="page-bottom">
            <div class="input-area">
                <div class="message-input">
                    <input type="text" v-model="message" />
                </div>
                <div class="choose-image">
                    <i class="icon iconfont icon-fasongtupian-" @click="chooseImage"></i>
                </div>
                <div class="send-message">
                    <button @click="sendMessage">发送</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'
  import List from '@/mixins/list.js'
  export default {
    mixins: [Basic, List],
    components: {
    },
    data () {
      return {
        scrollTop: 0,
        fromMap: {
          10: 'myself',
          20: 'others'
        },
        dialogues: [],
        message: '',
        imgUrl: '',
        getListPath: '/customer-service-message'
      }
    },
    mounted () {
      this.interval = setInterval(this.getMessageList, 5000)
    },
    onUnload () {
      clearInterval(this.interval)
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.getMessageList()
        that.scrollToBottom()
        that.defaultList()
        that.startLoading()
      },
      sendMessage () {
        if (!this.message) {
          return
        }
        this.sendUserMessage(this.message, 10)
        let dialogues = this.$utils.cloneObject(this.dialogues)
        dialogues.push({
          type: 10,
          userHeader: 'http://img5.imgtn.bdimg.com/it/u=3906842785,3298929596&fm=26&gp=0.jpg',
          messageType: 10,
          content: this.message
        })
        this.dialogues = dialogues

        this.scrollToBottom()
      },
      sendPicture (imageNames = []) {
        if (imageNames.length === 0) {
          return
        }
        let dialogues = this.$utils.cloneObject(this.dialogues)
        dialogues.push({
          type: 10,
          contentUrl: this.imgUrl,
          messageType: 20,
          content: this.message
        })
        this.dialogues = dialogues
        this.scrollToBottom()
        // 调用接口将内容存到数据库中，并返回处理后可访问的图片地址
        this.sendUserMessage(imageNames, 20)
      },
      // 选择图片
      chooseImage () {
        let that = this
        wx.chooseImage({
          count: 1,
          sizeType: ['compressed'],
          sourceType: ['album', 'camera'],
          success: async (res) => {
            let images = res.tempFilePaths
            for (let n = 0, length = images.length; n < length; n++) {
              let name = await that.uploadImage(images[n])
              that.sendPicture(name)
            }
          }
        })
      },
      // 上传图片
      uploadImage (imagePath) {
        let that = this
        // 添加配置参数
        return new Promise((resolve, reject) => {
          let host = process.env.NODE_ENV === 'production' ? `https://kj-api.jovo.com.cn` : `http://konka-api.test`
          wx.uploadFile({
            url: `${host}/api/upload-image`,
            filePath: imagePath,
            name: 'file',
            success (res) {
              // console.log(res);
              if (res.statusCode === 200) {
                let body = JSON.parse(res.data)
                let data = body.data
                // console.log(data.filename)
                that.imgUrl = data.url
                resolve(data.filename)
              } else {
                console.log('错误1')
                reject(res.errMsg)
              }
            },
            fail (err) {
              console.log('错误2')
              reject(err)
            }
          })
        })
      },
      // 跳转到底部
      scrollToBottom () {
        wx.createSelectorQuery().select('#message-list-box').boundingClientRect(function (rect) {
          this.scrollTop = rect.bottom
        }.bind(this)).exec()
      },
      //   留言记录
      getMessageList () {
        let that = this
        let options = {
          url: '/customer-service-message',
          method: 'get'
        }
        that.$utils.requestServer(options)
          .then(res => {
            that.dialogues = res.data.reverse()
            this.scrollToBottom()
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      },
      //   发送留言
      sendUserMessage (content, messageType) {
        let that = this
        let options = {
          url: '/customer-service-message',
          method: 'post',
          data: {content: content, messageType: messageType}
        }
        that.$utils.requestServer(options)
          .then(res => {
            this.message = ''
            this.getMessageList()
          })
          .catch(err => {
            that.$utils.error(`获取失败，${err}`)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .page, .page-main {
        background-color: red;
    }
    .page-main {
        height: 100%;
        box-sizing: border-box;
        background-color: #f7f7f7;
    }
    .dialogue-histories {
        padding-top: 30px;
    }
    .date-time {
        padding-bottom: 30px;
        text-align: center;
    }
    .date-time-text {
        margin: auto;
        width: max-content;
        height: 40px;
        line-height: 40px;
        padding: 0 20px;
        font-size: 22px;
        color: #ffffff;
        background-color: #cccccc;
        border-radius: 4px;
    }
    .dialogue {
        @extend .align-item-start;
        padding-bottom: 40px;
        padding-left: 30px;
        padding-right: 30px;
        box-sizing: border-box;

        .user-header {
            /*margin-right: 20px;*/
        }
        .dialogue-content {
            font-size: 30px;
            word-break: break-all;
            max-width: 440px;
            padding: 25px 20px;
            box-sizing: border-box;
            border-radius: 8px;
        }
        .dialogue-content image {
            max-width: 300px;
            max-height: 300px;
        }
    }
    .dialogue.others {
        @extend .row-flex;

        .user-header {
            margin-right: 20px;
        }
        .dialogue-content {
            color: #33302b;
            background-color: #ffffff;
            border: 1px solid #e5e5e5;
        }
    }
    .dialogue.myself {
        @extend .row-reverse-flex;

        .user-header {
            margin-left: 20px;
        }
        .dialogue-content {
            color: #ffffff;
            background-color: #37bc9b;
            border: 1px solid #37bc9b;
        }
    }

    .input-area {
        @extend .position-relative;
        @extend .occupy-all;
        @extend .row-flex;
        @extend .align-item-center;

        padding: 18px 20px;
        box-sizing: border-box;

        .message-input {
            @extend .elastic-flex-item;
            @extend .position-relative;
            height: 60px;
            border-bottom: 1px solid #cccccc;
            box-sizing: border-box;
            overflow: hidden;
        }
        .message-input input {
            @extend .position-relative;
            width: 100%;
            height: 100%;
            font-size: 28px;
            box-sizing: border-box;
        }

        .choose-image {
            margin-left: 5px;
        }
        .choose-image .iconfont {
            font-size: 70px;
            color: $light_black;
        }

        .send-message button {
            margin-left: 20px;
            width: 100px;
            height: 60px;
            line-height: 60px;
            padding: 0;
            font-size: 30px;
            border-radius: 4px;
            text-align: center;
            color: #ffffff;
            background-color: #37bc9b;
        }
    }
</style>
