<template>
  <div class="badge-box">
    <span v-if="isShowLeft" class="badge-number left">{{ value }}</span>
    <span class="badge-content">
      <slot>&emsp;</slot>
    </span>
    <span v-if="isShowRight" class="badge-number right">{{ value }}</span>
  </div>
</template>

<script>
export default {
  name: 'Badge',
  props: {
    position: {
      type: String,
      default: 'right'
    },
    value: {
      type: Number,
      required: true
    },
    autoHidden: {
      type: Boolean,
      default: true
    },
    hiddenValue: {
      type: Number,
      default: 0
    }
  },
  computed: {
    isShowRight() {
      return this.position === 'right' ? this.isShowHidden : false
    },
    isShowLeft() {
      return this.position === 'left' ? this.isShowHidden : false
    },
    isShowHidden() {
      return this.autoHidden === false ? true : this.value !== this.hiddenValue
    }
  }
}
</script>

<style scoped>
.badge-box {
  display: inline-block;
  vertical-align: middle;
  position: relative;
  font-size: 0;
}
.badge-content {
  display: inline-block;
  vertical-align: middle;
  font-size: 14px;
}
.badge-number {
  display: inline-block;
  vertical-align: middle;
  background-color: #f56c6c;
  border-radius: 10px;
  color: #fff;
  font-size: 12px;
  height: 18px;
  line-height: 18px;
  padding: 0 6px;
  text-align: center;
  white-space: nowrap;
  border: 1px solid #fff;
}

.badge-number.right {
  margin-left: 3px;
}

.badge-number.left {
  margin-right: 3px;
}
</style>
