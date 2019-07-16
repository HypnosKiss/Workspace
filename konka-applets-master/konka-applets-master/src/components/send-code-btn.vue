<template>
    <button :disabled="!isCanSendCode"
            @click="sendSmsCode">{{ loginCodeBtnText }}
    </button>
</template>

<script>
  export default {
    name: 'send-code-btn',
    props: {
      isCanSendCode: Boolean,
      smsId: String
    },
    data () {
      return {
        codeTime: 0,
        codeTimer: null
      }
    },
    computed: {
      loginCodeBtnText () {
        return this.codeTime === 0 ? (this.smsId === '' ? '验证码' : '重试') : '重试(' + this.codeTime + ')'
      }
    },
    methods: {
      changeCodeTime () {
        this.codeTime - 1 === 0 && this.$emit('time-reset')
        this.codeTime = this.codeTime > 0 ? this.codeTime - 1 : this.codeTime
      },
      sendSmsCode () {
        this.codeTime = 59
        this.$emit('send-code')
      }
    },
    created () {
      this.codeTimer = setInterval(this.changeCodeTime, 1000)
    }
  }
</script>
<style scoped>
    button {
        width: 120px;
        height: 60px;
        background-color: #f56271;
        border-radius: 6px;
        color: #ffffff;
        font-size: 26px;
        border: 0;
        padding: 0;
        line-height: 60px;
    }
    button[disabled]{
        background-color: #e5e5e5;
        color: #ffffff;
    }
</style>
