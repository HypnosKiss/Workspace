<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>客服留言管理</span>
      <span class="header-btn-box">
        <el-button type="primary" size="mini" plain @click="refresh"
          >立即刷新</el-button
        >
      </span>
    </el-header>
    <el-main>
      <div class="service-message-box">
        <div class="service-client-list">
          <div class="service-client-search">
            <el-input
              v-model="clientSearch"
              placeholder="搜索"
              size="small"
              :clearable="true"
              prefix-icon="el-icon-search"
            />
          </div>
          <div class="service-client-box">
            <div
              v-for="(client, index) in clientShowList"
              :key="index"
              :class="{ active: client.userCode === activeClient }"
              class="service-client-line"
              @click="selectClient(client.userCode)"
            >
              <div class="service-client-avatar">
                <el-badge
                  :value="client.unreadNum"
                  :hidden="client.unreadNum === 0"
                >
                  <img
                    v-if="client.user.avatar === ''"
                    src="../../assets/avatar.png"
                    alt=""
                  />
                  <img
                    v-if="client.user.avatar !== ''"
                    :src="client.user.avatar"
                    alt=""
                  />
                </el-badge>
              </div>
              <div class="service-client-name">
                {{ client.nickname }}({{ client.username }})
              </div>
              <div class="service-client-last-time">
                {{ client.lastSendTime }}
              </div>
              <div class="service-client-last-message">
                {{ client.lastMessage }}
              </div>
            </div>
          </div>
        </div>
        <div ref="messageList" class="service-message-list">
          <div
            v-for="(message, index) in messageList"
            :key="index"
            class="message-line"
            :class="{
              client: message.type === 10,
              service: message.type === 20
            }"
          >
            <div v-if="message.type === 10" class="message-avatar">
              <img
                v-if="message.avatar === ''"
                src="../../assets/avatar.png"
                alt=""
              />
              <img v-if="message.avatar !== ''" :src="message.avatar" alt="" />
            </div>
            <div v-if="message.type === 20" class="message-avatar">
              <img src="../../assets/avatar.png" alt="" />
            </div>
            <div v-if="message.messageType === 10" class="message-content">
              {{ message.content }}
            </div>
            <div v-if="message.messageType === 20" class="message-content">
              <img :src="message.contentUrl" alt="" />
            </div>
          </div>
        </div>
        <div class="service-message-input">
          <div class="message-input">
            <el-input
              v-model="inputMessage"
              resize="none"
              type="textarea"
              :rows="4"
            />
          </div>
          <div class="message-btn">
            <el-button
              size="medium"
              :disabled="sendMessageLoading"
              circle
              icon="el-icon-picture"
              @click="imageBox = true"
            />
          </div>
          <el-button
            class="message-send"
            size="medium"
            type="primary"
            :disabled="sendMessageLoading"
            @click="sendMessage"
          >
            发送
          </el-button>
        </div>
      </div>
      <el-dialog width="400px" title="发送图片" :visible.sync="imageBox">
        <single-upload-image v-model="inputImage" />
        <div slot="footer" class="dialog-footer">
          <el-button :disabled="sendMessageLoading" @click="imageBox = false">
            取消
          </el-button>
          <el-button
            type="primary"
            :disabled="sendMessageLoading"
            @click="sendImage"
          >
            发送
          </el-button>
        </div>
      </el-dialog>
    </el-main>
  </el-container>
</template>

<script>
import SingleUploadImage from '../../components/SingleUploadImage'

export default {
  components: { SingleUploadImage },
  data() {
    return {
      clientSearch: '',
      inputMessage: '',
      activeClient: null,
      imageBox: false,
      inputImage: '',
      clientList: [],
      messageList: [],
      autoRefreshTime: null,
      refreshClientLoading: false,
      refreshMessageLoading: false,
      refreshUnreadLoading: false,
      sendMessageLoading: false,
      scrollListStatus: false
    }
  },
  computed: {
    clientShowList() {
      return this.clientList.filter(client => {
        return (
          client.username.indexOf(this.clientSearch) !== -1 ||
          client.nickname.indexOf(this.clientSearch) !== -1 ||
          client.lastMessage.indexOf(this.clientSearch) !== -1
        )
      })
    }
  },
  created() {
    this.refresh()
  },
  mounted() {
    this.autoRefreshTime = setInterval(this.refresh, 5000)
  },
  beforeDestroy() {
    clearInterval(this.autoRefreshTime)
  },
  methods: {
    selectClient(code) {
      if (this.activeClient === code) {
        this.activeClient = ''
        this.messageList = []
      } else {
        this.activeClient = code
        this.clearUnread()
        this.refreshMessage()
        this.scrollListStatus = true
      }
    },
    scrollList() {
      this.$refs.messageList.scrollTop = this.$refs.messageList.scrollHeight
      this.scrollListStatus = false
    },
    refresh() {
      if (
        this.refreshClientLoading === false &&
        this.refreshMessageLoading === false &&
        this.refreshUnreadLoading === false
      ) {
        this.refreshClient()
        this.activeClient !== null && this.refreshMessage()
        this.activeClient !== null && this.clearUnread()
      }
    },
    refreshClient() {
      this.refreshClientLoading = true
      this.$axios
        .get('/admin/customer-service/clients')
        .then(response => {
          this.refreshClientLoading = false
          this.clientList = response.data
        })
        .catch(error => {
          this.refreshClientLoading = false
          this.$message.error(error.message)
        })
    },
    refreshMessage() {
      this.refreshMessageLoading = true
      this.$axios
        .get(
          '/admin/customer-service/clients/' + this.activeClient + '/messages'
        )
        .then(response => {
          this.refreshMessageLoading = false
          this.messageList = response.data
          this.scrollListStatus === true && setTimeout(this.scrollList, 500)
        })
        .catch(error => {
          this.refreshMessageLoading = false
          this.$message.error(error.message)
        })
    },
    clearUnread() {
      this.refreshUnreadLoading = true
      this.$axios
        .delete(
          '/admin/customer-service/clients/' + this.activeClient + '/unread'
        )
        .then(() => {
          this.refreshUnreadLoading = false
          this.refreshClient()
        })
        .catch(error => {
          this.refreshUnreadLoading = false
          this.$message.error(error.message)
        })
    },
    sendMessage() {
      if (this.inputMessage === '') {
        this.$message.warning('请输入内容再发送')
      } else if (this.activeClient === null) {
        this.$message.warning('请选择一个客户回复')
      } else {
        this.sendMessageLoading = true
        this.$axios
          .post(
            '/admin/customer-service/clients/' + this.activeClient + '/message',
            { content: this.inputMessage }
          )
          .then(() => {
            this.inputMessage = ''
            this.sendMessageLoading = false
            this.scrollListStatus = true
            this.refreshMessage()
          })
          .catch(error => {
            this.sendMessageLoading = false
            this.$message.error(error.message)
          })
      }
    },
    sendImage() {
      if (this.inputImage === '') {
        this.$message.warning('请上传图片后再发送')
      } else if (this.activeClient === null) {
        this.$message.warning('请选择一个客户回复')
      } else {
        this.$axios
          .post(
            '/admin/customer-service/clients/' + this.activeClient + '/image',
            { content: this.inputImage }
          )
          .then(() => {
            this.inputImage = ''
            this.imageBox = false
            this.scrollListStatus = true
            this.refreshMessage()
          })
          .catch(error => {
            this.$message.error(error.message)
          })
      }
    }
  }
}
</script>

<style scoped>
.service-message-box {
  margin: 0 auto;
  border: 1px solid rgb(235, 238, 245);
  border-radius: 5px;
  overflow: hidden;
  height: 750px;
  min-width: 1100px;
}

.service-client-list {
  float: left;
  width: 320px;
  height: 750px;
  border-right: 1px solid rgb(235, 238, 245);
  box-sizing: border-box;
}

.service-client-search {
  background: #ffffff;
  padding: 10px;
  border-bottom: 1px solid rgb(235, 238, 245);
}

.service-client-box {
  overflow: auto;
  height: 700px;
}

.service-client-line {
  overflow: hidden;
  box-sizing: border-box;
  padding: 10px;
  font-size: 14px;
  cursor: pointer;
}

.service-client-line.active {
  background: #f3f3f3;
}

.service-client-line + .service-client-line {
  border-top: 1px solid rgb(235, 238, 245);
}

.service-client-line:last-child {
  border-bottom: 1px solid rgb(235, 238, 245);
}

.service-client-avatar {
  float: left;
  height: 50px;
  width: 50px;
  box-sizing: border-box;
  padding-right: 10px;
  text-align: left;
}

.service-client-avatar .el-badge__content.is-fixed {
  top: 3px;
  right: 17px;
}

.service-client-avatar img {
  width: 40px;
  height: 40px;
}

.service-client-name {
  float: left;
  height: 20px;
  line-height: 20px;
  width: 150px;
  overflow: hidden;
}

.service-client-last-time {
  float: right;
  height: 20px;
  line-height: 20px;
  width: 80px;
  text-align: right;
}

.service-client-last-message {
  float: left;
  height: 20px;
  line-height: 20px;
  width: 240px;
  overflow: hidden;
}

.service-message-list {
  width: calc(100% - 320px);
  height: 590px;
  float: left;
  box-sizing: border-box;
  border-bottom: 1px solid rgb(235, 238, 245);
  overflow: auto;
  padding: 20px;
  background: #f3f3f3;
}

.message-line {
  overflow: hidden;
  margin-bottom: 20px;
}

.message-line.client div {
  float: left;
}

.message-line.service div {
  float: right;
}

.message-avatar {
  width: 40px;
  height: 40px;
}

.message-avatar img {
  max-width: 40px;
  max-height: 40px;
  text-align: center;
  display: inline-block;
  vertical-align: middle;
}

.message-content {
  line-height: 30px;
  background: #ffffff;
  border-radius: 5px;
  margin: 0 20px;
  padding: 5px 10px;
  font-size: 14px;
  max-width: 60%;
  position: relative;
}

.message-content:before {
  display: none;
  content: '';
  position: absolute;
  left: -9px;
  top: 18px;
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 5px 8.7px 5px 0;
  border-color: transparent #ffffff transparent transparent;
}

.message-content:after {
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 5px 0 5px 8.7px;
  border-color: transparent transparent transparent #aee26f;
  display: none;
  content: '';
  position: absolute;
  right: -9px;
  top: 18px;
}

.message-content img {
  max-width: calc(100% - 20px);
  margin: 20px 10px 10px;
}

.message-line.client .message-content {
  background: #ffffff;
  text-align: left;
}

.message-line.client .message-content:before {
  display: block;
}

.message-line.service .message-content {
  background: #b1e36f;
  text-align: left;
}

.message-line.service .message-content:after {
  display: block;
}

.service-message-input {
  width: calc(100% - 320px);
  height: 160px;
  float: left;
  box-sizing: border-box;
  overflow: hidden;
  padding: 10px 20px 0;
}

.message-input {
  margin-bottom: 10px;
}

.message-btn {
  float: left;
}

.message-send {
  float: right;
}
</style>
