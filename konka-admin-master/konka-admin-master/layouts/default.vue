<template>
  <div class="layout">
    <el-container class="main-container">
      <el-header style="background: #007cd5;">
        <img src="../assets/logo.png" class="shop-logo" alt="" />
        <div class="menu-collapse" @click="switchMenuCollapse">
          <img v-if="!menuCollapse" src="../assets/menu-off.svg" alt="" />
          <img v-if="menuCollapse" src="../assets/menu-on.svg" alt="" />
        </div>
        <div class="shop-user">
          <div class="shop-user-name">
            <span>你好，{{ name }}</span>
          </div>
          <button class="shop-user-btn" @click="showChangePassword">
            <img src="../assets/change-password.svg" alt="" />
          </button>
          <button class="shop-user-btn" @click="askConfirmLogout">
            <img src="../assets/logout.svg" alt="" />
          </button>
        </div>
      </el-header>
      <el-container>
        <el-aside class="main-aside" width="">
          <el-menu
            :router="true"
            :unique-opened="true"
            :default-active="activeMainMenu"
            background-color="#545c64"
            text-color="#fff"
            active-text-color="#ffd04b"
            :collapse-transition="false"
            :collapse="menuCollapse"
          >
            <el-menu-item index="/">
              <i class="el-icon-menu">&emsp;</i>
              <span slot="title">控制台</span>
            </el-menu-item>
            <el-submenu
              v-for="(menu, index) in mainMenuList"
              :key="index"
              :index="menu.name"
            >
              <template slot="title">
                <i :class="menu.icon">&emsp;</i>
                <span slot="title">{{ menu.name }}</span>
              </template>
              <el-menu-item
                v-for="(subMenu, subIndex) in menu.subMenuList"
                :key="subIndex"
                :index="subMenu.link"
              >
                {{ subMenu.name }}
              </el-menu-item>
            </el-submenu>
          </el-menu>
        </el-aside>
        <el-main>
          <el-card>
            <nuxt />
          </el-card>
          <el-dialog
            title="修改密码"
            :modal-append-to-body="false"
            width="400px"
            :close-on-click-modal="false"
            :visible.sync="changePasswordVisible"
          >
            <el-form
              ref="changePasswordForm"
              :model="changePasswordInfo"
              :rules="changePasswordRules"
            >
              <el-form-item label="请输入旧密码" prop="oldPassword">
                <el-input
                  v-model="changePasswordInfo.oldPassword"
                  type="password"
                />
              </el-form-item>
              <el-form-item label="请输入新密码" prop="newPassword">
                <el-input
                  v-model="changePasswordInfo.newPassword"
                  type="password"
                />
              </el-form-item>
              <el-form-item label="请确认新密码" prop="confirmPassword">
                <el-input
                  v-model="changePasswordInfo.confirmPassword"
                  type="password"
                />
              </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
              <el-button @click="changePasswordVisible = false">
                取 消
              </el-button>
              <el-button type="primary" @click="validateChangePassword">
                确 定
              </el-button>
            </div>
          </el-dialog>
        </el-main>
      </el-container>
    </el-container>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data() {
    return {
      name: this.$auth.user.nickname,
      activeMainMenu: this.$route.path,
      menuCollapse: false,
      changePasswordVisible: false,
      changePasswordInfo: {
        oldPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      changePasswordRules: {
        oldPassword: [
          { required: true, message: '旧密码不能为空', trigger: 'blur' }
        ],
        newPassword: [
          { required: true, message: '新密码不能为空', trigger: 'blur' }
        ],
        confirmPassword: [
          { required: true, message: '确认密码不能为空', trigger: 'blur' },
          {
            validator: function(rule, value, callback) {
              if (value !== this.changePasswordInfo.newPassword) {
                callback(new Error('两次输入密码不一致!'))
              } else {
                callback()
              }
            }.bind(this),
            trigger: 'blur'
          }
        ]
      }
    }
  },
  computed: {
    ...mapGetters(['mainMenuList'])
  },
  methods: {
    showChangePassword() {
      this.$data.changePasswordInfo = {
        oldPassword: '',
        newPassword: '',
        confirmPassword: ''
      }
      this.$data.changePasswordVisible = true
    },
    validateChangePassword() {
      this.$refs.changePasswordForm.validate(valid => {
        valid === true && this.changePassword()
      })
    },
    switchMenuCollapse() {
      this.menuCollapse = !this.menuCollapse
    },
    changePassword() {
      this.$axios
        .put('/admin/change-password', this.changePasswordInfo)
        .then(response => {
          this.$message({
            message: response.message,
            type: 'success'
          })
          this.logout()
        })
        .catch(error => {
          this.$message({
            message: error.message,
            type: 'error'
          })
        })
    },
    askConfirmLogout() {
      this.$confirm('确认退出系统?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(this.logout)
    },
    logout() {
      this.$auth.logout().then(() => {
        this.$auth.redirect('login')
      })
    }
  }
}
</script>

<style>
.layout {
  position: absolute;
  width: 100%;
  height: 100%;
  display: flex;
}

.main-container {
  min-width: 1440px;
  background: #f9f9f9;
}

.header-menu {
  float: right;
  margin: 16px 0;
  display: none;
}

.main-aside {
  position: relative;
  overflow-y: auto;
}

.shop-logo {
  width: 128px;
  height: 17px;
  margin: 20px 8px 0;
  float: left;
  filter: brightness(100);
}

.el-menu {
  min-height: 100%;
}

.el-menu:not(.el-menu--collapse) {
  width: 200px;
}

.menu-collapse {
  float: left;
  margin: 15px 20px;
  cursor: pointer;
}

.menu-collapse img {
  width: 30px;
}

.shop-user {
  float: right;
  overflow: hidden;
  margin: 10px;
  height: 40px;
}

.shop-user-name {
  float: left;
  font-size: 16px;
  color: #ffffff;
  line-height: 38px;
  height: 40px;
}

.shop-user-name:before {
  content: '';
  display: inline-block;
  vertical-align: middle;
  width: 40px;
  height: 40px;
  line-height: 40px;
  background: url('../assets/avatar.png') no-repeat;
  background-size: 100%;
  margin-right: 8px;
}

.shop-user-name span {
  display: inline-block;
  vertical-align: middle;
  height: 40px;
}

.shop-user button:before {
  content: '';
  border-left: 1px solid #ffffff;
  display: inline-block;
  vertical-align: middle;
  height: 30px;
  width: 1px;
  margin-right: 20px;
}

.shop-user-btn {
  display: inline-block;
  vertical-align: middle;
  background: none;
  border: none;
  color: #ffffff;
  height: 40px;
  line-height: 40px;
  padding: 0;
  margin-left: 20px;
  outline: none;
}

.shop-user-btn img {
  width: 30px;
  display: inline-block;
  vertical-align: middle;
  cursor: pointer;
}

.header-btn-box {
  float: right;
}

.el-select {
  width: 100%;
}

.el-pagination {
  text-align: center;
  margin-top: 20px;
}

.el-transfer {
  clear: both;
}

.el-date-editor.el-input {
  width: 100%;
}
</style>
