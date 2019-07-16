<template>
  <div class="login-box">
    <img class="login-logo" src="../assets/logo.png" alt="Logo" />
    <el-card v-loading="loading" class="box-card">
      <div slot="header" class="clearfix">
        <span>欢迎使用康佳优品管理系统</span>
      </div>
      <el-alert
        v-if="errorTips === ''"
        title="请输入账号密码登录系统"
        show-icon
        :closable="false"
        type="info"
        center
        class="login-tips"
      />
      <el-alert
        v-if="errorTips !== ''"
        :title="errorTips"
        :closable="false"
        type="error"
        center
        show-icon
        class="login-tips"
      />
      <el-form :model="loginInfo">
        <el-form-item>
          <el-input
            ref="username"
            v-model="loginInfo.username"
            placeholder="请输入用户名"
            @keyup.enter.native="focusPassword"
          />
        </el-form-item>
        <el-form-item>
          <el-input
            ref="password"
            v-model="loginInfo.password"
            type="password"
            placeholder="请输入密码"
            @keyup.enter.native="login"
          />
        </el-form-item>
        <el-form-item>
          <el-button
            type="primary"
            class="login-btn"
            :disabled="!isCanLogin"
            @click="login"
          >
            登陆
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
export default {
  auth: false,
  layout: 'login',
  name: 'Login',
  data() {
    return {
      loading: false,
      errorTips: '',
      loginInfo: {
        username: '',
        password: ''
      }
    }
  },
  computed: {
    isCanLogin() {
      return this.loginInfo.username !== '' && this.loginInfo.password !== ''
    }
  },
  mounted() {
    this.focusUsername()
  },
  methods: {
    login() {
      this.isCanLogin === true &&
        this.$auth
          .loginWith('local', {
            data: {
              username: this.loginInfo.username,
              password: this.loginInfo.password
            }
          })
          .then(() => {
            this.$auth.redirect('home')
          })
          .catch(error => {
            this.errorTips = error.message
          })
    },
    focusUsername() {
      this.$refs.username.focus()
    },
    focusPassword() {
      this.$refs.password.focus()
    }
  }
}
</script>

<style scoped>
.login-tips {
  margin-bottom: 20px;
}

.login-logo {
  width: 256px;
  height: 34px;
  position: fixed;
  left: calc(50% - 128px);
  top: calc(50% - 250px);
}

.box-card {
  position: fixed;
  width: 300px;
  left: calc(50% - 150px);
  top: calc(50% - 180px);
  z-index: 5;
}

.login-btn {
  width: 100%;
}
</style>
